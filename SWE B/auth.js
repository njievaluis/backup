
document.addEventListener('DOMContentLoaded', function() {
    const isLoggedIn = localStorage.getItem('isLoggedIn');

    if (!isLoggedIn || isLoggedIn !== 'true') {
        window.location.href = 'login.html';
    } else {
        const userEmail = localStorage.getItem('userEmail');
        console.log('Logged in as:', userEmail);

        const logoutLink = document.getElementById('logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('userEmail');
                window.location.href = 'login.html';
            });
        }
    }
});
