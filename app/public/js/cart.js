//
// METHODS:
// Cart.Add(itemID) - adds one item to the cart
// Cart.Remove(itemID) - removes one item from the cart
// Cart.UpdateCounter() - updates the counter of items in the cart
// Cart.Get() - returns the cart object
// Cart.Delete(itemId) - deletes the item from the cart (all instances of it)



// load admin/modals.js
function createToast(header, msg) {
    // Create bootstrap toast
    let toast = document.createElement('div');
    toast.classList.add('toast');
    toast.style.position = 'fixed';
    toast.style.zIndex = 9999;
    toast.style.left = '30px';
    toast.style.top = '30px';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-bs-delay', '3000');
    toast.setAttribute('data-bs-autohide', 'true');

    // Create header
    let toastHeader = document.createElement('div');
    toastHeader.classList.add('toast-header');
    toastHeader.innerHTML = header;

    // Create body
    let toastBody = document.createElement('div');
    toastBody.classList.add('toast-body');
    toastBody.innerHTML = msg;

    // Append header and body to toast
    toast.appendChild(toastHeader);
    toast.appendChild(toastBody);

    // Append toast to the beginning of the body
    document.body.insertBefore(toast, document.body.firstChild);

    // Show toast
    let toastElement = new bootstrap.Toast(toast);
    toastElement.show();
}

(function () {
    const apiUrl = '/api/cart';
    var Cart = {};

    //Adds one item to the cart order
    Cart.Add = function(itemID) {
        const url = apiUrl + "/add/" + itemID;
        return new Promise((resolve, reject) => {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                Cart.UpdateCounter();
                resolve(data);
            })
            .catch(error => {
                console.error('Add item error:', error);
                createToast('Error', error.message);
                reject(error);
            });
        });
    };
    //Removes one item from the cart order
    Cart.Remove = function (itemID) {

        const url = apiUrl + '/remove/' + itemID;
        return new Promise((resolve, reject) => {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    Cart.UpdateCounter();
                    resolve(data);
                }
                )
                .catch(error => {
                    reject(error);
                }
                );
        });
    }
    //Gets the cart order from the server
    Cart.Get = function () {
        const url = apiUrl;
        return new Promise((resolve, reject) => {
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    resolve(data);
                }
                )
                .catch(error => {
                    reject(error);
                }
                );
        });
    }

    Cart.UpdateCounter = function() {
        fetch(apiUrl + '/count', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Handle non-JSON error responses
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            const cartCircle = document.querySelector('#shopping-circle');
            const cartCircleText = document.querySelector('#shopping-circle-text');
            
            if (data.count > 0) {
                cartCircle.classList.remove('d-none');
            } else {
                cartCircle.classList.add('d-none');
            }
            cartCircleText.textContent = data.count;
            this.count = data.count;
        })
        .catch(error => {
            console.error('Cart count error:', error);
            createToast('Cart Error', error.message);
        });
    };

    Cart.Delete = function (itemId) {
        return new Promise((resolve, reject) => {
            fetch(apiUrl + '/item/' + itemId,
                {
                    method: 'DELETE'
                }).then(response => response.json())
                .then(data => {
                    Cart.UpdateCounter();
                    resolve(data);
                }
                ).catch(error => {
                    reject(error);
                }
                );
        });
    }

    Cart.Checkout = function() {
        return new Promise((resolve, reject) => {
            fetch(apiUrl + '/checkout', {
                method: 'POST', // Explicitly set to POST
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                Cart.UpdateCounter();
                resolve(data);
            })
            .catch(error => {
                console.error('Checkout error:', error);
                createToast('Checkout Failed', error.message);
                reject(error);
            });
        });
    };


    window.Cart = Cart;
    console.log('Cart.js loaded.');
})();