// App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý các thông báo flash
    setupFlashMessages();
    
    // Xử lý các form validation
    setupFormValidation();
    
    // Xử lý các dropdown menu
    setupDropdowns();
    
    console.log('App JS loaded successfully');
});

// Xử lý các thông báo flash
function setupFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert');
    
    flashMessages.forEach(message => {
        // Thêm nút đóng thông báo
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.className = 'close';
        closeButton.style.float = 'right';
        closeButton.style.fontSize = '1.25rem';
        closeButton.style.fontWeight = 'bold';
        closeButton.style.lineHeight = '1';
        closeButton.style.color = 'inherit';
        closeButton.style.opacity = '0.5';
        closeButton.style.background = 'none';
        closeButton.style.border = '0';
        closeButton.style.padding = '0';
        closeButton.style.cursor = 'pointer';
        
        closeButton.addEventListener('click', function() {
            message.style.display = 'none';
        });
        
        message.prepend(closeButton);
        
        // Tự động ẩn thông báo sau 5 giây
        setTimeout(() => {
            message.style.display = 'none';
        }, 5000);
    });
}

// Xử lý form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const requiredInputs = form.querySelectorAll('[required]');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    
                    // Hiển thị thông báo lỗi
                    let errorMessage = input.dataset.errorMessage || 'Trường này là bắt buộc';
                    
                    // Kiểm tra xem đã có thông báo lỗi chưa
                    let nextElement = input.nextElementSibling;
                    if (nextElement && nextElement.classList.contains('error-message')) {
                        nextElement.textContent = errorMessage;
                    } else {
                        // Tạo thông báo lỗi mới
                        const errorElement = document.createElement('div');
                        errorElement.className = 'error-message';
                        errorElement.textContent = errorMessage;
                        errorElement.style.color = 'red';
                        errorElement.style.fontSize = '0.875rem';
                        errorElement.style.marginTop = '0.25rem';
                        
                        input.parentNode.insertBefore(errorElement, input.nextSibling);
                    }
                    
                    // Thêm class lỗi cho input
                    input.classList.add('is-invalid');
                    input.style.borderColor = 'red';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Xóa thông báo lỗi khi người dùng nhập lại
        requiredInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (input.value.trim()) {
                    // Xóa thông báo lỗi
                    let nextElement = input.nextElementSibling;
                    if (nextElement && nextElement.classList.contains('error-message')) {
                        nextElement.remove();
                    }
                    
                    // Xóa class lỗi
                    input.classList.remove('is-invalid');
                    input.style.borderColor = '';
                }
            });
        });
    });
}

// Xử lý dropdown menu
function setupDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (trigger && menu) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle dropdown menu
                menu.classList.toggle('show');
                
                // Đóng dropdown khi click bên ngoài
                document.addEventListener('click', function closeDropdown(e) {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.remove('show');
                        document.removeEventListener('click', closeDropdown);
                    }
                });
            });
        }
    });
}
