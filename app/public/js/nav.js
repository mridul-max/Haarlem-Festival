import { isCurrentLink, getNavbarItems } from './utils.js';

//Check if /js/cart.js is loaded.
if (typeof Cart === 'undefined') {
    //console.error('cart.js is not loaded!');
    //load it.
    let script = document.createElement('script');
    script.src = '/js/cart.js';
    document.head.appendChild(script);
}

// Creates a nav link for the navbar.
function createNavLink(collapseLi, element) {
    // Create the a
    let collapseA = document.createElement('a');
    collapseA.classList.add('nav-link');
    let link = element.page.href;
    if (link.length == 0) {
        link = "/";
    }
    collapseA.setAttribute('href', link);
    collapseA.textContent = element.page.title;

    if (link == '/') {
        // Unique name for the home link
        collapseA.textContent = "Home";
    }

    // If the current URL contains the href of the page, add the active class to the li.
    if (isCurrentLink(element.page.href)) {
        collapseA.classList.add('active');
    }

    // Add the a to the li
    collapseLi.appendChild(collapseA);
    return collapseLi
}

// Creates a dropdown menu for the navbar.
function createDropdown(collapseLi, element) {
    collapseLi.classList.add('dropdown');
    // open on hover
    collapseLi.onmouseover = function () {
        // if window width is less than 960 px
        collapseLi.classList.add('show');
        collapseLi.children[1].classList.add('show');
        collapseLi.children[0].setAttribute('aria-expanded', 'true');
    };
    collapseLi.onmouseleave = function () {
        collapseLi.classList.remove('show');
        collapseLi.children[1].classList.remove('show');
        collapseLi.children[0].setAttribute('aria-expanded', 'false');
    };
    // Create the a
    let collapseA = document.createElement('a');
    collapseA.classList.add('nav-link');
    let link = element.page.href;
    if (link.length == 0) {
        link = "/";
    }
    collapseA.setAttribute('href', link);
    collapseA.setAttribute('id', 'navbarDropdown-' + element.id);
    collapseA.setAttribute('role', 'button');
    collapseA.setAttribute('aria-haspopup', 'true');
    collapseA.setAttribute('aria-expanded', 'false');
    collapseA.textContent = element.page.title;
    collapseLi.appendChild(collapseA);

    // If the current URL contains the href of the page, add the active class to the li.
    if (isCurrentLink(element.page.href)) {
        collapseA.classList.add('active');
    }

    // Create the dropdown menu
    let collapseDropdownMenu = document.createElement('div');
    collapseDropdownMenu.classList.add('dropdown-menu');
    collapseDropdownMenu.setAttribute('aria-labelledby', 'navbarDropdown-' + element.id);
    // Create the dropdown menu a
    element.children.forEach(child => {
        let collapseDropdownMenuA = document.createElement('a');
        collapseDropdownMenuA.classList.add('dropdown-item');
        collapseDropdownMenuA.setAttribute('href', child.page.href);
        collapseDropdownMenuA.textContent = child.page.title;
        collapseDropdownMenu.appendChild(collapseDropdownMenuA);

        if (isCurrentLink(child.page.href)) {
            collapseDropdownMenuA.classList.add('active');
        }
    }
    );
    // Add the dropdown menu to the li
    collapseLi.appendChild(collapseDropdownMenu);
    return collapseLi;
}

function createIcon(href, alt, iconClass) {
    let collapseLi = document.createElement('li');
    collapseLi.classList.add('nav-item');
    //collapseLi.classList.add('d-none');
    collapseLi.classList.add('d-lg-block');
    collapseLi.classList.add('px-2');

    let collapseA = document.createElement('a');
    collapseA.setAttribute('href', href);

    collapseA.classList.add('nav-item');
    //collapseA.classList.add('d-none');
    collapseA.classList.add('d-lg-block');
    collapseA.classList.add('d-xl-block');
    collapseA.classList.add(iconClass);
    collapseA.setAttribute('alt', alt);

    collapseLi.appendChild(collapseA);

    if (alt == 'Shopping cart') {
        // Create a circle with the number of items in the cart
        let cartCircle = document.createElement('div');
        cartCircle.classList.add('shopping-circle');
        cartCircle.id = 'shopping-circle';

        let cartCircleText = document.createElement('p');
        cartCircleText.classList.add('shopping-circle-text');
        cartCircleText.textContent = '0';
        cartCircleText.id = 'shopping-circle-text';

        cartCircle.appendChild(cartCircleText);

        collapseA.appendChild(cartCircle);

        Cart.UpdateCounter();
    }

    return collapseLi;
}

function createSearch() {
    let collapseLi = document.createElement('li');
    collapseLi.classList.add('nav-item');
    collapseLi.classList.add('dropdown');
    collapseLi.onmouseover = function () {
        collapseLi.classList.add('show');
        collapseLi.children[1].classList.add('show');
        collapseLi.children[0].setAttribute('aria-expanded', 'true');
    };
    collapseLi.onmouseleave = function () {
        collapseLi.classList.remove('show');
        collapseLi.children[1].classList.remove('show');
        collapseLi.children[0].setAttribute('aria-expanded', 'false');
    };
    // Create the a
    let collapseA = document.createElement('a');
    collapseA.classList.add('nav-link');
    collapseA.classList.add('dropdown-bs-toggle');
    collapseA.setAttribute('href', "#");
    collapseA.setAttribute('role', 'button');
    collapseA.setAttribute('aria-haspopup', 'true');
    collapseA.setAttribute('aria-expanded', 'false');
    collapseA.classList.add('search-icon');
    collapseLi.appendChild(collapseA);

    // Create the dropdown menu
    let collapseDropdownMenu = document.createElement('div');
    collapseDropdownMenu.classList.add('dropdown-menu');

    // Create input field for search and add it to dropdown
    let collapseDropdownMenuInput = document.createElement('input');
    collapseDropdownMenuInput.classList.add('form-control');
    collapseDropdownMenuInput.classList.add('dropdown-item');
    collapseDropdownMenuInput.setAttribute('type', 'text');
    collapseDropdownMenuInput.setAttribute('placeholder', 'Search');
    collapseDropdownMenuInput.setAttribute('aria-label', 'Search');
    collapseDropdownMenu.appendChild(collapseDropdownMenuInput);

    // Add the dropdown menu to the li
    collapseLi.appendChild(collapseDropdownMenu);
    return collapseLi;
}

// Loads the navbar.
function loadNavbar() {
    let nav = document.getElementsByClassName('navbar')[0];
    if (nav == null) {
        console.error('Navbar not found');
        return;
    }

    // Create the collapse button
    let colapseButton = document.createElement('button');
    colapseButton.classList.add('navbar-toggler');
    colapseButton.setAttribute('type', 'button');
    colapseButton.setAttribute('data-bs-toggle', 'collapse');
    colapseButton.setAttribute('data-bs-target', '#navbarNav');
    colapseButton.setAttribute('aria-controls', 'navbarNav');
    colapseButton.setAttribute('aria-expanded', 'false');
    colapseButton.setAttribute('aria-label', 'Toggle navigation');
    colapseButton.classList.add('m-1');
    // Create the span
    let colapseButtonSpan = document.createElement('span');
    colapseButtonSpan.classList.add('navbar-toggler-icon');
    colapseButton.appendChild(colapseButtonSpan);
    // Add the button to the navbar
    nav.appendChild(colapseButton);

    // Create the collapse div
    let collapseDiv = document.createElement('div');
    collapseDiv.classList.add('collapse');
    collapseDiv.classList.add('navbar-collapse');
    collapseDiv.classList.add('justify-content-md-center');
    collapseDiv.setAttribute('id', 'navbarNav');
    // Create the ul
    let collapseUl = document.createElement('ul');
    collapseUl.classList.add('navbar-nav');

    let navs = getNavbarItems();

    navs.then(data => {
        data.forEach(element => {
            // Create the li
            let collapseLi = document.createElement('li');
            collapseLi.classList.add('nav-item');

            // Check if element is in half of array
            if (Math.floor(data.length / 2) == data.indexOf(element) - 1) {
                // add a
                let collapseA = document.createElement('a');
                collapseA.setAttribute('href', '/');;
                collapseA.classList.add('navbar-brand');
                collapseA.classList.add('d-none');
                collapseA.classList.add('d-lg-block');
                collapseA.classList.add('d-xl-block');

                collapseUl.appendChild(collapseA);
            }

            if (element.children.length > 0) {
                // Add the li to the ul
                collapseUl.appendChild(createDropdown(collapseLi, element));
            } else {
                // Add the li to the ul
                collapseUl.appendChild(createNavLink(collapseLi, element));
            }
        });
    })
        .then(() => {
            collapseUl.appendChild(createIcon('/home/account', 'Account', 'user-icon'));
            collapseUl.appendChild(createIcon('/shopping-cart', 'Shopping cart', 'shopping-cart-icon'));
        });

    // Add the ul to the collapse div
    collapseDiv.appendChild(collapseUl);

    // Add the collapse div to the navbar
    nav.appendChild(collapseDiv);
}
loadNavbar();