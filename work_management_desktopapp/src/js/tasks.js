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

        // Logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', this.handleLogout.bind(this));
        }

        // Open modal button
        const openModalBtn = document.getElementById('openModalBtn');
        if (openModalBtn) {
            openModalBtn.addEventListener('click', this.handleOpenModal.bind(this));
        }
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

            // Store current user info
            this.currentUser = tokenInfo.payload;

            // Display user email
            const userEmail = document.getElementById('userEmail');
            if (userEmail && tokenInfo.payload && tokenInfo.payload.sub) {
                userEmail.textContent = tokenInfo.payload.sub;
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
                console.error('Failed to load tasks:', result.error);
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
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
            <td>${task.title}</td>
            <td>${startDate}</td>
            <td>${dueDate}</td>
            <td><span class="status-badge ${statusClass}">${this.getStatusText(task.status)}</span></td>
            <td><span class="status-badge ${priorityClass}">${this.getPriorityText(task.priority)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="edit-btn" onclick="editTask(${task.id})">Sửa</button>
                    <button class="delete-btn" onclick="deleteTask(${task.id})">Xóa</button>
                </div>
            </td>
        `;

        return row;
    }

    getStatusClass(status) {
        const statusClasses = {
            'pending': 'status-pending',
            'in_progress': 'status-in-progress',
            'completed': 'status-completed'
        };
        return statusClasses[status] || '';
    }

    getPriorityClass(priority) {
        const priorityClasses = {
            'low': 'priority-low',
            'medium': 'priority-medium',
            'high': 'priority-high'
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
        if (modalError) {
            modalError.style.display = 'none';
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
        }
    }

    async handleEditTask(event) {
        event.preventDefault();

        const editModalError = document.getElementById('editModalError');
        if (editModalError) {
            editModalError.style.display = 'none';
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

        if (event.target === addModal) {
            this.closeAddModal();
        }

        if (event.target === editModal) {
            this.closeEditModal();
        }
    }

    async handleLogout() {
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
