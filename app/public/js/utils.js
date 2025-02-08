let navItems = null;

/**
 * @returns Returns the array of navbar items from '/api/nav'
 */
export function getNavbarItems() {
    if (navItems == null) {
        navItems = fetch('/api/nav')
            .then(response => response.json())
            .then(data => data)
            .catch(error => console.error(error));
    }
    return navItems;
}

/**
 * @returns Returns the array of social media items from '/api/socials'
 * @param {string} href The href to compare to the current page
 */
export function isCurrentLink(href) {
    if (href.length == 0) {
        href += "/";
    }
    href = href.split('?')[0];
    return window.location.pathname == href;
}

export function isValidPassword(password) {
    return (password.length >= 8);
}