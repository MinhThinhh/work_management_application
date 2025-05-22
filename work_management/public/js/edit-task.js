// Edit Task JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit task script loaded');

    // Lấy thông tin task từ form
    const form = document.querySelector('form');

    if (!form) {
        console.error('Form not found');
        return;
    }

    console.log('Form found:', form);

    const taskId = form.getAttribute('data-task-id');
    console.log('Task ID:', taskId);

    // Tạo dialog
    createEditDialog();

    // Mở dialog khi trang được tải
    setTimeout(() => {
        openEditDialog();
    }, 100);
});

// Tạo dialog cho form sửa công việc
function createEditDialog() {
    console.log('Creating edit dialog');

    try {
        // Lấy thông tin form hiện tại
        const originalForm = document.querySelector('form');

        if (!originalForm) {
            console.error('Original form not found');
            return;
        }

        console.log('Original form:', originalForm);

        const taskId = originalForm.getAttribute('data-task-id');
        console.log('Task ID:', taskId);

        // Lấy các giá trị từ form
        const titleInput = originalForm.querySelector('#title');
        const descriptionInput = originalForm.querySelector('#description');
        const startDateInput = originalForm.querySelector('#start_date');
        const dueDateInput = originalForm.querySelector('#due_date');
        const priorityInput = originalForm.querySelector('#priority');
        const statusInput = originalForm.querySelector('#status');

        console.log('Form elements:', {
            title: titleInput,
            description: descriptionInput,
            startDate: startDateInput,
            dueDate: dueDateInput,
            priority: priorityInput,
            status: statusInput
        });

        if (!titleInput || !descriptionInput || !startDateInput || !dueDateInput || !priorityInput || !statusInput) {
            console.error('Some form elements are missing');
            return;
        }

        const taskTitle = titleInput.value;
        const taskDescription = descriptionInput.value;
        const taskStartDate = startDateInput.value;
        const taskDueDate = dueDateInput.value;
        const taskPriority = priorityInput.value;
        const taskStatus = statusInput.value;

        console.log('Task data:', {
            title: taskTitle,
            description: taskDescription,
            startDate: taskStartDate,
            dueDate: taskDueDate,
            priority: taskPriority,
            status: taskStatus
        });

        // Lấy CSRF token
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (!metaTag) {
            console.error('CSRF token meta tag not found');
            return;
        }

        const csrfToken = metaTag.getAttribute('content');
        console.log('CSRF token found:', csrfToken ? 'Yes' : 'No');

        // Tạo dialog element
        const dialog = document.createElement('dialog');
        dialog.className = 'dialog';
        dialog.setAttribute('data-dialog', 'edit-form');

        // Tạo nội dung dialog
        dialog.innerHTML = `
            <form class="form" data-event-form method="POST" action="/tasks/${taskId}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="${csrfToken}">
                <div class="dialog__wrapper">
                    <div class="dialog__header">
                        <h2 class="dialog__title">Chỉnh sửa công việc</h2>
                        <button class="button button--icon button--secondary" type="button" data-dialog-close-button style="z-index: 1010;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="dialog__content">
                        <div class="form__fields">
                            <div class="form__field">
                                <label class="form__label" for="title">Tiêu đề</label>
                                <input class="input input--fill" id="title" name="title" type="text" value="${taskTitle}" placeholder="Nhập tiêu đề công việc" required autofocus />
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="description">Mô tả</label>
                                <textarea class="input input--fill" id="description" name="description" placeholder="Mô tả chi tiết công việc">${taskDescription}</textarea>
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="start_date">Ngày bắt đầu</label>
                                <input class="input input--fill" id="start_date" name="start_date" type="date" value="${taskStartDate}" required />
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="due_date">Ngày hết hạn</label>
                                <input class="input input--fill" id="due_date" name="due_date" type="date" value="${taskDueDate}" required />
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="priority">Mức độ ưu tiên</label>
                                <div class="select select--fill">
                                    <select class="select__select" id="priority" name="priority">
                                        <option value="low" ${taskPriority === 'low' ? 'selected' : ''}>Thấp</option>
                                        <option value="medium" ${taskPriority === 'medium' ? 'selected' : ''}>Trung bình</option>
                                        <option value="high" ${taskPriority === 'high' ? 'selected' : ''}>Cao</option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="select__icon">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="status">Trạng thái</label>
                                <div class="select select--fill">
                                    <select class="select__select" id="status" name="status">
                                        <option value="pending" ${taskStatus === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                                        <option value="in_progress" ${taskStatus === 'in_progress' ? 'selected' : ''}>Đang thực hiện</option>
                                        <option value="completed" ${taskStatus === 'completed' ? 'selected' : ''}>Hoàn thành</option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="select__icon">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dialog__footer">
                        <div class="dialog__actions">
                            <a href="/dashboard" class="button button--secondary">
                                Hủy
                            </a>
                            <button type="submit" class="button button--primary">Cập nhật</button>
                        </div>
                    </div>
                </div>
            </form>
        `;

        // Thêm dialog vào body
        document.body.appendChild(dialog);

        // Xử lý nút đóng dialog
        const closeButtons = dialog.querySelectorAll('[data-dialog-close-button]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dialog.close();
                window.location.href = '/dashboard';
            });
        });

        // Xử lý submit form
        const dialogForm = dialog.querySelector('form');
        dialogForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Lấy dữ liệu form và chuyển thành JSON
            const formData = new FormData(dialogForm);
            const formDataObj = {};

            // Chuyển FormData thành object
            for (let [key, value] of formData.entries()) {
                formDataObj[key] = value;
            }

            // Lấy token JWT nếu có
            const token = localStorage.getItem('jwt_token');

            // Đảm bảo formData có _method=PUT
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            console.log('Updating task with ID:', taskId);

            // Gửi request bằng fetch API
            fetch(`/tasks/${taskId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': token ? `Bearer ${token}` : '',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi khi cập nhật công việc: ' + response.status);
                }
                return response.text();
            })
            .then(data => {
                // Hiển thị thông báo thành công
                showNotification('Công việc đã được cập nhật thành công!', 'success');

                // Chuyển hướng về dashboard
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            })
            .catch(error => {
                console.error('Lỗi khi cập nhật công việc:', error);
                showNotification('Lỗi khi cập nhật công việc: ' + error.message, 'error');
            });
        });
    } catch (error) {
        console.error('Error in createEditDialog:', error);
    }
}

// Mở dialog sửa công việc
function openEditDialog() {
    const dialog = document.querySelector('[data-dialog="edit-form"]');
    if (dialog) {
        dialog.showModal();

        // Thêm sự kiện click bên ngoài để đóng dialog
        dialog.addEventListener('click', function(e) {
            if (e.target === dialog) {
                dialog.close();
                window.location.href = '/dashboard';
            }
        });
    }
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    // Tạo phần tử thông báo
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.classList.add(`notification--${type}`);

    // Thêm nội dung
    notification.textContent = message;

    // Thêm vào body
    document.body.appendChild(notification);

    // Hiển thị thông báo
    setTimeout(() => {
        notification.classList.add('notification--visible');
    }, 10);

    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.classList.remove('notification--visible');

        // Xóa khỏi DOM sau khi animation kết thúc
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
