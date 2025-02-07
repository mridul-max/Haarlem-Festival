// Author: Konrad
// If page is not in iframe, redirect to '/manage'.
if (window.frameElement == null) {
    window.location.href = '/manageJazz';
    throw new Error('Unauthorized access');
}


import { ImagePicker } from "./image_picker.js";
import { MsgBox } from "./modals.js";

let editedArtistId = -1;
const artists = document.getElementById('artists');
const masterEditor = document.getElementById('master-editor');

// Artist fields.
const name = document.getElementById('name');
const description = document.getElementById('description');
const country = document.getElementById('country');
const genres = document.getElementById('genres');
const facebook = document.getElementById('facebook');
const instagram = document.getElementById('instagram');
const twitter = document.getElementById('twitter');
const spotify = document.getElementById('spotify');
const albums = document.getElementById('albums');
const kind = document.getElementById('kind');

const btnSubmit = document.getElementById('submit');
let isInCreationMode = false;

const btnOpen = document.getElementById('open');

const msgBox = new MsgBox();
const imgPicker = new ImagePicker();


// If window frame has a data-locations attribute, then we lock the location type.
let bindedKind = -1;
if (window.frameElement != null && window.frameElement.getAttribute('data-kind') != undefined) {
    kind.disabled = true;
    bindedKind = window.frameElement.getAttribute('data-kind');
}

function updateExistingArtist(id, data) {
    fetch('/api/artists/' + id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (!data.error_message) {
                loadArtistsList();
                msgBox.createToast('Success!', 'Artist has been updated');
            } else {
                msgBox.createToast('Somethin went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Somethin went wrong', error);
        });
}

function createNewArtist(data) {
    fetch('/api/artists', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (!data.error_message) {
                let option = createNewOptionItem(data);
                artists.appendChild(option);
                artists.selectedIndex = artists.length - 1;
                editedArtistId = data.id;
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';
                msgBox.createToast('Success!', 'Page has been created');

                // exit the new page mode
                isInCreationMode = false;
                btnSubmit.innerHTML = 'Save';

                btnOpen.onclick = function () {
                    setOnClick(data.id, data.kind.id);
                }
            } else {
                msgBox.createToast('Somethin went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Somethin went wrong', error);
        });
}

btnSubmit.onclick = function () {
    let pickedImageIds = imgPicker.getSelectedImages();

    let descriptionText = description.value;
    // Repalce all new lines with <br>
    descriptionText = descriptionText.replace(/\r?\n/g, '<br>');

    // to json
    let data = {
        name: name.value,
        description: descriptionText,
        images: pickedImageIds,
        country: country.value,
        genres: genres.value,
        facebook: facebook.value,
        instagram: instagram.value,
        twitter: twitter.value,
        spotify: spotify.value,
        recentAlbums: albums.value,
        kindId: kind.value
    };

    if (isInCreationMode) {
        createNewArtist(data);
    } else {
        updateExistingArtist(editedArtistId, data);
    }
}

document.getElementById('delete').onclick = function () {
    if (editedArtistId === -1) {
        msgBox.createToast('Error!', 'No page selected');
        return;
    }

    msgBox.createYesNoDialog('Delete page', 'Are you sure you want to delete this page? This is irreversible!', function () {
        // fetch with post
        fetch('/api/artists/' + editedArtistId, {
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
                    let options = artists.getElementsByTagName('option');
                    for (let option of options) {
                        if (option.value == editedArtistId) {
                            option.remove();
                            break;
                        }
                    }
                    toggleEditor(masterEditor, false);
                    msgBox.createToast('Success!', 'Page has been deleted');
                } else {
                    msgBox.createToast('Somethin went wrong', data.error_message);
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
        fetch('/api/artists/' + element.id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.error_message) {
                    console.log(data);
                    editedArtistId = data.id;
                    name.value = data.name;
                    description.value = data.description.replace(/<br>/g, '\r\n');
                    country.value = data.country;
                    genres.value = data.genres;
                    facebook.value = data.facebook;
                    instagram.value = data.instagram;
                    twitter.value = data.twitter;
                    spotify.value = data.spotify;
                    albums.value = data.recentAlbums;
                    kind.value = data.kind.id;

                    imgPicker.unselectAllImages();
                    // select images that are used by the page.
                    data.images.forEach(image => {
                        let checkboxes = document.getElementsByName('image');
                        imgPicker.selectImage(image.id);
                    });

                    //setOnClick(editedArtistId, data.kind.id);
                    btnOpen.onclick = function () {
                        setOnClick(editedArtistId, data.kind.id);
                    }
                } else {
                    msgBox.createToast('Somethin went wrong', data.error_message);
                }
            })
            .catch(error => {
                msgBox.createToast('Somethin went wrong', error);
            });
    }

    return option;
}

function setOnClick(id, kind) {
    let link = 'http://' + window.location.hostname;
    if (kind == 1) {
        link += '/festival/jazz/artist/' + id;
    } else if (kind == 2) {
        link += '/festival/dance/artist/' + id;
    }

    if (link == '') {
        link = "http://" + window.location.hostname;
    }
    window.open(link, '_blank');
}

// Load text pages from '/api/admin/text-pages'
function loadArtistsList() {
    let lastSelectedId = artists.value;

    artists.innerHTML = '';
    let toSelect = -1;

    // Add empty unselected option
    let option = document.createElement('option');
    option.innerHTML = 'Select Artist';
    option.value = -1;
    option.disabled = true;
    artists.appendChild(option);

    let url = '/api/artists?sort=name';

    if (bindedKind != -1) {
        url += '&kind=' + bindedKind;
    }

    // fetch with post
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            data.forEach(element => {
                let option = createNewOptionItem(element);

                // append option
                artists.appendChild(option);

                // if last selected
                // add a delay to make sure that the option is added to the list.
                if (lastSelectedId == element.id) {
                    toSelect = artists.options.length - 1;
                }
            });

            // select last selected
            if (toSelect != -1) {
                artists.selectedIndex = toSelect;
            }
        });
}

loadArtistsList();

imgPicker.loadImagePicker(images, () => { }, () => { });

function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        editedArtistId = -1;
        imgPicker.unselectAllImages();
        artists.selectedIndex = 0;
        name.value = '';
        description.value = '';
        country.value = '';
        genres.value = '';
        facebook.value = '';
        instagram.value = '';
        twitter.value = '';
        spotify.value = '';
        albums.value = '';
        kind.value = -1;
    }
}

document.getElementById('new-page').onclick = function () {
    isInCreationMode = true;
    toggleEditor(masterEditor, true);
    editedArtistId = -1;
    imgPicker.unselectAllImages();
    artists.selectedIndex = 0;
    name.value = '';
    description.value = '';
    country.value = '';
    genres.value = '';
    facebook.value = '';
    instagram.value = '';
    twitter.value = '';
    spotify.value = '';
    albums.value = '';
    kind.value = -1;
    btnSubmit.innerHTML = 'Create';

    kind.value = bindedKind;
}

// load artist kidns
fetch('/api/artists/kinds', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json())
    .then(data => {
        // Create first -- SELECT OPTION -- option
        let option = document.createElement('option');
        option.innerHTML = '-- Select Artist Kind --';
        option.value = -1;
        option.disabled = true;
        kind.appendChild(option);

        data.forEach(element => {
            let option = document.createElement('option');
            option.innerHTML = element.name;
            option.value = element.id;
            kind.appendChild(option);
        });

        kind.value = -1;
    }
    );

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