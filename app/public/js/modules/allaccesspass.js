class AllAccessPass {
    constructor(container) {
        this.container = container;
        this.build();
    }

    static start(id) {
        let container = document.getElementById(id);
        if (!container) {
            console.error('Could not load all access pass.');
            return;
        }

        return new AllAccessPass(container);
    }

    async build() {
        this.container.innerHTML = '';
        this.container.className = 'row col-12 d-flex justify-content-center mx-auto allday-pass px-0 festive-background';
        this.details = await this.getAllAccessPass(this.container.dataset.kind);

        // Add CSS styles
        this.addStyles();

        // Header Section
        let headerContainer = document.createElement('div');
        headerContainer.classList.add('header-container', 'text-center', 'col-12', 'col-xl-6', 'mx-auto');
        
        let header = document.createElement('h2');
        header.innerText = 'All-Access Pass';
        header.classList.add('header-title');
        headerContainer.appendChild(header);

        let description = document.createElement('p');
        description.innerText = 'Why settle for just one artist, if you can experience them all?';
        description.classList.add('header-description');
        headerContainer.appendChild(description);

        this.container.appendChild(headerContainer);

        // Details and Purchase Section
        let rowDetailsAndPurchase = document.createElement('div');
        rowDetailsAndPurchase.classList.add('row', 'col-12', 'col-xl-11', 'mx-auto', 'p-0', 'd-flex', 'justify-content-center', 'align-content-center');

        // Details Section
        let detailsContainer = document.createElement('div');
        detailsContainer.classList.add('col-12', 'col-xl-5', 'details-container');
        
        let detailsHeader = document.createElement('h3');
        detailsHeader.innerText = 'Perks';
        detailsHeader.classList.add('details-header');
        detailsContainer.appendChild(detailsHeader);

        let detailsList = document.createElement('ul');
        detailsList.classList.add('details-list');
        for (let perk of this.details.perks) {
            let perkItem = document.createElement('li');
            perkItem.innerText = perk;
            detailsList.appendChild(perkItem);
        }
        detailsContainer.appendChild(detailsList);
        rowDetailsAndPurchase.appendChild(detailsContainer);

        // Purchase Section
        let purchaseContainer = document.createElement('div');
        purchaseContainer.classList.add('col-12', 'col-xl-6', 'purchase-container');
        for (let pass of this.details.passes) {
            let passContainer = document.createElement('div');
            passContainer.classList.add('pass-container');

            let passName = document.createElement('h3');
            passName.innerText = pass.name;
            passContainer.appendChild(passName);

            let passPrice = document.createElement('p');
            passPrice.classList.add('price');
            passPrice.innerText = '€ ' + pass.price;
            passContainer.appendChild(passPrice);

            let passButton = document.createElement('button');
            passButton.classList.add('btn', 'btn-primary', 'add-to-cart-button');
            passButton.innerText = 'Add to cart';
            passButton.onclick = () => {
                this.addPassToCart(pass);
            }
            passContainer.appendChild(passButton);

            purchaseContainer.appendChild(passContainer);
        }

        rowDetailsAndPurchase.appendChild(purchaseContainer);
        this.container.appendChild(rowDetailsAndPurchase);
    }

    addStyles() {
        const style = document.createElement('style');
        style.innerHTML = `
            .festive-background {
                background: linear-gradient(135deg, #ffcc00, #ff6699);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }

            .header-container {
                margin-bottom: 20px;
            }

            .header-title {
                font-size: 2.5em;
                color: #d50000;
                margin-bottom: 10px;
            }

            .header-description {
                font-size: 1.2em;
                color: #333;
            }

            .details-container {
                background-color: rgba(255, 255, 255, 0.9);
                border-radius: 10px;
                padding: 15px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .details-header {
                font-size: 1.8em;
                color: #d50000;
                margin-bottom: 10px;
            }

            .details-list {
                list-style-type: none;
                padding: 0;
            }

            .details-list li {
                padding: 5px 0;
                font-size: 1.1em;
            }

            .purchase-container {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .pass-container {
                background-color: #fff;
                border-radius: 5px;
                padding: 15px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s;
            }

            .pass-container:hover {
                transform: scale(1.02);
            }

            .price {
                font-size: 1.2em;
                color: #007bff;
            }

            .add-to-cart-button {
                background-color: #d50000;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px;
                transition: background-color 0.3s;
            }

            .add-to-cart-button:hover {
                background-color: #a00000;
            }
        `;
        document.head.appendChild(style);
    }

    async getAllAccessPass(kind) {
        let obj = null;
        if (kind == 'jazz') {
            obj = {
                name: 'All-Access Jazz Pass',
                perks: [
                    'Pay once to access everything',
                    'Affordable way to experience more than one artist'
                ],
                passes: [
                    {
                        id: 7,
                        name: 'One-day pass',
                        price: 0
                    },
                    {
                        id: 8,
                        name: 'All-Day Pass',
                        price: 0
                    }
                ]
            }
        } 
        for (let pass of obj.passes) {
            let t = await fetch('/api/tickettypes/' + pass.id, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let data = await t.json();
            pass.price = data.price;
        }

        return obj;
    }

    addPassToCart(pass) {
        let kindId = -1;
        if (this.container.dataset.kind == 'jazz') {
            kindId = 1;
        }
        window.location.href = '/buyPass?event_type=' + kindId + "&pass_type=" + pass.id;
    }
}