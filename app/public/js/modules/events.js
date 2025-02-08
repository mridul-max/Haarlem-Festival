class EventsList {
    constructor(container) {
        this.events = [];

        this.init(container);
    }

    static build(containerId) {
        this.container = document.getElementById(containerId);

        if (!this.container) {
            throw new Error('Container not found');
        }

        this.container.classList.add('row', 'col-12');

        const data = this.container.dataset;
        if (!data) {
            throw new Error('No data found');
        }

        if (data.type === 'jazz') {
            console.log('Jazz events');
            // Load jazz-events.js.
            let script = document.createElement('script');
            script.src = '/js/modules/event-viewers/jazz-events.js';
            script.type = 'text/javascript';
            document.getElementsByTagName('head')[0].appendChild(script);
            script.onload = () => new JazzEventList(this.container);
        } else if (data.type === 'stroll') {
            console.log('Stroll events');
            let script = document.createElement('script');
            script.src = '/js/modules/event-viewers/stroll-events.js';
            script.type = 'text/javascript';
            document.getElementsByTagName('head')[0].appendChild(script);
            script.onload = () => new StrollEventList(this.container);
        } else {
            console.log('Default events');
            return new EventsList(this.container);
        }
    }

    addEvent(event) {
        this.events.push(event);
    }

    getEvents() {
        return this.events;
    }

    removeEvent(event) {
        this.events = this.events.filter((e) => e !== event);
    }

    clearEvents() {
        this.events = [];
    }

    async init(container) {
        // create the sorting columns
        this.sortingContainer = document.createElement('div');
        this.sortingContainer.classList.add('col-3', 'p-1');
        container.appendChild(this.sortingContainer);

        let eventsMain = document.createElement('div');
        eventsMain.classList.add('col-8');

        this.eventsContainer = document.createElement('div');
        this.eventsContainer.classList.add('container', 'gy-1', 'overflow-auto');
        this.eventsContainer.style.maxHeight = '500px';
        eventsMain.appendChild(this.eventsContainer);

        container.appendChild(eventsMain);
    }

    addToCart(event) {
        Cart.Add(event);
    }

    noEvents() {
        this.eventsContainer.innerHTML = '';
        let noEventsContainer = document.createElement('div');
        noEventsContainer.classList.add('col-12', 'text-center', 'card');
        let noEvents = document.createElement('p');
        noEvents.classList.add('text-center');
        noEvents.innerText = 'Sorry! There are no events to display. Try changing your filters.';
        noEventsContainer.appendChild(noEvents);
        this.eventsContainer.appendChild(noEventsContainer);
    }
}
