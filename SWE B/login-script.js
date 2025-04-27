document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (email && password) {

            localStorage.setItem('userEmail', email);
            localStorage.setItem('isLoggedIn', 'true');
            
            window.location.href = 'index.html';
        } else {
            alert('Please enter both email and password');
        }
    });
});