// Author: Konrad
if (window.frameElement == null) {
    window.location.href = '/manageDJs';
    throw new Error('Unauthorized access');
}

import { MsgBox } from "./modals.js";

let editedId = -1;
let editedEventId = -1;
const locations = document.getElementById('locations');
const masterEditor = document.getElementById('master-editor');
const btnOpen = document.getElementById('open');

const title = document.getElementById('title');
const artistCheckboxesContainer = document.getElementById('artist-checkboxes');
const locationSelect = document.getElementById('location');
const ticketType = document.getElementById('ticketType');
const startTime = document.getElementById('startTime');
const endTime = document.getElementById('endTime');

const btnSubmit = document.getElementById('submit');
let isInCreationMode = false;

// On startTime change finished, set endTime to startTime + 1 hour.
// Because we are lazy, woooo!
startTime.onchange = function () {
    let start = new Date(startTime.value);
    start.setHours(start.getHours() + 3);
    endTime.value = start.toISOString().slice(0, 16);
}

const msgBox = new MsgBox();

const maxNameLength = 15;
const maxLocationLength = 12;

let baseURL = '/api/events/dance';

function updateExistingEntry(id, json) {
    fetch(baseURL + "/" + id, {
        method: 'PUT',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
        body: json
    })
        .then(response => response.json())
        .then(data => {
            if (!data.error_message) {
                loadList();
                msgBox.createToast('Success!', 'Event has been updated');
            } else {
                msgBox.createToast('Something went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Something went wrong', error);
        });
}

function createNewEntry(json) {
    fetch(baseURL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
        body: json
    })
        .then(response => response.json())
        .then(data => {
            loadList();
        })
        .catch(error => {
            msgBox.createToast('Something went wrong', error);
        });
}

btnSubmit.onclick = function () {
    let start = new Date(startTime.value);
    start = start.getFullYear() + '-' + (start.getMonth() + 1) + '-' + start.getDate() + ' ' + start.getHours() + ':' + start.getMinutes() + ':' + start.getSeconds();

    let end = new Date(endTime.value);
    end = end.getFullYear() + '-' + (end.getMonth() + 1) + '-' + end.getDate() + ' ' + end.getHours() + ':' + end.getMinutes() + ':' + end.getSeconds();

    let artists = [];
    // get artsits from check boxes that are checked
    let checkboxes = artistCheckboxesContainer.getElementsByTagName('input');
    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            artists.push(checkbox.value);
        }
    }

    if (artists.length == 0) {
        msgBox.createToast('Error!', 'You need to select at least one artist');
        return;
    }

    // to json
    let data = {
        id: 0,
        event: {
            id: editedEventId,
            name: title.value,
            startTime: start,
            endTime: end,
            artistIds: artists,
            locationId: locationSelect.value,
            eventTypeId: 4
        },
        ticketTypeId: ticketType.value
    };

    // Make sure that all numbers are represented as numbers
    for (let key in data.event) {
        if (!isNaN(data.event[key])) {
            data.event[key] = parseInt(data.event[key]);
        }
    }

    let json = JSON.stringify(data);
    // Make sure that artistIds is always an array, even if only one artist is selected
    if (artists.length == 1) {
        json = json.replace('"artistIds":' + artists[0], '"artistIds":[' + artists[0] + ']');
    }

    // disable the editor.
    toggleEditor(masterEditor, false);

    if (isInCreationMode) {
        createNewEntry(json);
    } else {
        data.id = editedId;
        updateExistingEntry(editedEventId, json);
    }
}

document.getElementById('delete').onclick = function () {
    if (editedEventId === -1) {
        msgBox.createToast('Error!', 'No event selected');
        return;
    }

    msgBox.createYesNoDialog('Delete event', 'Are you sure you want to delete this event? This is irreversible!', function () {
        // fetch with post
        fetch(baseURL + "/" + editedEventId, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success_message) {
                    // remove the option from the list
                    let options = locations.getElementsByTagName('option');
                    for (let option of options) {
                        if (option.value == editedEventId) {
                            option.remove();
                            break;
                        }
                    }
                    toggleEditor(masterEditor, false);
                    msgBox.createToast('Success!', 'Event has been deleted');
                } else {
                    msgBox.createToast('Something went wrong', data.error_message);
                }
            })
    }, function () { });
}


document.getElementById('cancel').onclick = function () {
    toggleEditor(masterEditor, false);
}

function createNewOptionItem(element) {
    // create option
    let option = document.createElement('option');

    let name = element.event.name;
    let location = element.event.location.name;
    let dispStartTime = element.event.startTime.date;
    let dispEndTime = element.event.endTime.date;

    // make sure that name always is 15 chars long
    if (name.length > maxNameLength) {
        // cut off last 3 chars and add ...
        name = name.substring(0, maxNameLength - 3) + '...';
    } else {
        let spacesAdded = 0;
        let spacesToAdd = maxNameLength - name.length;
        while (spacesAdded < spacesToAdd) {
            name += '&nbsp;';
            spacesAdded++;
        }
    }

    // make sure that location always is 15 chars long
    if (location.length > maxLocationLength) {
        location = location.substring(0, maxLocationLength) + '...';
    } else {
        let spacesAdded = 0;
        let spacesToAdd = maxLocationLength - location.length;
        while (spacesAdded < spacesToAdd + 3) {
            location += '&nbsp;';
            spacesAdded++;
        }
    }

    // display startTime and endTime in a following pattern: dd/mm/yyyy hh:mm
    dispStartTime = dispStartTime.substring(8, 10) + '/' + dispStartTime.substring(5, 7) + '/' + dispStartTime.substring(0, 4) + ' ' + dispStartTime.substring(11, 16);
    dispEndTime = dispEndTime.substring(8, 10) + '/' + dispEndTime.substring(5, 7) + '/' + dispEndTime.substring(0, 4) + ' ' + dispEndTime.substring(11, 16);

    option.innerHTML = name + ' | ' + location + ' | ' + dispStartTime + ' | ' + dispEndTime;

    option.value = element.event.id;

    // on click
    option.onclick = function () {
        toggleEditor(masterEditor, true);
        btnSubmit.innerHTML = 'Save';
        isInCreationMode = false;
        // Do the api call to get the page content.
        fetch(baseURL + "/" + element.event.id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.error_message) {
                    editedId = data.id;
                    editedEventId = data.event.id;

                    title.value = data.event.name;

                    const artistCheckboxes = artistCheckboxesContainer.getElementsByClassName('artist-checkbox');
                    // Uncheck all first.
                    for (let checkbox of artistCheckboxes) {
                        checkbox.checked = false;
                    }

                    // Check the artists that are in the event
                    for (let artist of data.event.artists) {
                        for (let checkbox of artistCheckboxes) {
                            if (checkbox.value == artist.id) {
                                checkbox.checked = true;
                                break;
                            }
                        }
                    }

                    // select the location option corresponding to id in the selec
                    for (let option of locationSelect.getElementsByClassName('location-option')) {
                        if (option.value == data.event.location.id) {
                            option.selected = true;
                            break;
                        }
                    }


                    ticketType.value = data.ticketType.id;

                    let dateStart = new Date(data.event.startTime.date);
                    dateStart.setHours(dateStart.getHours() + 2);
                    dateStart = dateStart.toISOString().slice(0, 16);
                    startTime.value = dateStart;

                    let dateEnd = new Date(data.event.endTime.date);
                    dateEnd.setHours(dateEnd.getHours() + 2);
                    dateEnd = dateEnd.toISOString().slice(0, 16);
                    endTime.value = dateEnd;

                    setOpenButton(baseURL, data.event.id);

                } else {
                    msgBox.createToast('Something went wrong', data.event.error_message);
                }
            })
            .catch(error => {
                msgBox.createToast('Something went wrong', error);
                console.error('Error:', error);
            });
    }

    return option;
}

function setOpenButton(baseURI, eventId) {
    btnOpen.onclick = function () { window.open('/festival/dance/event/' + eventId, '_blank'); }
}

let isBasicStuffLoaded = false;

function loadList() {
    let lastSelectedId = locations.value;

    locations.innerHTML = '';
    let toSelect = -1;

    // Add empty unselected option
    let option = document.createElement('option');
    let head = 'Name' + ('&nbsp;').repeat(maxNameLength - 3) +
        '| Location' + ('&nbsp;').repeat(maxLocationLength - 5) +
        ' | START' + ('&nbsp;').repeat(12) + '| END';
    option.innerHTML = head;
    option.value = -1;
    option.disabled = true;
    locations.appendChild(option);

    let url = baseURL + '?sort=time';
    // fetch with post
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // check if data is array
            if (Array.isArray(data)) {
                data.forEach(element => {
                    let option = createNewOptionItem(element);

                    // append option
                    locations.appendChild(option);

                    // if last selected
                    // add a delay to make sure that the option is added to the list.
                    if (lastSelectedId == element.id) {
                        toSelect = locations.options.length - 1;
                    }
                });

                // select last selected
                if (toSelect != -1) {
                    locations.selectedIndex = toSelect;
                }
            }
        });

    if (isBasicStuffLoaded) {
        return
    }
    isBasicStuffLoaded = true;

    let uri = '/api/artists?sort=name&kind=2';

    fetch(uri, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // check if data is array
            if (Array.isArray(data)) {
                data.forEach(element => {
                    let divFormCheck = document.createElement('div');
                    divFormCheck.classList.add('form-check');

                    let input = document.createElement('input');
                    input.type = 'checkbox';
                    input.value = element.id;
                    input.id = 'artist-' + element.id;
                    input.classList.add('form-check-input');
                    input.classList.add('artist-checkbox');

                    let label = document.createElement('label');
                    label.classList.add('form-check-label');
                    label.innerHTML = element.name;
                    label.htmlFor = 'artist-' + element.id;

                    divFormCheck.appendChild(input);
                    divFormCheck.appendChild(label);

                    artistCheckboxesContainer.appendChild(divFormCheck);
                });
            }
        }
        );

    let locationURI = '/api/locations/type/4?sort=name';
    locationSelect.innerHTML = '';

    // Add the '-- select --' option
    let dummyLocation = document.createElement('option');
    dummyLocation.innerHTML = '-- select --';
    dummyLocation.value = -1;
    dummyLocation.disabled = true;
    locationSelect.appendChild(dummyLocation);

    // and now, load locations.
    location.innerHTML = '';
    fetch(locationURI, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // check if data is array
            if (Array.isArray(data)) {
                data.forEach(element => {
                    let option = document.createElement('option');
                    option.innerHTML = element.name;
                    option.value = element.id;
                    locationSelect.appendChild(option);
                    option.classList.add('location-option');
                });
            }
        }
        );
    location.selectedIndex = -1;

    ticketType.innerHTML = '';

    // Load ticket types.
    fetch('/api/tickettypes', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // check if data is array
            if (Array.isArray(data)) {
                data.forEach(element => {
                    let option = document.createElement('option');
                    option.innerHTML = element.name + ' - ' + element.price + '€';
                    option.value = element.id;
                    ticketType.appendChild(option);
                });
            }
        }
        );
    ticketType.selectedIndex = 0;
}

loadList();

function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        editedId = -1;
        locationSelect.selectedIndex = 0;
        ticketType.selectedIndex = 0;
        startTime.value = '';
        endTime.value = '';

        // Unselect all checkboxes
        let checkboxes = document.getElementsByClassName('form-check-input');
        for (let checkbox of checkboxes) {
            checkbox.checked = false;
        }

        if (locations.dataset.locations != undefined) {
            locationType.value = locations.dataset.locations;
        }
    }
}

document.getElementById('new-page').onclick = function () {
    isInCreationMode = true;
    toggleEditor(masterEditor, false);
    toggleEditor(masterEditor, true);

    locations.selectedIndex = -1;
}

if (window.self != window.top) {
    let container = document.getElementsByClassName('container')[0];
    // 1em margin on left and right
    container.style.marginLeft = '1em';
    container.style.marginRight = '1em';

    container.style.padding = '0';
    container.style.width = '90%';
    // disable max-width
    container.style.maxWidth = 'none';
}