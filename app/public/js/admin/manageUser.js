
function addUser() {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const role = document.getElementById("role").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return;
    }

    const data = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        role: role,
        password: password
    }

    fetch("/api/user/addUser", {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    }).then(res => res.json())
        .then(data => {
            if (data.success_message) {
                alert(data.success_message)
                // Reload the page to show the updated list of users
                window.location.href = "/manageUsers";
            } else {
                alert(data.error_message)
            }
        })
        .catch(err => console.log(err))
}

function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        const data = {
            id: id
        }
        fetch("/api/user/deleteUser", {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        }).then(res => res.json())
            .then(data => {
                if (data.success_message) {
                    alert(data.success_message)
                    // Reload the page to show the updated list of users
                    location.reload();
                } else {
                    alert(data.error_message)
                }
            })
    }
    else {
        alert("User not deleted");
    }
}

function updateUser(id) {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const role = document.getElementById("role").value;

    const data = {
        id: id,
        firstName: firstName,
        lastName: lastName,
        email: email,
        role: role
    }
    fetch("/api/user/updateUser", {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    }).then(res => res.json())
        .then(data => {
            if (data.success_message) {
                alert(data.success_message)
                // Reload the page to show the updated list of users
                window.location.href = "/manageUsers";
            } else {
                alert(data.error_message)
            }
        }
        )
}
