// Author: Konrad
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
        this.container.className = 'row col-12 d-flex justify-content-center mx-auto allday-pass px-0';
        this.details = await this.getAllAccessPass(this.container.dataset.kind);

        let headerContainer = document.createElement('div');
        headerContainer.classList.add('row', 'col-12', 'col-xl-6', 'col-xl-5', 'mx-auto', 'text-center');
        let header = document.createElement('h2');
        header.innerText = 'All-Access Pass';
        headerContainer.appendChild(header);

        let hr = document.createElement('hr');
        headerContainer.appendChild(hr);

        let description = document.createElement('p');
        description.innerText = 'Why settle for just one artist, if you can experience them all?';
        headerContainer.appendChild(description);

        this.container.appendChild(headerContainer);

        let rowDetailsAndPurchase = document.createElement('div');
        rowDetailsAndPurchase.classList.add('row', 'col-12', 'col-xl-11', 'mx-auto', 'p-0', 'd-flex', 'justify-content-center', 'align-content-center');

        let detailsContainer = document.createElement('div');
        detailsContainer.classList.add('col-12', 'col-xl-5');
        let detailsHeader = document.createElement('h3');
        detailsHeader.classList.add('text-center', 'text-xl-start');
        detailsHeader.innerText = 'Perks';
        detailsContainer.appendChild(detailsHeader);
        let hr2 = document.createElement('hr');
        hr2.classList.add('d-block', 'd-xl-none', 'col-12');
        detailsContainer.appendChild(hr2);
        let detailsList = document.createElement('ul');

        for (let perk of this.details.perks) {
            let perkItem = document.createElement('li');
            perkItem.innerText = perk;
            detailsList.appendChild(perkItem);
        }

        detailsContainer.appendChild(detailsList);

        let hrForSmall = document.createElement('hr');
        hrForSmall.classList.add('d-block', 'd-xl-none', 'col-12');
        detailsContainer.appendChild(hrForSmall);

        rowDetailsAndPurchase.appendChild(detailsContainer);

        let vr = document.createElement('div');
        vr.classList.add('col-1', 'h-100', 'my-auto', 'px-0', 'd-xl-flex', 'justify-content-center', 'd-none');
        vr.innerHTML = '<div class="vr" style="height: 110px"></div>';
        rowDetailsAndPurchase.appendChild(vr);

        let purchaseContainer = document.createElement('div');
        purchaseContainer.classList.add('col-12', 'col-xl-6');
        for (let pass of this.details.passes) {
            let passContainer = document.createElement('div');
            passContainer.classList.add('row', 'd-inline', 'my-0');

            let passName = document.createElement('h3');
            passName.classList.add('d-inline');
            passName.innerText = pass.name;
            passContainer.appendChild(passName);

            let passPrice = document.createElement('p');
            passPrice.classList.add('d-inline', 'price');
            passPrice.innerText = '€ ' + pass.price;
            passContainer.appendChild(passPrice);

            let passButton = document.createElement('button');
            passButton.classList.add('btn', 'btn-primary', 'w-100', 'w-sm-auto', 'float-none', 'float-md-end');
            passButton.innerText = 'Add to cart';
            passButton.onclick = () => {
                this.addPassToCart(pass);
            }
            passContainer.appendChild(passButton);

            purchaseContainer.appendChild(passContainer);

            if (pass != this.details.passes[this.details.passes.length - 1]) {
                let hr3 = document.createElement('hr');
                hr3.classList.add('my-1');
                purchaseContainer.appendChild(hr3);
            }
        }

        rowDetailsAndPurchase.appendChild(purchaseContainer);
        this.container.appendChild(rowDetailsAndPurchase);
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
        } else if (kind == 'dance') {
            obj = {
                name: 'All-Access Dance Pass',
                perks: [
                    'Pay once to access everything',
                    'Affordable way to experience more than one artist'
                ],
                passes: [
                    {
                        id: 10,
                        name: 'Friday Pass',
                        price: 0
                    },
                    {
                        id: 11,
                        name: 'Weekend Day Pass',
                        price: 0
                    },
                    {
                        id: 13,
                        name: 'All Day Pass',
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
            let data = await t.json()
            pass.price = data.price;
        }

        return obj;
    }

    addPassToCart(pass) {
        // redirect to /buyPass
        let kindId = -1;
        if (this.container.dataset.kind == 'jazz') {
            kindId = 1;
        } else if (this.container.dataset.kind == 'dance') {
            kindId = 4;
        }

        window.location.href = '/buyPass?event_type=' + kindId + "&pass_type=" + pass.id;
    }
}