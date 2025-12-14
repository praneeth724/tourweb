// Auth Page JavaScript - Login & Register

// Tab switching
const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

loginTab.addEventListener('click', () => {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
    loginForm.classList.add('slide-in');
});

registerTab.addEventListener('click', () => {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
    registerForm.classList.add('slide-in');
});

// Login Form Submission
document.getElementById('loginFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';
    submitBtn.disabled = true;

    const formData = {
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value
    };

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Success! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = result.redirect || 'index.html';
            }, 1500);
        } else {
            throw new Error(result.message || 'Login failed');
        }

    } catch (error) {
        console.error('Login error:', error);
        showNotification(error.message, 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Register Form Submission
document.getElementById('registerFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();

    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('registerConfirmPassword').value;

    // Validate password match
    if (password !== confirmPassword) {
        showNotification('Passwords do not match!', 'error');
        return;
    }

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating account...';
    submitBtn.disabled = true;

    const formData = {
        fullName: document.getElementById('registerName').value,
        username: document.getElementById('registerUsername').value,
        email: document.getElementById('registerEmail').value,
        password: password
    };

    try {
        const response = await fetch('register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Account created successfully! Please login.', 'success');

            // Clear form
            document.getElementById('registerFormElement').reset();

            // Switch to login tab after 2 seconds
            setTimeout(() => {
                loginTab.click();
                // Pre-fill email in login form
                document.getElementById('loginEmail').value = formData.email;
            }, 2000);
        } else {
            throw new Error(result.message || 'Registration failed');
        }

    } catch (error) {
        console.error('Registration error:', error);
        showNotification(error.message, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'notification fixed top-5 right-5 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 slide-in';

    let bgColor, icon;
    if (type === 'success') {
        bgColor = 'bg-green-500';
        icon = 'fa-check-circle';
    } else if (type === 'error') {
        bgColor = 'bg-red-500';
        icon = 'fa-exclamation-circle';
    } else {
        bgColor = 'bg-blue-500';
        icon = 'fa-info-circle';
    }

    notification.classList.add(bgColor);
    notification.innerHTML = `
        <i class="fas ${icon} text-white text-xl"></i>
        <span class="text-white font-semibold">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Password strength indicator (for register form)
document.getElementById('registerPassword')?.addEventListener('input', (e) => {
    const password = e.target.value;
    let strength = 0;

    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    // You can add a strength meter UI here if desired
    console.log('Password strength:', strength);
});

console.log('Auth system initialized');
