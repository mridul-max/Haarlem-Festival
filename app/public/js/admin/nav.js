// Author: Konrad
if (window.frameElement == null) {
    window.location.href = '/manageNavBar';
    throw new Error('Unauthorized access');
}

import { MsgBox } from "./modals.js";

// Artist fields.
const navs = document.getElementById('navs');

const btnSubmit = document.getElementById('submit');

const msgBox = new MsgBox();

let baseURL = '/api/nav';

function createNewEntry(data) {
    fetch(baseURL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (!data.error_message) {
                loadList();
            } else {
                msgBox.createToast('Something went wrong', data.error_message);
            }
        })
        .catch(error => {
            msgBox.createToast('Something went wrong', error);
        });
}

btnSubmit.onclick = function () {
    // get all the options that are the first child of the nav
    let options = navs.getElementsByTagName('li');
    let data = [];

    for (let option of options) {
        // if is not a child of navs, skip it.
        if (option.parentElement.id != 'navs') {
            continue;
        }

        let e = {
            id: option.id,
            page: {
                id: option.dataset.pageId
            },
            children: []
        }

        // check if there are children
        let children = option.getElementsByTagName('li');
        for (let child of children) {
            e.children.push({
                id: child.id,
                page: {
                    id: child.dataset.pageId
                },
                children: []
            });
        }

        data.push(e);
    }

    createNewEntry(data);
}

function createNewOptionItem(element) {
    // create option
    let li = document.createElement('li');
    li.classList.add('list-group-item', 'my-2');
    li.id = element.id;
    li.dataset.pageId = element.page.id;

    // add p and - button
    let p = document.createElement('p');
    p.classList.add('d-inline-block');
    p.innerHTML = element.page.title;
    li.appendChild(p);

    let btn = document.createElement('button');
    btn.classList.add('btn', 'btn-danger', 'float-right', 'ml-2');
    btn.innerHTML = '-';
    btn.onclick = function () {
        // remove the option from the list
        li.remove();

        createNewPagesItem(element.page);
    }
    li.appendChild(btn);

    // add the 'open' button
    btn = document.createElement('button');
    btn.classList.add('btn', 'btn-primary', 'float-right');
    btn.innerHTML = 'Open';
    btn.onclick = function () {
        // open the page
        window.open(element.page.href, '_blank');
    }
    li.appendChild(btn);


    let ul = document.createElement('ul');
    ul.classList.add('list-group');
    ul.classList.add('list-group-flush');
    ul.classList.add('ml-3');
    // set height to at least 16px, so you can drag into it.
    ul.style.minHeight = '16px';
    li.appendChild(ul);

    // If children of elemnt exist, create a ul and add the children to it.
    if (element.children.length > 0) {
        ul.id = element.id;
        ul.dataset.pageId = element.page.id;
        element.children.forEach(child => {
            ul.appendChild(createNewOptionItem(child));
        });
    }

    return li;
}

function createNewPagesItem(element) {
    let options = navs.getElementsByTagName('li');
    for (let option of options) {
        if (option.dataset.pageId == element.id) {
            // skip this one
            return;
        }

        // check the children
        let children = option.getElementsByTagName('li');
        for (let child of children) {
            if (child.dataset.pageId == element.id) {
                return;
            }
        }
    }

    let li = document.createElement('li');
    li.classList.add('list-group-item');
    li.id = element.id;

    // Add the p and + buttons.
    let p = document.createElement('p');
    p.innerHTML = element.title;
    li.appendChild(p);

    let btn = document.createElement('button');
    btn.classList.add('btn');
    btn.classList.add('btn-primary');
    btn.classList.add('btn-sm');
    btn.innerHTML = '+';
    btn.onclick = function () {
        let e = {
            id: 0,
            page: {
                id: li.id,
                title: p.innerHTML,
                href: element.href
            },
            children: []
        }

        let option = createNewOptionItem(e);
        navs.appendChild(option);

        // remove self from pages
        li.remove();
    };
    li.appendChild(btn);

    // aqdd btn that lets you open the page
    let btnOpen = document.createElement('button');
    btnOpen.classList.add('btn');
    btnOpen.classList.add('btn-primary');
    btnOpen.classList.add('btn-sm');
    btnOpen.classList.add('ml-2');
    btnOpen.innerHTML = 'Open';
    btnOpen.onclick = function () {
        window.open(element.href, '_blank');
    };
    li.appendChild(btnOpen);

    pages.appendChild(li);

    return li;
}


async function loadList() {
    const pages = document.getElementById('pages');
    navs.innerHTML = '';
    pages.innerHTML = '';

    // fetch with post
    await fetch(baseURL, {
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
                    option.dataset.pageId = element.page.id;

                    // append option
                    navs.appendChild(option);
                });
            }
        });

    fetch('/api/pages', {
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
                    // Do not add, if already in the list of navs.
                    createNewPagesItem(element);
                });
            }
        }
        );
}

loadList();

$('#navs').sortable({
    group: 'nested'
});


function toggleEditor(element, isEnabled) {
    if (isEnabled) {
        element.classList.remove('disabled-module');
    } else {
        element.classList.add('disabled-module');
        editedId = -1;
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