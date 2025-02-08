const START_DATE = '2025-07-27 10:00:00';

let fcScript = document.createElement('script');
fcScript.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js';
fcScript.type = 'text/javascript';
document.getElementsByTagName('head')[0].appendChild(fcScript);
let calendar = null;

let calendarLoadRetries = 0;
let calLoadInterval = setInterval(() => {
    if (calendarLoadRetries > 5) {
        clearInterval(calLoadInterval);
        console.error('Could not load calendar.');
        return;
    }
    if (typeof FullCalendar === 'undefined') {
        calendarLoadRetries++;
        return;
    }
    clearInterval(calLoadInterval);
    console.log('FullCalendar defined. Loading calendar...');
    loadCalendar();
}, 1000);


function loadCalendar() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap5',
        initialView: 'timeGridWeek4Days',
        selectable: true,
        height: 650,
        initialDate: getStartDate(),
        headerToolbar: {
            left: 'prev,next today backToEvent',
            center: 'title',
            right: 'timeGridWeek4Days,timeGridDay'
        },
        slotLabelFormat: {
            hour: 'numeric',
            hour12: false,
            minute: '2-digit'
        },
        eventTimeFormat: {
            hour: 'numeric',
            hour12: false,
            minute: '2-digit'
        },
        views: {
            timeGridWeek4Days: {
                type: 'timeGridWeek',
                buttonText: '4 days',
                duration: { days: 4 },
                slotEventOverlap: false,
                allDaySlot: false,
                dayHeaderFormat: {
                    weekday: 'long'
                }
            }
        },
        customButtons: {
            backToEvent: {
                text: 'back to event',
                click: function () {
                    fetch('/api/events/dates',
                        {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        }
                    ).then(response => response.json())
                        .then(data => {
                            // get first object and convert to date
                            const firstDate = data[0] + " 10:00:00";
                            calendar.changeView($(window).width() < 960 ? 'timeGridDay' : 'timeGridWeek4Days');
                            calendar.gotoDate(firstDate);
                            calendar.scrollToTime('10:00:00');
                        })
                }
            }
        },
        eventContent: function (info) {
            return { html: "<center>" + info.event.title + "</center>" };
        }
    });

    calendar.render();

    // wait for calendar to be rendered
    setTimeout(function () {
        // scroll to 10am
        calendar.scrollToTime('10:00:00');
    }, 1000);

    function getStartDate() {
        // If today is after the start date, return today.
        if (new Date() > new Date(START_DATE)) {
            return new Date();
        }

        return START_DATE;
    }

    function checkResize() {
        calendar.changeView($(window).width() < 960 ? 'timeGridDay' : 'timeGridWeek4Days');
    }

    function addEvent(title, start, end, url, backgroundColor, borderColor) {
        calendar.addEvent({
            title: title,
            start: start,
            end: end,
            url: url,
            backgroundColor: backgroundColor,
            textColor: '#000',
            borderColor: borderColor
        });
    }

    $(window).on('resize', function () {
        checkResize();
    });
    checkResize();

    // Check what kind of calendar it is and load the events.
    if (calendarEl.dataset.calendarType === 'all-events') {
        loadAllEvents();
    } else if (calendarEl.dataset.calendarType === 'personal') {
        // TODO: Load events for the current user from the database.
    } else if (calendarEl.dataset.calendarType === 'stroll') {
        loadStrollEvents();
    }

    function loadAllEvents() {
        fetch('/api/events',
            {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        ).then(response => response.json())
            .then(data => {
                for (let e of data) {
                    let backgroundColor = "#e2e0da";
                    let borderColor = "#412c0c";
                    let url = "#";

                    if (e.event.eventType.id == 1) {
                        backgroundColor = "#d6e7ef";
                        borderColor = "#005990";
                        url = "/festival/jazz/event/" + e.event.id;
                    } else if (e.event.eventType.id == 3) {
                        backgroundColor = "#e2e0da";
                        borderColor = "#412c0c";
                        url = "/festival/history-stroll";
                    }

                    const startTime = new Date(e.event.startTime.date);
                    const endTime = new Date(e.event.endTime.date);

                    // Don't add events shorter than 30 minutes.
                    if (endTime - startTime < 30 * 60 * 1000) {
                        continue;
                    }

                    // Don't add events longer than 8 hours.
                    if (endTime - startTime > 8 * 60 * 60 * 1000) {
                        continue;
                    }

                    addEvent(e.event.name, startTime, endTime, url, backgroundColor, borderColor);
                }

            }
            );
    }

    function loadStrollEvents() {
        fetch('/api/events/stroll',
            {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        ).then(response => response.json())
            .then(data => {
                for (let e of data) {
                    const backgroundColor = "#e2e0da";
                    const borderColor = "#412c0c";
                    const url = "#";

                    let text = "<center>" + e.event.guide.language + "<br>" + e.event.guide.guideName + "  " + e.event.guide.lastName + "</center>";

                    addEvent(text, new Date(e.event.startTime.date), new Date(e.event.endTime.date), url, backgroundColor, borderColor);
                }

            }
            );
    }
}