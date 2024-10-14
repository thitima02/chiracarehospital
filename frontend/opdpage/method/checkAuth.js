// checkAuth.js
function checkAuth() {
    if (!localStorage.getItem('token')) {
        window.location.href = "../login.html";
    }
}
