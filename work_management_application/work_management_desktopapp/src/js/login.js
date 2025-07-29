// Login functionality
class LoginManager {
    constructor() {
        this.init();
    }

    async init() {
        this.bindEvents();
        await this.checkApiConnection();
    }

    async checkApiConnection() {
        try {
            const isConnected = await window.api.checkApiConnection();
            if (!isConnected) {
                this.showError('Cannot connect to API server. Please make sure the server is running.');
            } else {
                // Hide any existing error messages
                const errorDiv = document.getElementById('error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Error checking API connection:', error);
            this.showError('Cannot connect to API server. Please make sure the server is running.');
        }
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

        // Show loading state
        const loading = document.getElementById('loading');
        const originalButtonText = submitButton.innerHTML;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Đang đăng nhập...';
        }
        if (loading) loading.style.display = 'block';

        try {
            console.log('Attempting login...');
            const result = await window.api.login({ email, password });
            console.log('Login result:', result);

            if (result.success) {
                console.log('Login successful, redirecting based on role');
                this.showSuccess('Đăng nhập thành công! Đang chuyển hướng...');

                // Redirect based on user role
                const userRole = result.user?.role;
                let redirectUrl = 'tasks.html'; // Default for regular users

                if (userRole === 'admin') {
                    redirectUrl = 'admin.html';
                } else if (userRole === 'manager') {
                    redirectUrl = 'manager.html';
                }

                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1000);
            } else {
                this.showError(result.error || 'Đăng nhập thất bại');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showError('Đã xảy ra lỗi khi đăng nhập');
        } finally {
            // Reset button state
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            if (loading) loading.style.display = 'none';
        }
    }

    showError(message) {
        const errorDiv = document.getElementById('error');
        const successDiv = document.getElementById('success');

        // Hide success message if showing
        if (successDiv) successDiv.style.display = 'none';

        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    showSuccess(message) {
        const successDiv = document.getElementById('success');
        const errorDiv = document.getElementById('error');

        // Hide error message if showing
        if (errorDiv) errorDiv.style.display = 'none';

        if (successDiv) {
            successDiv.textContent = message;
            successDiv.style.display = 'block';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LoginManager();
});
