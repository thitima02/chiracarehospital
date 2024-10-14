function handleLogout() {
    fetch('../../backend/sql/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '../login.html';
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
