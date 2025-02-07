// Author: Konrad
const HAARLEM_LOCATION = [52.3814425, 4.6360367]; // I don't think Haarlem is going to move anytime soon.
const DEFAULT_ZOOM_LEVEL = 16;

class HMap {
    constructor(container) {
        this.container = container;
        this.map = null;
        this.areas = [];
        this.pins = [];
        this.L = null;

        this.load();
    }

    static start(id) {
        let container = document.getElementById(id);
        if (!container) {
            console.error('Could not find map container.');
            return;
        }

        switch (container.dataset.mapkind) {
            case 'general':
                return new GeneralMap(container);
            case 'event':
                return new EventMap(container);
            default:
                break;
        }

        console.error('Could not find map kind.');
    }

    async load() {
        this.L = await this.loadLeaflet();
        if (!this.L) {
            console.error('Could not load map.');
            return;
        }
        console.log('L defined. Loading map...');
        this.loadMap(L);
    }

    loadLeaflet() {
        return new Promise((resolve) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = 'https://unpkg.com/leaflet@1.9.3/dist/leaflet.css';
            link.media = 'all';
            document.getElementsByTagName('head')[0].appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.3/dist/leaflet.js';
            script.type = 'text/javascript';
            script.onload = () => resolve(L);
            document.getElementsByTagName('head')[0].appendChild(script);
        });
    }

    loadMap(L) {
        console.log('Overwrite this function to load the map.');
    }

    addArea(name, coordinates, color) {
        let area = this.L.polygon(coordinates, {
            color: color,
            fillColor: color,
            fillOpacity: 0.5
        }).addTo(this.map);
        area.bindPopup(name);

        this.areas.push(area);
    }

    clearAreas() {
        this.areas.forEach(area => {
            this.map.removeLayer(area);
        });
        this.areas = [];
    }

    /**
     * Adds a pin to the map.
     * @param {*} name Name displayed
     * @param {*} location [longtitude, latitude]
     */
    addPin(markerContent, location) {
        let pin = L.marker(location).addTo(this.map).bindPopup(markerContent);
        this.pins.push(pin);
        return pin;
    }

    addPinNoContent(location) {
        console.log(location);
        let pin = L.marker(location).addTo(this.map);
        this.pins.push(pin);
        return pin;
    }

    clearPins() {
        this.pins.forEach(pin => {
            this.map.removeLayer(pin);
        });
        this.pins = [];
    }

    moveMap(location, zoom) {
        this.map.panTo(location, { animate: false }); // Can't animate, because the zoom fucks up the animation.
        this.map.setZoom(zoom);
    }
}

class GeneralMap extends HMap {
    constructor(container) {
        super(container);
    }

    loadMap(L) {
        // Create a map with a general location.
        let colViews = document.createElement('div');
        colViews.classList.add('col-0', 'col-md-2');
        let h3 = document.createElement('h3');
        h3.innerText = 'Check out the locations per event';
        colViews.appendChild(h3);

        let btnLayers = document.createElement('button');
        btnLayers.classList.add('btn', 'btn-primary', 'd-block', 'd-md-none', 'collapsed');
        btnLayers.setAttribute('data-bs-toggle', 'collapse');
        btnLayers.setAttribute('data-bs-target', '#mapCollapseLayers');
        btnLayers.setAttribute('aria-expanded', 'false');
        btnLayers.setAttribute('aria-controls', 'mapCollapseLayers');
        btnLayers.innerText = 'Views';
        colViews.appendChild(btnLayers);

        let divCollapse = document.createElement('div');
        divCollapse.classList.add('w-100', 'list-group', 'collapsed', 'collapse', 'd-md-flex');
        divCollapse.id = 'mapCollapseLayers';
        colViews.appendChild(divCollapse);

        let buttons = [
            { name: 'Overview', function: () => { this.showOverview(); } },
            { name: 'DANCE!', function: () => { this.showDance(); } },
            { name: 'Haarlem Jazz', function: () => { this.showJazz() } },
            { name: 'Stroll Through Haarlem', function: () => { this.showStroll() } },
            { name: 'Yummy!', function: () => { this.showYummy() } },
            { name: 'The Teyler Mystery', function: () => { this.showTeyler() } },
        ]

        buttons.forEach(button => {
            let btn = document.createElement('button');
            btn.classList.add('list-group-item', 'list-group-item-action');
            btn.innerText = button.name;
            btn.onclick = () => {
                // Get buttons with active class and remove it.
                let activeButtons = document.querySelectorAll('.list-group-item.active');
                activeButtons.forEach(activeButton => {
                    activeButton.classList.remove('active');
                });
                // Make this active.
                btn.classList.add('active');
                this.clearAreas();
                button.function();
            }
            divCollapse.appendChild(btn);
        });

        let mapDiv = document.createElement('div');
        mapDiv.id = 'map';
        mapDiv.classList.add('col-12', 'col-md-10');

        this.container.appendChild(colViews);
        this.container.appendChild(mapDiv);

        this.map = L.map('map').setView(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(this.map);
    }

    showOverview() {
        const LOCATION = [52.393306, 4.622498];
        const ZOOM = 14;
        this.moveMap(LOCATION, ZOOM);
        this.clearPins();

        this.clearAreas();

        this.addArea('Festival Area', [
            [52.385553700259415, 4.631949663162232],
            [52.38434879837112, 4.644438028335572],
            [52.38624781359209, 4.650413990020753],
            [52.38550786220234, 4.651283025741578],
            [52.38340580873727, 4.644362926483155],
            [52.3812447148585, 4.64674472808838],
            [52.37933238600426, 4.642689228057862],
            [52.38026236449064, 4.640800952911378],
            [52.37939787808805, 4.637947082519532],
            [52.37791773328495, 4.638075828552247],
            [52.37688291231777, 4.639331102371217],
            [52.37598561121694, 4.63626265525818],
            [52.37627379749958, 4.629557132720948],
            [52.37749856822017, 4.6264779567718515],
            [52.37787843672914, 4.624385833740235],
            [52.383857660450126, 4.629181623458863],
            [52.38364810660761, 4.630469083786012]
        ], '#4943A0');

        this.addArea('DANCE! Venue', [
            [52.411712914646635, 4.60553526878357],
            [52.41157752977527, 4.608570212417496],
            [52.40949635203989, 4.607754820876969],
            [52.40981049836807, 4.6043215933379065]
        ], '#4943A0');
    }
        showDance() {
        this.moveMap(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        this.clearPins()
    }

    loadPinsForType(type) {
        fetch('/api/locations/type/' + type, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(locations => {
                locations.forEach(location => {
                    // add marker

                    // add marker and popup
                    let markerContent = `<h3>${location.name}</h3>`;
                    // add google maps link
                    if (location.address) {
                        let fullAddress = `${location.address.streetName} ${location.address.houseNumber}, ${location.address.postalCode} ${location.address.city}`;
                        markerContent += `<a href="https://www.google.com/maps/search/?api=1&query=${location.name} ${location.address.streetName} ${location.address.houseNumber} ${location.address.postalCode} "
                        target="_blank">${fullAddress}</a>`;
                    }
                    this.addPin(markerContent, [location.lat, location.lon]);
                });
            }
            );
    }

    showJazz() {
        this.moveMap(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        this.clearPins();
        this.loadPinsForType(1);
    }

    showStroll() {
        this.moveMap(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        this.clearPins();
        this.loadPinsForType(3);
    }

    showYummy() {
        this.moveMap(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        this.clearPins();
        this.loadPinsForType(2);
    }

    showTeyler() {
        this.moveMap(HAARLEM_LOCATION, DEFAULT_ZOOM_LEVEL);
        this.clearPins();
    }
}

class EventMap extends HMap {
    constructor(container) {
        super(container);
    }

    loadMap(L) {
        // Get data.lon and data.lat from the container
        // First check if they are se
        let data = this.container.dataset;

        if (!data.lon || !data.lat) {
            console.error('No location data found');
            return;
        }

        let div = document.createElement('div');
        div.id = 'map';
        div.classList.add('col-12');
        this.container.appendChild(div);

        this.map = L.map('mapContainer').setView([data.lat, data.lon], DEFAULT_ZOOM_LEVEL);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(this.map);

        // disable map's zoom
        this.map.scrollWheelZoom.disable();

        // create pin
        const pin = this.addPinNoContent([data.lat, data.lon]);

        // on pin click, navigate to Google Maps
        this.map.on('click', () => {
            window.open(`https://www.google.com/maps/search/?api=1&query=${data.name} ${data.street}`)
        });
    }

    showEvent(event) {
        this.moveMap([event.location.lat, event.location.lon], DEFAULT_ZOOM_LEVEL);
    }
}
