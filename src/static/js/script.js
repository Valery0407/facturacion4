// Modern Pink Login - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    const loginError = document.getElementById('login-error');
    const loginBtn = document.getElementById('login-btn');
    const togglePasswordBtn = document.getElementById('togglePassword');
    
    // Toggle password visibility
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        const icon = togglePasswordBtn.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Form validation
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset errors
        emailError.textContent = '';
        passwordError.textContent = '';
        loginError.style.display = 'none';
        
        let isValid = true;
        
        // Validate email
        if (!emailInput.value.trim()) {
            emailError.textContent = 'Email is required';
            isValid = false;
        } else if (!isValidEmail(emailInput.value.trim())) {
            emailError.textContent = 'Please enter a valid email address';
            isValid = false;
        }
        
        // Validate password
        if (!passwordInput.value) {
            passwordError.textContent = 'Password is required';
            isValid = false;
        } else if (passwordInput.value.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            isValid = false;
        }
        
        if (isValid) {
            // Show loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            
            // Submit form via AJAX
            const formData = new FormData(loginForm);
            
            fetch('php/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to dashboard or home page
                    window.location.href = data.redirect || 'dashboard.php';
                } else {
                    // Show error message
                    loginError.textContent = data.message || 'Invalid email or password';
                    loginError.style.display = 'block';
                    
                    // Reset loading state
                    loginBtn.classList.remove('loading');
                    loginBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loginError.textContent = 'An error occurred. Please try again.';
                loginError.style.display = 'block';
                
                // Reset loading state
                loginBtn.classList.remove('loading');
                loginBtn.disabled = false;
            });
        }
    });
    
    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});