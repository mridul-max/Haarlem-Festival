
function updatePassword() {

    const newPassword = document.getElementById("new-password");
    const confirmPassword = document.getElementById("confirm-password");
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    const email = params.get('email');
    const data = {
        newPassword: newPassword.value,
        confirmPassword: confirmPassword.value,
        token: token,
        email: email
    };
    fetch("/api/user/updatePassword", {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    }).then(res => res.json())
        .then(data => {
            if (data.success_message) {
                alert(data.success_message);
                window.location.href = "/home/login";
            } else {
                alert(data.error_message)
            }
        })
}