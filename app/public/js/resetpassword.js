const emailField = document.getElementById("emailField");

function resetPassword() {
    const data = {
        email: emailField.value
    }

    fetch("/api/user/resetPassword", {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    }).then(res => res.json())
        .then(data => {
            if (data.success_message) {
                alert(data.success_message)
            } else {
                alert(data.error_message)
            }
        })
};
