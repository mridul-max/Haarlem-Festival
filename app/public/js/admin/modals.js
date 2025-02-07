// Author: Konrad
import { ImagePicker } from "./image_picker.js";

export class MsgBox {
    /**
     * Creates a toast message. Disappears after 3 seconds.
     * @param {*} header Text for the header.
     * @param {*} msg Text for the message.
     */
    createToast(header, msg) {
        // Create bootstrap toast
        let toast = document.createElement('div');
        toast.classList.add('toast');
        toast.style.position = 'absolute';
        toast.style.zIndex = 9999;
        toast.style.left = '30px';
        toast.style.top = '30px';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-bs-delay', '3000');
        toast.setAttribute('data-bs-autohide', 'true');

        // Create header
        let toastHeader = document.createElement('div');
        toastHeader.classList.add('toast-header');
        toastHeader.innerHTML = header;

        // Create body
        let toastBody = document.createElement('div');
        toastBody.classList.add('toast-body');
        toastBody.innerHTML = msg;

        // Append header and body to toast
        toast.appendChild(toastHeader);
        toast.appendChild(toastBody);

        // Append toast to the beginning of the body
        document.body.insertBefore(toast, document.body.firstChild);

        // Show toast
        let toastElement = new bootstrap.Toast(toast);
        toastElement.show();
    }

    /**
     * Creates a yes/no dialog.
     * @param {*} header Text for the header.
     * @param {*} msg Text for the message.
     * @param {*} yesCallback Callback for the yes button.
     * @param {*} noCallback Callback for the no button.
     */
    createYesNoDialog(header, msg, yesCallback, noCallback = () => { }) {
        // Create bootstrap modal
        let modal = document.createElement('div');
        modal.classList.add('modal');
        modal.setAttribute('tabindex', '-1');

        // Create modal dialog
        let modalDialog = document.createElement('div');
        modalDialog.classList.add('modal-dialog');
        modal.appendChild(modalDialog);

        // Create modal content
        let modalContent = document.createElement('div');
        modalContent.classList.add('modal-content');
        modalDialog.appendChild(modalContent);

        // Create modal header
        let modalHeader = document.createElement('div');
        modalHeader.classList.add('modal-header');
        modalContent.appendChild(modalHeader);

        // Create modal title
        let modalTitle = document.createElement('h5');
        modalTitle.classList.add('modal-title');
        modalTitle.innerHTML = header;
        modalHeader.appendChild(modalTitle);

        // Create modal body
        let modalBody = document.createElement('div');
        modalBody.classList.add('modal-body');
        modalContent.appendChild(modalBody);
        let modalBodyP = document.createElement('p');
        modalBodyP.innerHTML = msg;
        modalBody.appendChild(modalBodyP);

        // Create modal footer
        let modalFooter = document.createElement('div');
        modalFooter.classList.add('modal-footer');
        modalContent.appendChild(modalFooter);

        // Show modal
        let modalElement = new bootstrap.Modal(modal);
        modalElement.show();

        // Create yes button
        let yesButton = document.createElement('button');
        yesButton.classList.add('btn', 'btn-primary');
        yesButton.innerHTML = 'Yes';
        yesButton.onclick = function () {
            yesCallback();
            modalElement.hide();
        }
        modalFooter.appendChild(yesButton);

        // Create no button
        let noButton = document.createElement('button');
        noButton.classList.add('btn', 'btn-secondary');
        noButton.innerHTML = 'No';
        noButton.onclick = function () {
            noCallback();
            modalElement.hide();
        }
        modalFooter.appendChild(noButton);

        // Append modal to the beginning of the body
        document.body.insertBefore(modal, document.body.firstChild);

        // Focus on no button
        noButton.focus();
    }

    createDialogWithInputs(header, inputsArray, submitCallback, cancelCallback = () => { }) {
        // Create bootstrap modal
        let modal = document.createElement('div');
        modal.classList.add('modal');
        modal.setAttribute('tabindex', '-1');

        // Create modal dialog
        let modalDialog = document.createElement('div');
        modalDialog.classList.add('modal-dialog');
        modal.appendChild(modalDialog);

        // Create modal content
        let modalContent = document.createElement('div');
        modalContent.classList.add('modal-content');
        modalDialog.appendChild(modalContent);

        // Create modal header
        let modalHeader = document.createElement('div');
        modalHeader.classList.add('modal-header');
        modalContent.appendChild(modalHeader);

        // Create modal title
        let modalTitle = document.createElement('h5');
        modalTitle.classList.add('modal-title');
        modalTitle.innerHTML = header;
        modalHeader.appendChild(modalTitle);

        // Create modal body
        let modalBody = document.createElement('div');
        modalBody.classList.add('modal-body');
        modalContent.appendChild(modalBody);

        let modalBodyInputs = document.createElement('div');

        for (let i of inputsArray) {
            if (i.type == 'image-picker') {
                let pickerContainer = document.createElement('div');
                pickerContainer.setAttribute('id', i.id);
                let picker = new ImagePicker();
                picker.loadImagePicker(pickerContainer, () => { picker.unselectAllButOneNotInSelectedImages(); }, () => { });
                modalBodyInputs.appendChild(pickerContainer);
                pickerContainer.style.overflowY = 'auto';
                pickerContainer.style.height = '200px';
            } else if (i.type == 'textarea') {
                let label = document.createElement('label');
                label.classList.add('form-label');
                label.setAttribute('for', i.id);
                label.innerHTML = i.label;
                modalBodyInputs.appendChild(label);

                let textarea = document.createElement('textarea');
                textarea.classList.add('form-control');
                textarea.setAttribute('id', i.id);
                modalBodyInputs.appendChild(textarea);
                textarea.style.height = '200px';
                textarea.style.fontSize = '12px';
                textarea.style.resize = 'both';
                // set content of textarea
                if (i.content) {
                    textarea.innerHTML = i.content;
                }
            } else {
                let label = document.createElement('label');
                label.classList.add('form-label');
                label.setAttribute('for', i.id);
                label.innerHTML = i.label;
                modalBodyInputs.appendChild(label);

                let input = document.createElement('input');
                input.classList.add('form-control');
                input.setAttribute('type', 'text');
                input.setAttribute('id', i.id);
                modalBodyInputs.appendChild(input);
            }
        }
        modalBody.appendChild(modalBodyInputs);

        // Create modal footer
        let modalFooter = document.createElement('div');
        modalFooter.classList.add('modal-footer');
        modalContent.appendChild(modalFooter);

        // Show modal
        let modalElement = new bootstrap.Modal(modal);
        modalElement.show();

        // Create yes button
        let submitButton = document.createElement('button');
        submitButton.classList.add('btn', 'btn-primary');
        submitButton.innerHTML = 'Submit';
        submitButton.onclick = function () {
            submitCallback();
            modalElement.hide();
        }
        modalFooter.appendChild(submitButton);

        // Create no button
        let cancelButton = document.createElement('button');
        cancelButton.classList.add('btn', 'btn-secondary');
        cancelButton.innerHTML = 'Cancel';
        cancelButton.onclick = function () {
            cancelCallback();
            modalElement.hide();
        }
        modalFooter.appendChild(cancelButton);

        // Append modal to the beginning of the body
        document.body.insertBefore(modal, document.body.firstChild);

        // Focus on no button
        cancelButton.focus();

        return modal;
    }
}