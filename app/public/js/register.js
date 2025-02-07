//Personal information fields
const firstNameField = document.getElementById("firstName");
const lastNameField = document.getElementById("lastName");
const doBField = document.getElementById("dateOfBirth");
const phoneNumberField = document.getElementById("phoneNumber");

//Address fields
const streetNameField = document.getElementById("streetName");
const houseNumberField = document.getElementById("houseNumber");
const extensionField = document.getElementById("extension");
const postalCodeField = document.getElementById("postalCode");
const cityField = document.getElementById("city");
const countryField = document.getElementById("country");

//Account information fields
const emailField = document.getElementById("email");
const passwordField = document.getElementById("password");
const passwordConfirmField = document.getElementById("passwordConfirm");

//Popup window
var popup = document.getElementById("popup");


function attemptRegister(captcha) {
    //Clear popups
    popup.innerHTML = "";

    if (!allFieldsFilled()) {
        displayError("Please fill in all fields");
        return;
    }
    else if (!checkPassword()) {
        displayError("Confirmed password does not match.");
        return;
    }
    else if (!document.getElementById("termsAcceptance").checked) {
        displayError("You must agree to our terms and conditions.");
        return;
    }

    const data = {
        firstName: firstNameField.value,
        lastName: lastNameField.value,
        dateOfBirth: doBField.value,
        phoneNumber: phoneNumberField.value,
        email: emailField.value,
        password: passwordField.value,
        address: createAddressObject(),
        captchaResponse: captcha
    }

    fetch("/api/user/register", {
        method: "POST",
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.error_message) {
                displayError(data.error_message);
                return;
            }
            window.location.assign("/home/login");
        })
        .catch(error => { displayError(error) });
}

function allFieldsFilled() {
    return !(!firstNameField.value || !lastNameField.value || !doBField.value || !phoneNumberField.value || !streetNameField.value
        || !houseNumberField.value || !postalCodeField.value || !cityField.value || !countryField.value || !emailField.value
        || !passwordField.value || !passwordConfirmField.value);
}

function checkPassword() {
    return (passwordField.value == passwordConfirmField.value);
}

function createAddressObject() {
    var housenumber = houseNumberField.value + " " + extensionField.value;
    var postCode = postalCodeField.value.replace(" ", "");

    var address = {
        streetName: streetNameField.value,
        houseNumber: housenumber,
        postalCode: postCode,
        city: cityField.value,
        country: countryField.value
    }

    return address;
}

function fetchAddress() {
    var postalCode = postalCodeField.value.replace(" ", "");
    var houseNumber = houseNumberField.value;

    fetch("/api/address/fetch-address?postalCode=" + postalCode + "&houseNumber=" + houseNumber, {
        method: "GET"
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.street) {
                streetNameField.value = data.street;
                cityField.value = data.city;
                countryField.value = "Netherlands";
            }
        })
        .catch(error => { console.log(error) });
}

function displayError(error) {
    errorDiv = document.createElement("div");
    errorDiv.innerHTML = error;
    errorDiv.classList.add("alert");
    errorDiv.classList.add("alert-danger");
    errorDiv.classList.add("p-3");
    errorDiv.setAttribute("role", "alert");
    popup.appendChild(errorDiv);
}

function displaySuccess(success) {
    successDiv = document.createElement("div");
    successDiv.innerHTML = success;
    successDiv.classList.add("alert");
    successDiv.classList.add("alert-success");
    successDiv.classList.add("p-3");
    successDiv.setAttribute("role", "alert");
    popup.appendChild(successDiv);
}