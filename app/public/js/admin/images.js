// Author: Konrad
if (window.frameElement == null) {
    window.location.href = '/manageImages';
    throw new Error('Unauthorized access');
}

import { ImagePicker } from "./image_picker.js";
import { MsgBox } from "./modals.js";

const msgBox = new MsgBox();

let images = document.getElementById("images");
const imgPicker = new ImagePicker();
function load() {
    imgPicker.loadImagePicker(images, () => {
        imgPicker.unselectAllButOneNotInSelectedImages();
        let selectedImages = imgPicker.getSelectedImages();

        if (selectedImages.length > 0) {
            fetch('/api/images/' + selectedImages[0], {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('id').value = data.id;
                    document.getElementById('loaded-alt').value = data.alt;
                    document.getElementById('loaded-image').src = data.src;
                    document.getElementById('file-size').value = (data.size * 0.000001).toFixed(2) + " MB";
                    document.getElementById('resolution').value = data.resolution;
                    document.getElementById('mime-type').value = data.type;
                    document.getElementById('btn-remove').disabled = false;
                });
        }
    }, () => {
        clearDetails();
    });
}
load();

function clearDetails() {
    // clear the details
    document.getElementById('id').value = '';
    document.getElementById('loaded-alt').value = '';
    document.getElementById('loaded-image').src = '';
    document.getElementById('file-size').value = '';
    document.getElementById('resolution').value = '';
    document.getElementById('mime-type').value = '';
}

document.getElementById('btn-remove').onclick = () => {
    msgBox.createYesNoDialog('Are you sure?', 'Are you sure you want to delete this image?', () => {

        let id = document.getElementById('id').value;
        if (id) {
            fetch(`/api/images/` + id, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    clearDetails();
                    load();
                    if (!data.error_message) {
                        // reload image picker

                        msgBox.createToast('Success!', 'Image has been deleted');
                    } else {
                        msgBox.createToast('Somethin went wrong', data.error_message);
                    }
                });
        }
    }, () => { });
}

document.getElementById('btn-save').onclick = () => {
    let id = document.getElementById('id').value;
    let alt = document.getElementById('loaded-alt').value;
    if (id) {
        fetch(`/api/images/` + id, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "alt": alt
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (!data.error_message) {
                    // reload image picker
                    imgPicker.loadImagePicker(images);
                    msgBox.createToast('Success!', 'Image details have been updated');
                } else {
                    msgBox.createToast('Somethin went wrong', data.error_message);
                }
            });
    }
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