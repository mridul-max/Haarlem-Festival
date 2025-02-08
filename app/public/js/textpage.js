let areImagesSwappedForSmall = false;
let tableRowsWithImagesOnRight = document.querySelectorAll('tr td:nth-child(2) img');

function swapTableImg() {
    tableRowsWithImagesOnRight.forEach(element => {
        let parent = element.parentNode;
        let sibling = parent.previousElementSibling;
        parent.parentNode.insertBefore(parent, sibling);
    });
}

function checkResize() {
    if ($(window).width() < 960) {
        if (areImagesSwappedForSmall) {
            return;
        }
        areImagesSwappedForSmall = true;
        swapTableImg();
    }
    else {
        if (!areImagesSwappedForSmall) {
            return;
        }
        areImagesSwappedForSmall = false;
        swapTableImg();
    }
}

// Calls checkResize on resize (duh)
$(window).on('resize', function () {
    checkResize();
});

checkResize();

if (document.getElementById('mapContainer')) {
    console.log('Map container found! Loading map...');
    let script = document.createElement('script');
    script.src = '/js/modules/map.js';
    script.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(script);

    // Start map after loading script
    script.onload = () => HMap.start('mapContainer');
} else {
    console.log('No map container found.');
}

// Load calendar (if present)
if (document.getElementById('calendar')) {
    console.log('Calendar container found! Loading calendar...');
    let script = document.createElement('script');
    script.src = '/js/modules/calendar.js';
    script.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(script);
} else {
    console.log('No calendar container found.');
}

if (document.getElementById('countdown')) {
    console.log('Countdown container found! Loading countdown...');
    let script = document.createElement('script');
    script.src = '/js/modules/countdown.js';
    script.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(script);

    script.onload = () => Countdown.start('countdown');
} else {
    console.log('No countdown container found.');
}
// Load all day pass module
if (document.getElementById('allday-pass')) {
    console.log('All day pass container found! Loading all day pass...');
    let script = document.createElement('script');
    script.src = '/js/modules/allaccesspass.js';
    script.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(script);

    script.onload = () => AllAccessPass.start('allday-pass');
} else {
    console.log('No all day pass container found.');
}

// Load events module
if (document.getElementById('events')) {
    console.log('Events container found! Loading events...');
    let script = document.createElement('script');
    script.src = '/js/modules/events.js';
    script.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(script);

    script.onload = () => EventsList.build('events');
} else {
    console.log('No events container found.');
}
