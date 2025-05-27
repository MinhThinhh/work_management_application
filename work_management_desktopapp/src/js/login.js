// Login functionality
class LoginManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', this.handleLogin.bind(this));
        }
    }

    async handleLogin(event) {
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('error');
        const submitButton = event.target.querySelector('button[type="submit"]');

        // Clear previous errors
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }

        // Disable submit button
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Đang đăng nhập...';
        }

        try {
            console.log('Attempting login...');
            const result = await window.api.login({ email, password });
            console.log('Login result:', result);

            if (result.success) {
                console.log('Login successful, redirecting to tasks page');
                window.location.href = 'tasks.html';
            } else {
                this.showError(result.error || 'Đăng nhập thất bại');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showError('Đã xảy ra lỗi khi đăng nhập');
        } finally {
            // Re-enable submit button
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Đăng nhập';
            }
        }
    }

    showError(message) {
        const errorDiv = document.getElementById('error');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LoginManager();
});
