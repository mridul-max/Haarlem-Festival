const eventType = document.getElementById('event-type');
const passType = document.getElementById('pass-type');
const date = document.getElementById('event-date');
const price = document.getElementById('event-price');
const master = document.getElementById('master');

const details = document.getElementById('details-thing');

// disable both
eventType.disabled = true;
passType.disabled = true;
details.classList.add('disabled');

let eventTypeId = 0;
let passTypeId = 0;
let eventId = -1;

async function load() {
    eventType.value = 0;
    await fetch('/api/eventtypes', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            data.forEach(element => {
                eventType.innerHTML += `<option value="${element.id}">${element.name}</option>`;
            });
        })
        .catch((error) => {
            console.error('Error:', error);
        }
        );

    eventType.addEventListener('change', async (e) => {
        eventTypeId = e.target.value;
        loadPassTypes();

        date.innerHTML = '';
        price.innerHTML = '';

        passTypeId = 0;

        details.classList.add('disabled');
    });

    eventType.value = 0;
    eventType.disabled = false;

    // Check the GET parameters.
    const urlParams = new URLSearchParams(window.location.search);
    const eventTypeIdParam = urlParams.get('event_type');
    if (eventTypeIdParam) {
        eventType.value = eventTypeIdParam;
        eventTypeId = eventTypeIdParam;
        loadPassTypes();
    }
}
load();

async function loadPassTypes() {
    passType.innerHTML = '';
    passType.innerHTML += `<option value="0" disabled>===Select Pass Type===</option>`;
    passType.value = 0;
    passType.disabled = true;

    await fetch(`/api/events/passes?event_type=${eventTypeId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            data.forEach(element => {
                passType.innerHTML += `<option value="${element.id}" data-event-id="${element.event.id}" >${element.event.name}</option>`;
            });
        })
        .catch((error) => {
            console.error('Error:', error);
        }
        );

    passType.addEventListener('change', async (e) => {
        passTypeId = e.target.value;
        eventId = e.target.options[e.target.selectedIndex].dataset.eventId;
        prepare();
    });

    passType.value = 0;
    passType.disabled = false;

    // Check the GET parameters.
    const urlParams = new URLSearchParams(window.location.search);
    const passTypeIdParam = urlParams.get('pass_type');
    if (passTypeIdParam) {
        passType.value = passTypeIdParam;
        passTypeId = passTypeIdParam;
        eventId = passType.options[passType.selectedIndex].dataset.eventId;
        prepare();
    }
}

function prepare() {
    if (passTypeId == 0 || eventId == -1) {
        return;
    }

    fetch('/api/events/passes/' + eventId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            // display date without the hour.
            if (data.ticketType.name.includes('All-Day')) {
                date.innerHTML = "All Day";
            } else {
                date.innerHTML = data.event.startTime.date.split(' ')[0];
            }
            price.innerHTML = '€ ' + data.ticketType.price;

            details.classList.remove('disabled');
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

document.getElementById('buy-pass').addEventListener('click', async (e) => {
    master.classList.add('disabled');
    await Cart.Add(passTypeId);

    window.location.href = '/festival';
});