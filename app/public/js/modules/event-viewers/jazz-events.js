class JazzEventList extends EventsList {
    constructor(container) {
        super(container);
        this.loadEvents();
    }

    async init(container) {
        super.init(container);

        // SORT
        let sortRow = document.createElement('div');
        sortRow.classList.add('row');
        let sortHeader = document.createElement('h2');
        sortHeader.innerText = 'Sort';
        let sorts = document.createElement('select');
        sorts.classList.add('form-select');
        sorts.addEventListener('change', (e) => {
            this.sortMethod = e.target.value;
            this.loadEvents();
        });

        let sortOptions = [
            { value: 'time', text: 'Time ascending' },
            { value: 'time_desc', text: 'Time descending' },
            { value: 'price', text: 'Price ascending' },
            { value: 'price_desc', text: 'Price descending' }
        ];
        for (let option of sortOptions) {
            let sortOption = document.createElement('option');
            sortOption.value = option.value;
            sortOption.innerText = option.text;
            sorts.appendChild(sortOption);
        }

        sortRow.appendChild(sortHeader);
        sortRow.appendChild(sorts);
        this.sortingContainer.appendChild(sortRow);

        // FILTER
        // Filter Time
        let timeFilter = document.createElement('div');
        timeFilter.classList.add('row');
        let timeHeader = document.createElement('h2');
        timeHeader.innerText = 'Time';
        //start
        let timeStartHeader = document.createElement('h3');
        timeStartHeader.innerText = 'From';
        let timeStart = document.createElement('select');
        timeStart.classList.add('form-select');
        timeStart.addEventListener('change', (e) => {
            this.timeStart = e.target.value;
            this.loadEvents();
        });
        //end
        let timeEndHeader = document.createElement('h3');
        timeEndHeader.innerText = 'To';
        let timeEnd = document.createElement('select');
        timeEnd.classList.add('form-select');
        timeEnd.addEventListener('change', (e) => {
            this.timeEnd = e.target.value;
            this.loadEvents();
        });

        // Day Filter
        // add radio buttons for each day between 27th of July to 30th of July
        let dayFilter = document.createElement('div');
        dayFilter.classList.add('row');
        let dayHeader = document.createElement('h2');
        dayHeader.innerText = 'Day';
        let days = document.createElement('div');
        days.classList.add('d-block');

        let dates = await fetch('/api/events/dates?eventType=1').then((res) => res.json());

        // convert them to date objects
        dates = dates.map((date) => new Date(date));
        // sort
        dates.sort((a, b) => a - b);

        for (let date of dates) {
            let day = document.createElement('div');
            day.classList.add('form-check');

            let dayInput = document.createElement('input');
            dayInput.classList.add('form-check-input');
            dayInput.type = 'radio';
            dayInput.name = 'day';
            dayInput.value = date.toISOString().split('T')[0].split('-')[2];
            dayInput.id = 'day-' + dayInput.value;
            let dayLabel = document.createElement('label');
            dayLabel.classList.add('form-check-label');
            dayLabel.innerText = date.toDateString();
            dayLabel.htmlFor = 'day-' + dayInput.value;

            day.appendChild(dayInput);
            day.appendChild(dayLabel);
            days.appendChild(day);

            // allow unselecting the day
            dayInput.addEventListener('click', (e) => {
                let d = date.toISOString().split('T')[0].split('-')[2];
                if (e.target.checked && this.day === e.target.value) {
                    document.querySelector('input[name="day"]:checked').checked = false;
                    this.day = null
                } else {
                    this.day = d;
                }
                this.loadEvents();
            });
        }

        dayFilter.appendChild(dayHeader);
        dayFilter.appendChild(days);
        this.sortingContainer.appendChild(dayFilter);

        // add hours from 0 to 23
        for (let i = 0; i < 24; i++) {
            let optionStart = document.createElement('option');
            let optionEnd = document.createElement('option');
            optionStart.value = optionEnd.value = i;
            optionStart.innerText = optionEnd.innerText = i + ':00';
            timeStart.appendChild(optionStart);
            timeEnd.appendChild(optionEnd);
        }

        timeFilter.appendChild(timeHeader);
        timeFilter.appendChild(timeStartHeader);
        timeFilter.appendChild(timeStart);
        timeFilter.appendChild(timeEndHeader);
        timeFilter.appendChild(timeEnd);
        this.sortingContainer.appendChild(timeFilter);

        // Price
        let priceFilter = document.createElement('div');
        priceFilter.classList.add('row');
        let priceHeader = document.createElement('h2');
        priceHeader.innerText = 'Price';
        let priceFromHeader = document.createElement('h3');
        priceFromHeader.innerText = 'From';
        let priceFrom = document.createElement('input');
        priceFrom.type = 'number';
        priceFrom.classList.add('form-control');
        priceFrom.addEventListener('change', (e) => {
            this.priceFrom = e.target.value;
            this.loadEvents();
        }
        );

        let priceToHeader = document.createElement('h3');
        priceToHeader.innerText = 'To';
        let priceTo = document.createElement('input');
        priceTo.type = 'number';
        priceTo.classList.add('form-control');
        priceTo.addEventListener('change', (e) => {
            this.priceTo = e.target.value;
            this.loadEvents();
        }
        );

        priceFilter.appendChild(priceHeader);
        priceFilter.appendChild(priceFromHeader);
        priceFilter.appendChild(priceFrom);
        priceFilter.appendChild(priceToHeader);
        priceFilter.appendChild(priceTo);
        this.sortingContainer.appendChild(priceFilter);

        // Attributes
        let attributesFilter = document.createElement('div');
        attributesFilter.classList.add('row');
        let attributesHeader = document.createElement('h2');
        attributesHeader.innerText = 'Attributes';
        // hide events without seats checkbox
        let hideWithoutSeatsDiv = document.createElement('div');
        hideWithoutSeatsDiv.classList.add('form-check');
        let hideWithoutSeats = document.createElement('input');
        hideWithoutSeats.type = 'checkbox';
        hideWithoutSeats.classList.add('form-check-input');
        hideWithoutSeats.addEventListener('change', (e) => {
            this.hideWithoutSeats = e.target.checked;
            this.loadEvents();
        }
        );
        let hideWithoutSeatsLabel = document.createElement('label');
        hideWithoutSeatsLabel.classList.add('form-check-label');
        hideWithoutSeatsLabel.innerText = 'Hide events without seats';
        hideWithoutSeatsLabel.htmlFor = 'hideWithoutSeats';

        attributesFilter.appendChild(attributesHeader);
        hideWithoutSeatsDiv.appendChild(hideWithoutSeats);
        hideWithoutSeatsDiv.appendChild(hideWithoutSeatsLabel);
        attributesFilter.appendChild(hideWithoutSeatsDiv);
        this.sortingContainer.appendChild(attributesFilter);
    }

    async getData() {
        // if sort by is set
        let url = '/api/events/jazz';
        let args = '';

        function addArg(arg) {
            if (args == '') {
                args += `?${arg}`;
            } else {
                args += `&${arg}`;
            }
        }

        if (this.sortMethod) {
            addArg(`sort=${this.sortMethod}`);
            console.log(this.sortMethod);
        }

        // if time_start is set
        if (this.timeStart) {
            addArg(`time_from=${this.timeStart}`);
        }
        if (this.timeEnd) {
            addArg(`time_to=${this.timeEnd}`);
        }

        // if price_from is set
        if (this.priceFrom) {
            addArg(`price_from=${this.priceFrom}`);
        }
        if (this.priceTo) {
            addArg(`price_to=${this.priceTo}`);
        }

        // if hide_without_seats is set
        if (this.hideWithoutSeats) {
            addArg(`hide_without_seats`);
        }

        if (this.day) {
            addArg(`day=${this.day}`);
        }

        let response = await fetch(url + args);
        let data = await response.json();
        return data;
    }

    async loadEvents() {
        let data = await this.getData();

        this.clearEvents();
        if (data.length == 0) {
            this.noEvents();
        } else {
            for (let event of data) {
                this.addEvent(event);
            }
        }
    }

    addEvent(event) {
        super.addEvent(event);

        let eventContainer = document.createElement('div');
        eventContainer.classList.add('row', 'card');
        eventContainer.id = 'event-' + event.id;

        let rowTitle = document.createElement('div');
        rowTitle.classList.add('row');
        let title = document.createElement('h2');
        title.innerText = event.event.name;
        rowTitle.appendChild(title);

        let rowDetails = document.createElement('div');
        rowDetails.classList.add('row');

        rowDetails.appendChild(this.createDetailBox('Location', event.event.location.name));

        const startTime = new Date(event.event.startTime.date);
        const endTime = new Date(event.event.endTime.date);
        const startHour = startTime.getHours();
        const endHour = endTime.getHours();
        const startMinutes = startTime.getMinutes();
        const endMinutes = endTime.getMinutes();
        const startMinutesString = startMinutes < 10 ? '0' + startMinutes : startMinutes;
        const endMinutesString = endMinutes < 10 ? '0' + endMinutes : endMinutes;
        const displayTime = `${startTime.toDateString()}<br> ${startHour}:${startMinutesString} - ${endHour}:${endMinutesString}`;
        rowDetails.appendChild(this.createDetailBox('Time', displayTime));

        if (event.ticketType.price > 0) {
            const availableTickets = event.event.availableTickets;
            if (availableTickets <= 0) {
                rowDetails.appendChild(this.createDetailBox('Seats', 'Sold out'));
            } else {
                rowDetails.appendChild(this.createDetailBox('Seats', availableTickets + " / " + event.event.location.capacity));
            }
        }
        let price = this.createDetailBox('Price', event.ticketType.price == 0 ? "FREE" : "€ " + event.ticketType.price)
        price.classList.add('price');
        rowDetails.appendChild(price);

        // buttons row
        let rowButtons = document.createElement('div');
        rowButtons.classList.add('row', 'justify-content-end', 'py-2', 'gx-2', 'px-0');
        if (event.ticketType.price == 0 || event.event.availableTickets > 0) {
            let buyButton = document.createElement('button');
            buyButton.classList.add('btn', 'btn-primary', 'col-3');
            buyButton.innerText = event.ticketType.price == 0 ? 'Book a ticket' : 'Add ticket to cart';
            buyButton.addEventListener('click', () => {
                this.addToCart(event.id);
            });
            rowButtons.appendChild(buyButton);
        }
        let buttonDetailsA = document.createElement('a');
        buttonDetailsA.href = `/festival/jazz/event/${event.event.id}`;
        buttonDetailsA.classList.add('col-3');
        let buttonDetails = document.createElement('button');
        buttonDetails.classList.add('btn', 'btn-secondary', 'w-100');
        buttonDetails.innerText = 'About event';
        buttonDetailsA.appendChild(buttonDetails);
        //rowButtons.appendChild(amountInput);

        rowButtons.appendChild(buttonDetailsA);


        eventContainer.appendChild(rowTitle);
        eventContainer.appendChild(rowDetails);
        eventContainer.appendChild(rowButtons);

        this.eventsContainer.appendChild(eventContainer);
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