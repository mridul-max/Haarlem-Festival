// Author: Konrad
if (window.frameElement == null) {
    window.location.href = '/manageJazz';
    throw new Error('Unauthorized access');
}

import { MsgBox } from "./modals.js";

let editedId = -1;
let editedEventId = -1;
const locations = document.getElementById('locations');
const masterEditor = document.getElementById('master-editor');
const btnOpen = document.getElementById('open');

// Artist fields.
const artist = document.getElementById('artist');
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
const maxLocationLength = 15;

let baseURL = '/api/events/jazz';

function updateExistingEntry(id, data) {
    fetch(baseURL + "/" + id, {
        method: 'PUT',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
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

function createNewEntry(data) {
    fetch(baseURL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
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

    // to json
    let data = {
        id: 0,
        event: {
            id: editedEventId,
            name: artist.options[artist.selectedIndex].text,
            startTime: start,
            endTime: end,
            artistId: artist.value,
            locationId: locationSelect.value,
            eventTypeId: 1
        },
        ticketTypeId: ticketType.value
    };

    // disable the editor.
    toggleEditor(masterEditor, false);

    if (isInCreationMode) {
        createNewEntry(data);
    } else {
        data.id = editedId;
        updateExistingEntry(editedEventId, data);
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

                    // select the artist option corresponding to id in the select
                    let options = artist.getElementsByTagName('option');
                    for (let option of options) {
                        if (option.value == data.event.artist.id) {
                            option.selected = true;
                            break;
                        }
                    }

                    // select the location option corresponding to id in the selec
                    options = locationSelect.getElementsByTagName('option');
                    for (let option of options) {
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
    btnOpen.onclick = function () { window.open('/festival/jazz/event/' + eventId, '_blank'); }
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

    // load artist list to artist select
    artist.innerHTML = '';
    let jazzSelectOption = document.createElement('option');
    jazzSelectOption.innerHTML = '-- Select Artist -- ';
    jazzSelectOption.value = -1;
    jazzSelectOption.disabled = true;
    artist.appendChild(jazzSelectOption);

    let uri = '/api/artists?sort=name&kind=1';

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
                    let option = document.createElement('option');
                    option.innerHTML = element.name;
                    option.value = element.id;
                    artist.appendChild(option);
                });
            }
        }
        );
    artist.selectedIndex = 0;

    let locationURI = '/api/locations/type/';
    if (baseURL.endsWith('dance')) {
        locationURI += '4';
    } else {
        locationURI += '1';
    }

    locationURI += '?sort=name';

    locationSelect.innerHTML = '';

    // and now, load jazz locations.
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
        artist.selectedIndex = 0;
        locationSelect.selectedIndex = 0;
        ticketType.selectedIndex = 0;
        startTime.value = '';
        endTime.value = '';

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