if (window.frameElement == null) {
    window.location.href = '/manageTicketTypes';
    throw new Error('Unauthorized access');
}

import { MsgBox } from "./modals.js";

let editedEventId = -1;
const locations = document.getElementById('locations');
const masterEditor = document.getElementById('master-editor');

// Artist fields.
const name = document.getElementById('name');
const date = document.getElementById('date');
const festivalEventType = document.getElementById('festival-event-type');
const ticketType = document.getElementById('ticket-type');

const btnSubmit = document.getElementById('submit');
let isInCreationMode = false;

const msgBox = new MsgBox();

let baseURL = '/api/events/passes';

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

                // update the option in the list
                let options = locations.getElementsByTagName('option');
                for (let option of options) {
                    if (option.value == editedEventId) {
                        // remove the option from the list
                        option.remove();
                        break;
                    }
                }

                // remove the option from the list
                locations.removeChild(options[locations.selectedIndex]);

                // create new option
                locations.appendChild(createNewOptionItem(data));
                locations.selectedIndex = locations.length - 1;
                editedEventId = data.event.id;
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';

                msgBox.createToast('Success!', 'Pass has been updated');
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
            if (!data.error_message) {
                locations.appendChild(createNewOptionItem(data));
                locations.selectedIndex = locations.length - 1;
                editedEventId = data.event.id;

                msgBox.createToast('Success!', 'Pass has been created');

                // exit the new page mode
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';
            } else {
                msgBox.createToast('Something went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Something went wrong', error);
        });
}

btnSubmit.onclick = function () {
    let d = new Date(date.value);
    d = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

    // to json
    let data = {
        id: 0,
        event: {
            id: editedEventId,
            name: name.value,
            startTime: d,
            endTime: d,
            eventType: {
                id: festivalEventType.value,
            }
        },
        ticketType: {
            id: ticketType.value,
        }
    };


    if (isInCreationMode) {
        createNewEntry(data);
    } else {
        updateExistingEntry(editedEventId, data);
    }
}

document.getElementById('delete').onclick = function () {
    if (editedEventId === -1) {
        msgBox.createToast('Error!', 'No page selected');
        return;
    }

    msgBox.createYesNoDialog('Delete page', 'Are you sure you want to delete this event? This is irreversible!', function () {
        // fetch with post
        fetch('/api/events/' + editedEventId, {
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
                    msgBox.createToast('Success!', 'Page has been deleted');
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
    option.innerHTML = element.event.name;
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
                    editedEventId = data.event.id;

                    name.value = data.event.name;
                    let dispTime = element.event.startTime.date;
                    // convert string to date (yyyy/mm/dd hh:mm:ss)
                    let ffs = dispTime.split(/[- :]/);
                    dispTime = new Date(ffs[0], ffs[1] - 1, ffs[2], ffs[3], ffs[4], ffs[5]);
                    date.valueAsDate = dispTime;

                    if (data.event.eventType) {
                        festivalEventType.value = data.event.eventType.id;
                    } else {
                        festivalEventType.value = 0;
                    }

                    ticketType.value = data.ticketType.id;

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

// Load text pages from '/api/admin/text-pages'
function loadList() {
    let lastSelectedId = locations.value;

    locations.innerHTML = '';
    let toSelect = -1;

    // Add empty unselected option
    let option = document.createElement('option');
    let head = '=== Select a pass ===';
    option.innerHTML = head;
    option.value = -1;
    option.disabled = true;
    locations.appendChild(option);

    // fetch with post
    fetch(baseURL, {
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

    fetch('/api/eventtypes', {
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

                    // append option
                    festivalEventType.appendChild(option);
                });
            }
        });

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
                    option.innerHTML = element.name;
                    option.value = element.id;

                    // append option
                    ticketType.appendChild(option);
                });
            }
        });
}

loadList();

function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        editedEventId = -1;

        name.value = '';
        date.value = '';
        festivalEventType.selectedIndex = -1;
        ticketType.selectedIndex = -1;
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