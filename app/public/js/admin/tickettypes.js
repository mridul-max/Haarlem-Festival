if (window.frameElement == null) {
    window.location.href = '/manageTicketTypes';
    throw new Error('Unauthorized access');
}

import { MsgBox } from "./modals.js";

let editedId = -1;
const locations = document.getElementById('locations');
const masterEditor = document.getElementById('master-editor');

// Artist fields.
const name = document.getElementById('name');
const price = document.getElementById('price');
const nrOfPeople = document.getElementById('nrOfPeople');

const btnSubmit = document.getElementById('submit');
let isInCreationMode = false;

const msgBox = new MsgBox();

let baseURL = '/api/tickettypes/';

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
                    if (option.value == editedId) {
                        // remove the option from the list
                        option.remove();
                        break;
                    }
                }

                // create new option
                let option = createNewOptionItem(data);
                locations.appendChild(option);
                locations.selectedIndex = locations.length - 1;
                editedId = data.id;
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';

                msgBox.createToast('Success!', 'Ticket Type has been updated');
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
                let option = createNewOptionItem(data);
                locations.appendChild(option);
                locations.selectedIndex = locations.length - 1;
                editedId = data.id;
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';
                msgBox.createToast('Success!', 'Ticket Type has been created');

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
    // to json
    let data = {
        id: 0,
        name: name.value,
        price: price.value,
        nrOfPeople: nrOfPeople.value,
    };

    if (isInCreationMode) {
        createNewEntry(data);
    } else {
        updateExistingEntry(editedId, data);
    }
}

document.getElementById('delete').onclick = function () {
    if (editedId === -1) {
        msgBox.createToast('Error!', 'No ticket type selected');
        return;
    }

    msgBox.createYesNoDialog('Delete ticket type', 'Are you sure you want to delete this event? This is irreversible!', function () {
        // fetch with post
        fetch(baseURL + "/" + editedId, {
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
                        if (option.value == editedId) {
                            option.remove();
                            break;
                        }
                    }
                    toggleEditor(masterEditor, false);
                    msgBox.createToast('Success!', 'Ticket type has been deleted');
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
    option.innerHTML = element.name;
    option.value = element.id;

    // on click
    option.onclick = function () {
        toggleEditor(masterEditor, true);
        btnSubmit.innerHTML = 'Save';
        isInCreationMode = false;
        // Do the api call to get the page content.
        fetch(baseURL + "/" + element.id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.error_message) {
                    editedId = data.id;

                    name.value = data.name;
                    price.value = data.price;
                    nrOfPeople.value = data.nrOfPeople;

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
    let head = '=== Select a ticket type ===';
    option.innerHTML = head;
    option.value = -1;
    option.disabled = true;
    locations.appendChild(option);

    // fetch with post
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
}

loadList();

function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        editedId = -1;

        name.value = '';
        price.value = '';
        nrOfPeople.value = '';
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