// Author: Konrad
if (window.frameElement == null) {
    window.location.href = '/manageTextPages';
    throw new Error('Unauthorized access');
}

import { ImagePicker } from "./image_picker.js";
import { MsgBox } from "./modals.js";

let editedPageId = -1;
const title = document.getElementById('title');
const pageHref = document.getElementById('page-href');
const textPagesList = document.getElementById('text-pages-list');
const masterEditor = document.getElementById('master-editor');

const btnSubmit = document.getElementById('submit');
let isInNewPageMode = false;

const btnOpen = document.getElementById('open');

const msgBox = new MsgBox();
const imgPicker = new ImagePicker();

tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | customSeeSourceCode',
    menu: {
        custom: {
            title: 'Modules',
            items: 'customAddButtonButton customInsertCalendar customInsertCountdown customInsertImageButton customInsertMap customInsertNavTile customIframe | customJazzOptions customStrollOptions customYummyOptions customDanceOptions '
        }
    },
    menubar: 'file edit view insert format tools table custom',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    table_default_attributes: {
        border: "0"
    },
    content_css: [
        "https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css",
        "/css/main.css",
        "/css/editor.css"
    ],
    setup: (editor) => {
        try {
            editor.ui.registry.addMenuItem('customAddButtonButton', {
                text: 'Button',
                onAction: () => {
                    msgBox.createDialogWithInputs('Add Button', [
                        {
                            label: 'Button Text',
                            id: 'btn-text'
                        },
                        {
                            label: 'Button Link',
                            id: 'btn-link'
                        }],
                        function () {
                            let btnText = document.getElementById('btn-text').value;
                            let btnLink = document.getElementById('btn-link').value;
                            editor.insertContent(`<a href="${btnLink}"><button class="btn btn-primary" href="#">${btnText}</button></a>`);
                        },
                        function () { });
                }
            });
            editor.ui.registry.addMenuItem('customInsertImageButton', {
                text: 'Image',
                onAction: () => {
                    let rndInt = Math.floor(Math.random() * 100000000);
                    msgBox.createDialogWithInputs('Insert Image From Library', [
                        {
                            label: 'Image Picker',
                            id: 'image-picker-' + rndInt,
                            type: 'image-picker'
                        },
                        {
                            label: 'Image Width',
                            id: 'image-width',
                        },
                        {
                            label: 'Image Height',
                            id: 'image-height',
                        }],
                        function () {
                            let imagePicker = document.getElementById('image-picker-' + rndInt);
                            let selectedImage;
                            for (let label of imagePicker.getElementsByClassName('tile-picker')) {
                                let input = label.getElementsByTagName('input')[0];
                                if (input.checked) {
                                    selectedImage = label.getElementsByTagName('img')[0].src;
                                }
                            }

                            // remvoe the first part of the url before the /img
                            selectedImage = selectedImage.replace(/.*\/img/, '/img');

                            let imageWidth = document.getElementById('image-width').value;
                            let imageHeight = document.getElementById('image-height').value;

                            // if widtth and height are empty, set to auto
                            if (imageWidth == '') {
                                imageWidth = 'auto';
                            }
                            if (imageHeight == '') {
                                imageHeight = 'auto';
                            }

                            editor.insertContent(`<img src="${selectedImage}" width="${imageWidth}" height="${imageHeight}">`);
                        },
                        function () { });
                }
            });
            editor.ui.registry.addMenuItem('customInsertNavTile', {
                text: 'Nav Tile',
                onAction: () => {
                    let rndInt = Math.floor(Math.random() * 100000000);
                    msgBox.createDialogWithInputs('Create Nav Tile', [
                        {
                            label: 'Image Picker',
                            id: 'image-picker-' + rndInt,
                            type: 'image-picker'
                        },
                        {
                            label: 'Tile Text',
                            id: 'tile-text'
                        },
                        {
                            label: 'Tile Link',
                            id: 'tile-link'
                        },
                        {
                            label: 'Description',
                            id: 'tile-description'
                        }
                    ],
                        () => {
                            let imagePicker = document.getElementById('image-picker-' + rndInt);
                            let selectedImage;
                            for (let label of imagePicker.getElementsByClassName('tile-picker')) {
                                let input = label.getElementsByTagName('input')[0];
                                if (input.checked) {
                                    selectedImage = label.getElementsByTagName('img')[0].src;
                                }
                            }

                            // remvoe the first part of the url before the /img
                            selectedImage = selectedImage.replace(/.*\/img/, '/img');

                            let tileText = document.getElementById('tile-text').value;
                            let tileLink = document.getElementById('tile-link').value;
                            let tileDescription = document.getElementById('tile-description').value;

                            // Now we build the tile.
                            let a = document.createElement('a');
                            a.href = tileLink;
                            let div = document.createElement('div');
                            div.classList.add('card', 'img-fluid', 'nav-tile');
                            let divCarousel = document.createElement('div');
                            divCarousel.classList.add('carousel-caption');
                            let p = document.createElement('p');
                            p.innerText = tileText;
                            divCarousel.appendChild(p);
                            div.appendChild(divCarousel);

                            let img = document.createElement('img');
                            img.src = selectedImage;
                            img.classList.add('card-img-top');
                            div.appendChild(img);

                            let cardOverlay = document.createElement('div');
                            cardOverlay.classList.add('card-img-overlay');
                            let pOverlay = document.createElement('p');
                            pOverlay.classList.add('card-text', 'w-65', 'inline-block');
                            pOverlay.innerText = tileDescription;
                            cardOverlay.appendChild(pOverlay);
                            let btn = document.createElement('button');
                            btn.classList.add('btn', 'btn-primary', 'float-end');
                            btn.innerText = 'Learn More';
                            cardOverlay.appendChild(btn);
                            div.appendChild(cardOverlay);

                            a.appendChild(div);

                            let container = document.createElement('div');
                            container.appendChild(a);

                            console.log(a.outerHTML);

                            editor.insertContent(container.outerHTML);
                        });
                }
            });
            editor.ui.registry.addMenuItem('customInsertMap', {
                text: 'Map',
                onAction: () => {
                    editor.insertContent("<div id='mapContainer' class='row' data-mapkind='general'></div>");
                }
            });
            editor.ui.registry.addMenuItem('customInsertCalendar', {
                text: 'Calendar',
                onAction: () => {
                    msgBox.createDialogWithInputs('Create Calendar', [
                        {
                            label: 'Calendar Type (all-events/stroll)',
                            id: 'calendar-type',
                        }]
                        , () => {
                            let calendarType = document.getElementById('calendar-type').value;
                            if (calendarType == '') {
                                calendarType = 'all-events';
                            }
                            editor.insertContent(`<div id='calendar' class='row' data-calendar-type='${calendarType}'></div>`);
                        });
                }
            });
            editor.ui.registry.addMenuItem('customInsertCountdown', {
                text: 'Countdown',
                onAction: () => {
                    editor.insertContent("<p id='countdown'>00:00:00:00<br>days hours minutes seconds</p>");
                }
            });
            editor.ui.registry.addMenuItem('customJazzOptions', {
                text: 'Jazz Modules >',
                type: 'nestedmenuitem',
                getSubmenuItems: () => {
                    return [
                        {
                            text: 'All Day Pass',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='allday-pass' data-kind='jazz'></div>`);
                            }
                        },
                        {
                            text: 'Events Viewer',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='events' data-type='jazz'></div>`);
                            }
                        }
                    ];
                }
            });
            editor.ui.registry.addMenuItem('customStrollOptions', {
                text: 'Stroll Modules >',
                type: 'nestedmenuitem',
                getSubmenuItems: () => {
                    return [
                        {
                            text: 'Events Viewer',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='events' data-type='stroll'></div>`);
                            }
                        }
                    ];
                }
            });
            editor.ui.registry.addMenuItem('customYummyOptions', {
                text: 'Yummy Modules >',
                type: 'nestedmenuitem',
                getSubmenuItems: () => {
                    return [
                        {
                            text: 'Events Viewer',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='events' data-type='yummy'></div>`);
                            }
                        }
                    ];
                }
            });
            editor.ui.registry.addButton('customSeeSourceCode', {
                text: 'Source Code',
                onAction: () => {
                    msgBox.createDialogWithInputs('Source Code', [
                        {
                            label: 'Source Code',
                            id: 'source-code',
                            type: 'textarea',
                            content: tinyMCE.activeEditor.getContent()
                        }
                    ], () => {
                        tinyMCE.activeEditor.setContent(document.getElementById('source-code').value);
                    });
                }
            })
            editor.ui.registry.addMenuItem('customIframe', {
                text: 'iFrame',
                onAction: () => {
                    msgBox.createDialogWithInputs('Insert iFrame', [
                        {
                            label: 'Link',
                            id: 'link',
                        },
                        {
                            label: 'Width (auto if blank)',
                            id: 'width',
                        },
                        {
                            label: 'Height (auto if blank)',
                            id: 'height',
                        }]
                        , () => {
                            let link = document.getElementById('link').value;
                            let width = document.getElementById('width').value;
                            if (width == '') width = 'auto';
                            let height = document.getElementById('height').value;
                            if (height == '') height = 'auto';
                            let iframe = `<iframe src='${link}' width='${width}' height='${height}'></iframe>`;
                            editor.insertContent(iframe);
                        });
                }
            });
            editor.ui.registry.addMenuItem('customDanceOptions', {
                text: 'DANCE! Modules >',
                type: 'nestedmenuitem',
                getSubmenuItems: () => {
                    return [
                        {
                            text: 'All Day Pass',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='allday-pass' data-kind='dance'></div>`);
                            }
                        },
                        {
                            text: 'Events Viewer',
                            type: 'menuitem',
                            onAction: () => {
                                editor.insertContent(`<div id='events' data-type='dance'></div>`);
                            }
                        }
                    ];
                }
            });
        } catch (error) {
            console.error(error);
        }
    }
});

function updateExistingPage(id, data) {
    fetch('/api/textpages/' + id, {
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
                loadTextPagesList();
                msgBox.createToast('Success!', 'Page has been updated');
            } else {
                msgBox.createToast('Somethin went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Somethin went wrong: ', error);
            console.error(error);
        });
}

function createNewPage(data) {
    fetch('/api/textpages', {
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
                textPagesList.appendChild(option);
                textPagesList.selectedIndex = textPagesList.length - 1;
                editedPageId = data.id;
                isInNewPageMode = false;
                btnSubmit.innerHTML = 'Save';
                msgBox.createToast('Success!', 'Page has been created');

                // exit the new page mode
                isInNewPageMode = false;
                btnSubmit.innerHTML = 'Save';

                btnOpen.onclick = function () {
                    let link = data.href;
                    if (link == '') {
                        link = "http://" + window.location.hostname;
                    }
                    window.open(link, '_blank');
                };
            } else {
                msgBox.createToast('Somethin went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Somethin went wrong', error);
        });
}

btnSubmit.onclick = function () {
    let titleValue = title.value;
    let pickedImageIds = imgPicker.getSelectedImages();
    let content = tinymce.activeEditor.getContent();

    // to json
    let data = {
        title: titleValue,
        images: pickedImageIds,
        content: content,
        href: pageHref.value
    };

    if (isInNewPageMode) {
        createNewPage(data);
    } else {
        updateExistingPage(editedPageId, data);
    }
}

document.getElementById('delete').onclick = function () {
    if (editedPageId === -1) {
        msgBox.createToast('Error!', 'No page selected');
        return;
    }

    msgBox.createYesNoDialog('Delete page', 'Are you sure you want to delete this page? This is irreversible!', function () {
        // fetch with post
        fetch('/api/textpages/' + editedPageId, {
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
                    let options = textPagesList.getElementsByTagName('option');
                    for (let option of options) {
                        if (option.value == editedPageId) {
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
    option.innerHTML = element.title;
    option.value = element.id;

    // on click
    option.onclick = function () {
        toggleEditor(masterEditor, true);
        btnSubmit.innerHTML = 'Save';
        isInNewPageMode = false;
        // Do the api call to get the page content.
        fetch('/api/textpages/' + element.id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.error_message) {
                    tinymce.activeEditor.setContent(data.content);
                    editedPageId = data.id;
                    title.value = data.title;

                    pageHref.value = data.href;

                    imgPicker.unselectAllImages();
                    // select images that are used by the page.
                    data.images.forEach(image => {
                        imgPicker.selectImage(image.id);
                    });

                    btnOpen.onclick = function () {
                        let link = data.href;
                        if (link == '') {
                            link = "http://" + window.location.hostname;
                        }
                        window.open(link, '_blank');
                    };
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

// Load text pages from '/api/admin/text-pages'
function loadTextPagesList() {
    let lastSelectedId = textPagesList.value;

    textPagesList.innerHTML = '';
    let toSelect = -1;

    // Add empty unselected option
    let option = document.createElement('option');
    option.innerHTML = 'Select page';
    option.value = -1;
    option.disabled = true;
    textPagesList.appendChild(option);

    // fetch with post
    fetch('/api/textpages', {
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
                textPagesList.appendChild(option);

                // if last selected
                // add a delay to make sure that the option is added to the list.
                if (lastSelectedId == element.id) {
                    toSelect = textPagesList.options.length - 1;
                }
            });

            // select last selected
            if (toSelect != -1) {
                textPagesList.selectedIndex = toSelect;
            }
        });
}

loadTextPagesList();

imgPicker.loadImagePicker(images, () => { }, () => { });

function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        tinymce.activeEditor.setContent('');
        editedPageId = -1;
        imgPicker.unselectAllImages();
        textPagesList.selectedIndex = 0;
        title.value = '';
        pageHref.value = '';
    }
}

document.getElementById('new-page').onclick = function () {
    isInNewPageMode = true;
    toggleEditor(masterEditor, true);
    tinymce.activeEditor.setContent('');
    editedPageId = -1;
    imgPicker.unselectAllImages();
    textPagesList.selectedIndex = 0;
    title.value = '';
    pageHref.value = '';
    btnSubmit.innerHTML = 'Create';
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

// on 'title' edit end, try to generate a href automatically:
title.onblur = function () {
    if (pageHref.value == '') {
        let titleValue = title.value;
        // Remove special characters
        titleValue = titleValue.replace(/[^a-zA-Z0-9 ]/g, "");
        // Replace spaces with dashes
        titleValue = titleValue.replace(/ /g, "-");
        // Make lowercase
        titleValue = titleValue.toLowerCase();
        // Make sure that the count of characters is less than 50
        titleValue = titleValue.substring(0, 50);

        pageHref.value = '/' + titleValue;
    }
}