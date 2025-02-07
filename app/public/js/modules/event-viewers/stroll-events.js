class StrollEventList extends EventsList {
    constructor(container) {
        super(container);
        this.loadEvents();
    }

    async init(container) {

        // load template from /templates/stroll-events-template.html
        await fetch('/templates/stroll-events-template.html')
            .then((response) => response.text())
            .then((data) => {
                container.innerHTML = data;
                this.eventsContainer = document.getElementById('events-container');

                document.getElementById('date').addEventListener('change', (event) => {
                    this.date = event.target.value;
                });
                document.getElementById('time').addEventListener('change', (event) => {
                    this.time = event.target.value;
                });
                document.getElementById('language').addEventListener('change', (event) => {
                    this.language = event.target.value;
                });
                // ticket
                document.getElementById('ticket').addEventListener('change', (event) => {
                    this.type = event.target.value;
                });

                document.getElementById('apply-btn').addEventListener('click', () => {
                    this.loadEvents();
                });

                this.loadEvents();
            });

        // Load dates
        let dates = await fetch('/api/events/dates').then((res) => res.json());

        // create array of dates also in between the first and last date
        let firstDate = new Date(dates[0]);
        let lastDate = new Date(dates[dates.length - 1]);

        while (firstDate <= lastDate) {
            let date = firstDate.toISOString().split('T')[0];
            if (!dates.includes(date)) {
                dates.push(date);
            }
            firstDate.setDate(firstDate.getDate() + 1);
        }


        // convert them to date objects
        dates = dates.map((date) => new Date(date));
        // sort
        dates.sort((a, b) => a - b);

        for (let date of dates) {
            console.log(date);
            let option = document.createElement('option');
            option.value = date.toISOString().split('T')[0];
            option.innerText = date.toDateString();
            document.getElementById('date').appendChild(option);
        }
    }

    async loadEvents() {
        this.clearEvents();
        let data = await this.getData();

        if (data.length == 0) {
            this.noEvents();
        } else {
            for (let event of data) {
                this.addEvent(event);
            }
        }
    }

    async getData() {
        // if sort by is set
        let url = '/api/events/stroll';
        let args = '';

        function addArg(arg) {
            if (args == '') {
                args += `?${arg}`;
            } else {
                args += `&${arg}`;
            }
        }

        // if time_start is set
        if (this.date) {
            addArg(`date=${this.date}`);
        }
        if (this.time) {
            addArg(`time=${this.time}`);
        }

        // if price_from is set
        if (this.language) {
            addArg(`language=${this.language}`);
        }
        if (this.type) {
            addArg(`type=${this.type}`);
        }

        let response = await fetch(url + args);
        let data = await response.json();
        return data;
    }

    addEvent(event) {
        super.addEvent(event);

        let ticketContainer = document.createElement('div');
        ticketContainer.classList.add('row', 'w-100', 'm-2', 'py-1', 'justify-content-around', 'border', 'border-1', 'border-dark', 'rounded-3');
        ticketContainer.id = 'event-' + event.id;

        let ticketHeader = document.createElement('div');
        ticketHeader.classList.add('col-6', 'my-auto');
        ticketContainer.appendChild(ticketHeader);
        //h4
        let ticketTitle = document.createElement('h4');
        ticketTitle.innerText = event.event.name;
        ticketHeader.appendChild(ticketTitle);
        //p
        let ticketPrice = document.createElement('p');
        ticketPrice.innerText = "Guide: " + event.event.guide.guideName + " " + event.event.guide.lastName;
        ticketHeader.appendChild(ticketPrice);

        let ticketBody = document.createElement('div');
        // align self to right
        ticketBody.classList.add('col-6');
        // info div
        let ticketInfo = document.createElement('div');
        ticketInfo.classList.add('align-middle');
        ticketBody.appendChild(ticketInfo);
        // p1
        let ticketInfoP1 = document.createElement('p');
        ticketInfoP1.innerHTML = "<strong>Star Point:</strong> " + event.event.location.name;
        ticketInfo.appendChild(ticketInfoP1);
        // p2
        let ticketInfoP2 = document.createElement('p');
        ticketInfoP2.innerHTML = "<strong>Language:</strong> " + event.event.guide.language;
        ticketInfo.appendChild(ticketInfoP2);
        // p3
        let ticketInfoP3 = document.createElement('p');
        // Time is in  2023-07-27 10:00:00.000000 format. Make it look better
        // Convert it to 203-07-27 10:00
        let formattedDateTime = event.event.startTime.date.split('.')[0];
        // Remove the seconds
        formattedDateTime = formattedDateTime.substring(0, formattedDateTime.length - 3);
        ticketInfoP3.innerHTML = "<strong>Time:</strong> " + formattedDateTime;
        ticketInfo.appendChild(ticketInfoP3);
        // p4
        let ticketInfoP4 = document.createElement('p');
        ticketInfoP4.innerHTML = "<strong>Available Tickets:</strong> " + event.event.availableTickets;
        ticketInfo.appendChild(ticketInfoP4);
        ticketContainer.appendChild(ticketBody);

        let ticketPriceDiv = document.createElement('div', 'w-100');
        let ticketPriceP = document.createElement('p');
        ticketPriceP.innerHTML = "<strong>Price:</strong> €" + event.ticketType.price;
        ticketPriceDiv.appendChild(ticketPriceP);
        //button
        let ticketButton = document.createElement('button');
        ticketButton.classList.add('btn', 'btn-primary', 'w-100');
        ticketButton.innerText = "Add to cart";
        ticketButton.addEventListener('click', () => {
            Cart.Add(event.id);
        });
        ticketPriceDiv.appendChild(ticketButton);
        ticketBody.appendChild(ticketPriceDiv);

        this.eventsContainer.appendChild(ticketContainer);
    }

    createDetailBox(name, value) {
        let container = document.createElement('div');
        container.classList.add('col-3');
        let header = document.createElement('h3');
        header.innerText = name;
        let text = document.createElement('p');
        text.innerHTML = value;
        container.appendChild(header);
        container.appendChild(text);
        return container;
    }

    removeEvent(event) {
        super.removeEvent(event);
        let eventContainer = document.getElementById('event-' + event.id);
        eventContainer.remove();
    }

    clearEvents() {
        super.clearEvents();
        this.eventsContainer.innerHTML = '';
    }
}
