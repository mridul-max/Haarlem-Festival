document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const emailField = document.getElementById("email");
    const passwordField = document.getElementById("password");
    const popup = document.getElementById("popup");
    const loginButton = document.getElementById("loginButton");
    const loginText = document.getElementById("loginText");
    const loginSpinner = document.getElementById("loginSpinner");

    // Function to display messages in the popup
    function displayMessage(message, type = "danger") {
        // Set message and style
        popup.textContent = message;
        popup.className = `alert alert-${type}`; // Bootstrap alert classes
        popup.style.display = "block";

        // Hide the popup after 5 seconds
        setTimeout(() => {
            popup.style.display = "none";
        }, 5000);
    }

    // Function to handle login
    async function attemptLogin() {
        // Check if all fields have input
        if (!emailField.value || !passwordField.value) {
            displayMessage("Please fill in all fields.", "danger");
            return;
        }

        // Show spinner and disable button
        loginText.textContent = "Logging in...";
        loginSpinner.style.display = "inline-block";
        loginButton.disabled = true;

        // Create data object
        const data = {
            email: emailField.value,
            password: passwordField.value
        };

        try {
            // Try to verify login
            const response = await fetch("/api/user/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });

            // Parse response
            const result = await response.json();

            // Handle response
            if (result.error_message) {
                displayMessage(result.error_message, "danger");
            } else {
                displayMessage(result.success_message, "success");
                // Redirect on successful login
                setTimeout(() => {
                    window.location.assign("/");
                }, 2000); // Redirect after 2 seconds
            }
        } catch (error) {
            console.error("Error:", error);
            displayMessage("An error occurred. Please try again.", "danger");
        } finally {
            // Hide spinner and re-enable button
            loginText.textContent = "Login";
            loginSpinner.style.display = "none";
            loginButton.disabled = false;
        }
    }

    // Attach event listener to login button
    loginButton.addEventListener("click", attemptLogin);
});