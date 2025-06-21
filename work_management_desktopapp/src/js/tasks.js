// Tasks functionality
class TaskManager {
    constructor() {
        this.currentTasks = [];
        this.currentUser = null;
        this.users = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.checkAuth();
        this.loadTasks();
        this.checkApiConnection();
    }

    bindEvents() {
        // Add task form
        const addTaskForm = document.getElementById('addTaskForm');
        if (addTaskForm) {
            addTaskForm.addEventListener('submit', this.handleAddTask.bind(this));
        }

        // Edit task form
        const editTaskForm = document.getElementById('editTaskForm');
        if (editTaskForm) {
            editTaskForm.addEventListener('submit', this.handleEditTask.bind(this));
        }

        // Modal close buttons
        const closeModal = document.getElementById('closeModal');
        const closeEditModal = document.getElementById('closeEditModal');

        if (closeModal) {
            closeModal.addEventListener('click', this.closeAddModal.bind(this));
        }

        if (closeEditModal) {
            closeEditModal.addEventListener('click', this.closeEditModal.bind(this));
        }

        // Confirm dialog buttons
        const confirmYes = document.getElementById('confirmYes');
        const confirmNo = document.getElementById('confirmNo');

        if (confirmYes) {
            confirmYes.addEventListener('click', this.handleDeleteConfirm.bind(this));
        }

        if (confirmNo) {
            confirmNo.addEventListener('click', this.closeConfirmDialog.bind(this));
        }

        // Close modals when clicking outside
        window.addEventListener('click', this.handleWindowClick.bind(this));

        // Profile modal event listeners
        const closeProfileModal = document.getElementById('closeProfileModal');
        if (closeProfileModal) {
            closeProfileModal.addEventListener('click', this.closeProfileModal.bind(this));
        }

        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', this.handleProfileSubmit.bind(this));
        }

        // Password modal event listeners
        const closePasswordModal = document.getElementById('closePasswordModal');
        if (closePasswordModal) {
            closePasswordModal.addEventListener('click', this.closePasswordModal.bind(this));
        }

        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', this.handlePasswordSubmit.bind(this));
        }



        // Open modal button
        const openModalBtn = document.getElementById('openModalBtn');
        if (openModalBtn) {
            openModalBtn.addEventListener('click', this.handleOpenModal.bind(this));
        }

        // Profile dropdown
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');
        if (profileButton && profileMenu) {
            profileButton.addEventListener('click', this.toggleProfileMenu.bind(this));
        }

        // Profile menu items
        const profileInfo = document.getElementById('profileInfo');
        const changePassword = document.getElementById('changePassword');
        const logoutBtnProfile = document.getElementById('logoutBtnProfile');

        if (profileInfo) {
            profileInfo.addEventListener('click', this.handleProfileInfo.bind(this));
        }
        if (changePassword) {
            changePassword.addEventListener('click', this.handleChangePassword.bind(this));
        }
        if (logoutBtnProfile) {
            logoutBtnProfile.addEventListener('click', this.handleLogout.bind(this));
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', (event) => {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                this.closeProfileMenu();
            }
        });
    }

    async checkApiConnection() {
        try {
            const isConnected = await window.api.checkApiConnection();
            if (isConnected) {
                this.updateConnectionStatus('Connected', true);
            } else {
                this.updateConnectionStatus('Disconnected', false);
            }
        } catch (error) {
            console.error('API connection error:', error);
            this.updateConnectionStatus('Error', false);
        }
    }

    updateConnectionStatus(status, isConnected) {
        const statusElement = document.getElementById('connectionStatus');
        if (statusElement) {
            statusElement.textContent = status;
            statusElement.className = isConnected ? 'status connected' : 'status disconnected';
        }
    }

    async checkAuth() {
        try {
            const token = await window.api.getToken();
            if (!token) {
                window.location.href = 'login.html';
                return;
            }

            const tokenInfo = await window.api.checkToken();
            if (!tokenInfo.valid) {
                await window.api.logout();
                window.location.href = 'login.html';
                return;
            }

            // Get full user data
            const userData = await window.api.getUserData();
            if (userData.success) {
                this.currentUser = userData.user;

                // Check user role and redirect if necessary
                if (this.currentUser.role === 'admin') {
                    window.location.href = 'admin.html';
                    return;
                } else if (this.currentUser.role === 'manager') {
                    window.location.href = 'manager.html';
                    return;
                }

                // Display user initials in profile avatar
                this.updateUserProfile();
            } else {
                console.error('Failed to get user data:', userData.error);
                // Fallback to token payload
                this.currentUser = tokenInfo.payload;
                this.updateUserProfile();
            }

            // Load users if manager or admin
            if (this.currentUser && (this.currentUser.role === 'manager' || this.currentUser.role === 'admin')) {
                await this.loadUsers();
                this.showUserSelectFields();
            }
        } catch (error) {
            console.error('Error checking auth:', error);
            window.location.href = 'login.html';
        }
    }

    async loadUsers() {
        try {
            const result = await window.api.getUsers();
            if (result.success) {
                this.users = result.users;
                this.populateUserSelects();
            } else {
                console.error('Failed to load users:', result.error);
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    populateUserSelects() {
        const userSelect = document.getElementById('userId');
        const editUserSelect = document.getElementById('editUserId');

        if (userSelect) {
            userSelect.innerHTML = '<option value="">Chọn người dùng...</option>';
            this.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                userSelect.appendChild(option);
            });
        }

        if (editUserSelect) {
            editUserSelect.innerHTML = '<option value="">Chọn người dùng...</option>';
            this.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                editUserSelect.appendChild(option);
            });
        }
    }

    showUserSelectFields() {
        const userSelectGroup = document.getElementById('userSelectGroup');
        const editUserSelectGroup = document.getElementById('editUserSelectGroup');
        const userSelect = document.getElementById('userId');
        const editUserSelect = document.getElementById('editUserId');

        if (userSelectGroup) {
            userSelectGroup.style.display = 'block';
        }
        if (editUserSelectGroup) {
            editUserSelectGroup.style.display = 'block';
        }
        if (userSelect) {
            userSelect.required = true;
        }
        if (editUserSelect) {
            editUserSelect.required = true;
        }
    }

    handleOpenModal() {
        const addTaskModal = document.getElementById('addTaskModal');
        const modalError = document.getElementById('modalError');
        const addTaskForm = document.getElementById('addTaskForm');

        if (addTaskModal) {
            addTaskModal.style.display = 'block';
        }

        if (modalError) {
            modalError.style.display = 'none';
        }

        if (addTaskForm) {
            addTaskForm.reset();

            // Set default date values
            const today = new Date().toISOString().split('T')[0];
            const startDateInput = document.getElementById('startDate');
            if (startDateInput) {
                startDateInput.value = today;
            }

            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const dueDateInput = document.getElementById('dueDate');
            if (dueDateInput) {
                dueDateInput.value = nextWeek.toISOString().split('T')[0];
            }
        }
    }

    async loadTasks() {
        const loading = document.getElementById('loading');
        const errorMessage = document.getElementById('errorMessage');

        // Show loading
        if (loading) loading.style.display = 'flex';
        if (errorMessage) errorMessage.style.display = 'none';

        try {
            const result = await window.api.getTasks();

            if (result.success) {
                this.currentTasks = result.tasks;
                this.renderTasks(result.tasks);
            } else {
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }

                // Show error message
                if (errorMessage) {
                    errorMessage.textContent = result.error || 'Không thể tải danh sách công việc';
                    errorMessage.style.display = 'block';
                }
                console.error('Failed to load tasks:', result.error);
            }
        } catch (error) {
            // Show error message
            if (errorMessage) {
                errorMessage.textContent = 'Đã xảy ra lỗi khi tải danh sách công việc';
                errorMessage.style.display = 'block';
            }
            console.error('Error loading tasks:', error);
        } finally {
            // Hide loading
            if (loading) loading.style.display = 'none';
        }
    }

    renderTasks(tasks) {
        const tasksList = document.getElementById('tasksList');
        const noTasks = document.getElementById('noTasks');
        const tasksTable = document.getElementById('tasksTable');

        if (!tasksList) return;

        tasksList.innerHTML = '';

        if (tasks.length === 0) {
            if (noTasks) noTasks.style.display = 'block';
            if (tasksTable) tasksTable.style.display = 'none';
            return;
        }

        if (noTasks) noTasks.style.display = 'none';
        if (tasksTable) tasksTable.style.display = 'table';

        tasks.forEach(task => {
            const row = this.createTaskRow(task);
            tasksList.appendChild(row);
        });
    }

    createTaskRow(task) {
        const row = document.createElement('tr');

        // Format dates
        const startDate = task.start_date ? new Date(task.start_date).toLocaleDateString('vi-VN') : 'N/A';
        const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('vi-VN') : 'N/A';

        const statusClass = this.getStatusClass(task.status);
        const priorityClass = this.getPriorityClass(task.priority);

        row.innerHTML = `
            <td>${task.id}</td>
            <td>
                <div style="font-weight: 500;">${task.title}</div>
                ${task.description ? `<div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">${task.description}</div>` : ''}
            </td>
            <td>${startDate}</td>
            <td>${dueDate}</td>
            <td><span class="badge ${statusClass}">${this.getStatusText(task.status)}</span></td>
            <td><span class="badge ${priorityClass}">${this.getPriorityText(task.priority)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="edit-btn" onclick="editTask(${task.id})">
                        <i class="fas fa-edit"></i>
                        Sửa
                    </button>
                    <button class="delete-btn" onclick="deleteTask(${task.id})">
                        <i class="fas fa-trash"></i>
                        Xóa
                    </button>
                </div>
            </td>
        `;

        return row;
    }

    getStatusClass(status) {
        const statusClasses = {
            'pending': 'badge-pending',
            'in_progress': 'badge-in-progress',
            'completed': 'badge-completed'
        };
        return statusClasses[status] || '';
    }

    getPriorityClass(priority) {
        const priorityClasses = {
            'low': 'badge-low',
            'medium': 'badge-medium',
            'high': 'badge-high'
        };
        return priorityClasses[priority] || '';
    }

    getStatusText(status) {
        const statusTexts = {
            'pending': 'Chờ xử lý',
            'in_progress': 'Đang thực hiện',
            'completed': 'Hoàn thành'
        };
        return statusTexts[status] || status;
    }

    getPriorityText(priority) {
        const priorityTexts = {
            'low': 'Thấp',
            'medium': 'Trung bình',
            'high': 'Cao'
        };
        return priorityTexts[priority] || priority;
    }

    async handleAddTask(event) {
        event.preventDefault();

        const modalError = document.getElementById('modalError');
        const submitBtn = event.target.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;

        // Hide error and show loading state
        if (modalError) modalError.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Đang thêm...';
        }

        const formData = new FormData(event.target);
        const taskData = {};

        for (const [key, value] of formData.entries()) {
            taskData[key] = value;
        }

        try {
            const result = await window.api.addTask(taskData);

            if (result.success) {
                this.closeAddModal();
                this.loadTasks();
                event.target.reset();
            } else {
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }

                if (modalError) {
                    modalError.textContent = result.error || 'Không thể thêm công việc';
                    modalError.style.display = 'block';
                }
            }
        } catch (error) {
            if (modalError) {
                modalError.textContent = error.message || 'Đã xảy ra lỗi khi thêm công việc';
                modalError.style.display = 'block';
            }
        } finally {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    }

    async handleEditTask(event) {
        event.preventDefault();

        const editModalError = document.getElementById('editModalError');
        const submitBtn = event.target.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;

        // Hide error and show loading state
        if (editModalError) editModalError.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Đang cập nhật...';
        }

        const formData = new FormData(event.target);
        const taskData = {};
        const taskId = document.getElementById('editTaskId').value;

        for (const [key, value] of formData.entries()) {
            if (key !== 'taskId') {
                taskData[key] = value;
            }
        }

        try {
            const result = await window.api.updateTask(taskId, taskData);

            if (result.success) {
                this.closeEditModal();
                this.loadTasks();
            } else {
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }

                if (editModalError) {
                    editModalError.textContent = result.error || 'Không thể cập nhật công việc';
                    editModalError.style.display = 'block';
                }
            }
        } catch (error) {
            if (editModalError) {
                editModalError.textContent = error.message || 'Đã xảy ra lỗi khi cập nhật công việc';
                editModalError.style.display = 'block';
            }
        } finally {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    }

    async handleDeleteConfirm() {
        const confirmDialog = document.getElementById('confirmDialog');
        const taskId = confirmDialog?.dataset.taskId;

        if (!taskId) return;

        this.closeConfirmDialog();

        try {
            const result = await window.api.deleteTask(taskId);

            if (result.success) {
                this.loadTasks();
            } else {
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }

                alert(result.error || 'Không thể xóa công việc');
            }
        } catch (error) {
            alert(error.message || 'Đã xảy ra lỗi khi xóa công việc');
        }
    }

    closeAddModal() {
        const modal = document.getElementById('addTaskModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    closeEditModal() {
        const modal = document.getElementById('editTaskModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    closeConfirmDialog() {
        const dialog = document.getElementById('confirmDialog');
        if (dialog) {
            dialog.style.display = 'none';
        }
    }

    handleWindowClick(event) {
        const addModal = document.getElementById('addTaskModal');
        const editModal = document.getElementById('editTaskModal');
        const profileModal = document.getElementById('profileModal');
        const passwordModal = document.getElementById('passwordModal');
        const profileMenu = document.getElementById('profileMenu');

        if (event.target === addModal) {
            this.closeAddModal();
        }

        if (event.target === editModal) {
            this.closeEditModal();
        }

        if (event.target === profileModal) {
            this.closeProfileModal();
        }

        if (event.target === passwordModal) {
            this.closePasswordModal();
        }

        if (profileMenu && !profileMenu.contains(event.target) && !event.target.closest('.profile-button')) {
            this.closeProfileMenu();
        }
    }

    updateUserProfile(userPayload) {
        const userInitials = document.getElementById('userInitials');
        if (userInitials && this.currentUser) {
            // Use actual user name if available, otherwise use email
            let displayName = this.currentUser.name || this.currentUser.email;

            // Extract initials from name or email
            let initials;
            if (this.currentUser.name) {
                // Get initials from full name
                const nameParts = this.currentUser.name.trim().split(' ');
                if (nameParts.length >= 2) {
                    initials = nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0);
                } else {
                    initials = nameParts[0].substring(0, 2);
                }
            } else {
                // Get initials from email
                const emailName = this.currentUser.email.split('@')[0];
                initials = emailName.substring(0, 2);
            }

            userInitials.textContent = initials.toUpperCase();
        }
    }

    toggleProfileMenu(event) {
        event.stopPropagation();
        const profileMenu = document.getElementById('profileMenu');
        if (profileMenu) {
            profileMenu.classList.toggle('show');
        }
    }

    handleProfileInfo() {
        this.closeProfileMenu();
        this.openProfileModal();
    }

    handleChangePassword() {
        this.closeProfileMenu();
        this.openPasswordModal();
    }

    openProfileModal() {
        const modal = document.getElementById('profileModal');
        if (modal && this.currentUser) {
            // Fill form with current user data
            document.getElementById('profileName').value = this.currentUser.name || '';
            document.getElementById('profileEmail').value = this.currentUser.email || '';
            document.getElementById('profilePhone').value = this.currentUser.phone || '';
            document.getElementById('profileAddress').value = this.currentUser.address || '';

            modal.style.display = 'block';
        }
    }

    closeProfileModal() {
        const modal = document.getElementById('profileModal');
        if (modal) {
            modal.style.display = 'none';
            this.clearProfileMessages();
        }
    }

    openPasswordModal() {
        const modal = document.getElementById('passwordModal');
        if (modal) {
            // Clear form
            document.getElementById('passwordForm').reset();
            modal.style.display = 'block';
        }
    }

    closePasswordModal() {
        const modal = document.getElementById('passwordModal');
        if (modal) {
            modal.style.display = 'none';
            this.clearPasswordMessages();
        }
    }

    clearProfileMessages() {
        const errorDiv = document.getElementById('profileError');
        const successDiv = document.getElementById('profileSuccess');
        if (errorDiv) errorDiv.style.display = 'none';
        if (successDiv) successDiv.style.display = 'none';
    }

    clearPasswordMessages() {
        const errorDiv = document.getElementById('passwordError');
        const successDiv = document.getElementById('passwordSuccess');
        if (errorDiv) errorDiv.style.display = 'none';
        if (successDiv) successDiv.style.display = 'none';
    }

    async handleProfileSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const profileData = {
            name: formData.get('name'),
            phone: formData.get('phone'),
            address: formData.get('address')
        };

        try {
            this.clearProfileMessages();

            const result = await window.api.updateProfile(profileData);

            if (result.success) {
                // Update current user data
                this.currentUser = { ...this.currentUser, ...result.user };

                // Update profile avatar initials
                this.updateUserProfile();

                // Show success message
                const successDiv = document.getElementById('profileSuccess');
                if (successDiv) {
                    successDiv.textContent = result.message || 'Cập nhật thông tin thành công!';
                    successDiv.style.display = 'block';
                }

                // Close modal after 1 second
                setTimeout(() => {
                    this.closeProfileModal();
                }, 1000);
            } else {
                // Check if token expired
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }
                throw new Error(result.error || 'Có lỗi xảy ra khi cập nhật thông tin');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            const errorDiv = document.getElementById('profileError');
            if (errorDiv) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        }
    }

    async handlePasswordSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const passwordData = {
            current_password: formData.get('current_password'),
            new_password: formData.get('new_password'),
            new_password_confirmation: formData.get('new_password_confirmation')
        };

        // Validate password confirmation
        if (passwordData.new_password !== passwordData.new_password_confirmation) {
            const errorDiv = document.getElementById('passwordError');
            if (errorDiv) {
                errorDiv.textContent = 'Mật khẩu mới và xác nhận mật khẩu không khớp';
                errorDiv.style.display = 'block';
            }
            return;
        }

        try {
            this.clearPasswordMessages();

            const result = await window.api.changePassword(passwordData);

            if (result.success) {
                // Show success message
                const successDiv = document.getElementById('passwordSuccess');
                if (successDiv) {
                    successDiv.textContent = result.message || 'Đổi mật khẩu thành công!';
                    successDiv.style.display = 'block';
                }

                // Clear form
                form.reset();

                // Close modal after 1 second
                setTimeout(() => {
                    this.closePasswordModal();
                }, 1000);
            } else {
                // Check if token expired
                if (result.tokenExpired) {
                    await window.api.logout();
                    window.location.href = 'login.html';
                    return;
                }
                throw new Error(result.error || 'Có lỗi xảy ra khi đổi mật khẩu');
            }
        } catch (error) {
            console.error('Error changing password:', error);
            const errorDiv = document.getElementById('passwordError');
            if (errorDiv) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        }
    }

    closeProfileMenu() {
        const profileMenu = document.getElementById('profileMenu');
        if (profileMenu) {
            profileMenu.classList.remove('show');
        }
    }

    async handleLogout() {
        this.closeProfileMenu();
        try {
            await window.api.logout();
            window.location.href = 'login.html';
        } catch (error) {
            console.error('Logout error:', error);
            window.location.href = 'login.html';
        }
    }
}

// Global functions for edit and delete (called from HTML)
window.editTask = function(taskId) {
    const taskManager = window.taskManagerInstance;
    if (!taskManager) return;

    const task = taskManager.currentTasks.find(t => t.id === taskId);
    if (!task) {
        alert('Không tìm thấy công việc');
        return;
    }

    // Fill edit form with task data
    document.getElementById('editTaskId').value = task.id;
    document.getElementById('editTitle').value = task.title;
    document.getElementById('editDescription').value = task.description || '';
    document.getElementById('editStartDate').value = task.start_date;
    document.getElementById('editDueDate').value = task.due_date;
    document.getElementById('editPriority').value = task.priority;
    document.getElementById('editStatus').value = task.status;

    // Set user_id if manager/admin
    const editUserSelect = document.getElementById('editUserId');
    if (editUserSelect && task.creator_id) {
        editUserSelect.value = task.creator_id;
    }

    // Show edit modal
    const editModal = document.getElementById('editTaskModal');
    const editModalError = document.getElementById('editModalError');

    if (editModal) {
        editModal.style.display = 'block';
    }

    if (editModalError) {
        editModalError.style.display = 'none';
    }
};

window.deleteTask = function(taskId) {
    const confirmDialog = document.getElementById('confirmDialog');
    if (confirmDialog) {
        confirmDialog.dataset.taskId = taskId;
        confirmDialog.style.display = 'flex';
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.taskManagerInstance = new TaskManager();
});
