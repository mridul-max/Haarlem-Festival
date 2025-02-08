import { getNavbarItems, isCurrentLink } from './utils.js';

function getSocials() {
    // For now, we return a static list.
    return [
        {
            name: 'X',
            url: 'https://twitter.com/gemeentehaarlem',
            img_src: '/img/png/X_logo.png'
        },
        {
            name: 'Instagram',
            url: 'https://www.instagram.com/gemeentehaarlem/',
            img_src: '/img/png/instagram.png'
        },
    ]
}
function onLoad() {
    const foot = document.getElementsByClassName('foot')[0];
    if (foot == null) {
        console.error('foot is null');
        return;
    }

    // Check if foot contains classes row and bottom. If not, add them
    if (!foot.classList.contains('row')) {
        foot.classList.add('row');
    }
    if (!foot.classList.contains('bottom')) {
        foot.classList.add('bottom');
    }

    const contactDiv = document.createElement('div');
    contactDiv.classList.add('col-lg-5', 'col-md-2', 'col-sm-12', 'col-xs-12');
    const contactUl = document.createElement('ul');
    contactUl.classList.add('list-unstyled', 'social');
    const contactUlSpan = document.createElement('span');
    contactUlSpan.classList.add('d-block');
    contactUlSpan.innerText = 'Contact us';
    contactUl.appendChild(contactUlSpan);

    let contactUlDiv = document.createElement('div');
    contactUlDiv.classList.add('row', 'd-inline');
    getSocials().forEach(social => {
        let contactUlLi = document.createElement('li');
        contactUlLi.classList.add('d-inline', 'me-3', 'list-unstyled'); // Added margin
        
        let contactUlLiA = document.createElement('a');
        contactUlLiA.href = social.url;
        
        let contactUlLiAImg = document.createElement('img');
        contactUlLiAImg.classList.add('d-inline', 'img-fluid');
        contactUlLiAImg.style.width = '30px'; // Fixed size
        contactUlLiAImg.style.height = '30px';
        contactUlLiAImg.src = social.img_src;
        contactUlLiAImg.alt = social.name;
        
        contactUlLiA.appendChild(contactUlLiAImg);
        contactUlLi.appendChild(contactUlLiA);
        contactUlDiv.appendChild(contactUlLi);
    });
    contactUl.appendChild(contactUlDiv);
    contactDiv.appendChild(contactUl);
    foot.appendChild(contactDiv);

    // Now the nav column
    const navDiv = document.createElement('div');
    navDiv.classList.add('col-lg-2', 'col-md-4', 'col-sm-12', 'col-xs-12');
    const navUl = document.createElement('ul');
    navUl.classList.add('foot-nav');

    let navs = getNavbarItems();

    navs.then(data => {
        data.forEach(nav => {
            let navLi = document.createElement('li');
            navLi.classList.add('list-unstyled')
            let navLiA = document.createElement('a');
            navLiA.innerText = nav.page.title;
            navLiA.href = nav.page.href;
            navLiA.classList.add('footer-nav');
            if (isCurrentLink(nav.page.href)) {
                navLiA.classList.add('footer-nav-active');
            }
            if (nav.children.length > 0) {
                // Create a dropdown
                navLiA.classList.add('dropdown-toggle');
                navLiA.setAttribute('data-bs-toggle', 'dropdown');
                navLiA.setAttribute('aria-haspopup', 'true');
                navLiA.setAttribute('aria-expanded', 'false');
                let navLiADiv = document.createElement('div');
                navLiADiv.classList.add('dropdown-menu', 'dropdown-footer');
                navLiADiv.setAttribute('aria-labelledby', 'navbarDropdown');
                let navLiADivAHome = document.createElement('a');
                navLiADivAHome.classList.add('dropdown-item');
                navLiADivAHome.innerText = 'Home';
                navLiADivAHome.href = nav.page.href;
                navLiADiv.appendChild(navLiADivAHome);
                nav.children.forEach(child => {
                    let navLiADivA = document.createElement('a');
                    navLiADivA.classList.add('dropdown-item');
                    navLiADivA.innerText = child.page.title;
                    navLiADivA.href = child.page.href;
                    navLiADiv.appendChild(navLiADivA);
                });
                navLi.appendChild(navLiADiv);
            }
            navLi.appendChild(navLiA);
            navUl.appendChild(navLi);
        })
    });

    navDiv.appendChild(navUl);
    foot.appendChild(navDiv);
    const createdByDiv = document.createElement('div');
    createdByDiv.classList.add('col-lg-5', 'col-md-2', 'col-sm-12', 'col-xs-12');
    const createdByUl = document.createElement('ul');
    //span
    const createdByUlSpan = document.createElement('span');
    createdByUl.appendChild(createdByUlSpan);
    const createdByUlLi = document.createElement('li');
    createdByUlLi.classList.add('list-unstyled');
    const createdByUlLiP = document.createElement('p');
    createdByUlLiP.innerText = 'Copyright@ Mahedi & Taanzel 2025';
    createdByUlLi.appendChild(createdByUlLiP);
    createdByUl.appendChild(createdByUlLi);
    // add the div
    createdByDiv.appendChild(createdByUl);
    foot.appendChild(createdByDiv);
}

onLoad();