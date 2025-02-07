//Fields
const emailField = document.getElementById("email");
const passwordField = document.getElementById("password");

//Popup window
var popup = document.getElementById("popup");

function attemptLogin(){

    //Clear popups
    popup.innerHTML = "";

    //Check if all fields have input
    if(!emailField.value || !passwordField.value){
        displayError("Please fill in all fields.");
        return;
    }

    //Create data object
    const data = {
        email: emailField.value,
        password: passwordField.value
    }
    
    //Try to verify login
    fetch("/api/user/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
        })
    .then(response => response.json())
    .then(data => {
            if(data.error_message){
                displayError(data.error_message);
                return;
            }
            displaySuccess(data.success_message);
            window.location.assign("/");
        })
    .catch(error => {displayError(error)});
}

function displayError(error){
    errorDiv = document.createElement("div");
    errorDiv.innerHTML = error;
    errorDiv.classList.add("alert");
    errorDiv.classList.add("alert-danger");
    errorDiv.classList.add("p-3");
    errorDiv.setAttribute("role", "alert");
    popup.appendChild(errorDiv);
}

function displaySuccess(success){
    successDiv = document.createElement("div");
    successDiv.innerHTML = success;
    successDiv.classList.add("alert");
    successDiv.classList.add("alert-success");
    successDiv.classList.add("p-3");
    successDiv.setAttribute("role", "alert");
    popup.appendChild(successDiv);
}   