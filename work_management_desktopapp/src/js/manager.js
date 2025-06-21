// Manager Panel JavaScript
class ManagerManager {
    constructor() {
        this.currentUser = null;
        this.tasks = [];
        this.users = [];
        this.currentSection = 'tasks';
        this.deleteTaskId = null;
        this.init();
    }

    async init() {
        await this.checkAuth();
        this.bindEvents();
        this.loadTasks();
        this.loadUsers(); // Load users for task assignment
    }

    async checkAuth() {
        try {
            const tokenInfo = await window.api.checkToken();
            if (!tokenInfo.valid) {
                window.location.href = 'login.html';
                return;
            }

            // Get full user data
            const userData = await window.api.getUserData();
            if (userData.success) {
                this.currentUser = userData.user;
                
                // Check if user is manager
                if (this.currentUser.role !== 'manager') {
                    alert('Bạn không có quyền truy cập trang này. Yêu cầu quyền manager.');
                    if (this.currentUser.role === 'admin') {
                        window.location.href = 'admin.html';
                    } else {
                        window.location.href = 'tasks.html';
                    }
                    return;
                }
                
                // Display user initials in profile avatar
                this.updateUserProfile();
            } else {
                console.error('Failed to get user data:', userData.error);
                window.location.href = 'login.html';
            }
        } catch (error) {
            console.error('Error checking auth:', error);
            window.location.href = 'login.html';
        }
    }

    bindEvents() {
        // Sidebar navigation
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = link.dataset.section;
                this.switchSection(section);
            });
        });

        // Profile dropdown
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');
        if (profileButton && profileMenu) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
            });
        }

        // Profile menu items
        const profileInfo = document.getElementById('profileInfo');
        const changePassword = document.getElementById('changePassword');
        const logoutBtnProfile = document.getElementById('logoutBtnProfile');

        if (profileInfo) {
            profileInfo.addEventListener('click', () => {
                profileMenu.classList.remove('show');
                openProfileModal();
            });
        }
        if (changePassword) {
            changePassword.addEventListener('click', () => {
                profileMenu.classList.remove('show');
                openPasswordModal();
            });
        }
        if (logoutBtnProfile) {
            logoutBtnProfile.addEventListener('click', () => {
                profileMenu.classList.remove('show');
                handleLogout();
            });
        }

        // Window click for closing modals and menus
        window.addEventListener('click', (event) => {
            this.handleWindowClick(event);
        });
    }

    switchSection(section) {
        // Update active sidebar link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-section="${section}"]`).classList.add('active');

        // Hide all sections
        document.querySelectorAll('.content-section').forEach(sec => {
            sec.classList.add('hidden');
        });

        // Show selected section
        document.getElementById(`${section}Section`).classList.remove('hidden');

        // Update page title and header actions
        const pageTitle = document.getElementById('pageTitle');
        const addTaskBtn = document.getElementById('addTaskBtn');

        switch (section) {
            case 'tasks':
                pageTitle.textContent = 'Quản lý công việc';
                addTaskBtn.style.display = 'flex';
                this.loadTasks();
                break;
            case 'reports':
                pageTitle.textContent = 'Báo cáo & Thống kê';
                addTaskBtn.style.display = 'none';
                this.loadReports();
                break;
        }

        this.currentSection = section;
    }

    async loadTasks() {
        const loading = document.getElementById('loading');
        const errorMessage = document.getElementById('errorMessage');

        try {
            loading.style.display = 'flex';
            errorMessage.style.display = 'none';

            const result = await window.api.getTasks();

            if (result.success) {
                this.tasks = result.tasks || [];
                this.renderTasks();
            } else {
                // Check if token expired
                if (result.tokenExpired) {
                    alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                    window.location.href = 'login.html';
                    return;
                }
                throw new Error(result.error || 'Không thể tải danh sách công việc');
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
            errorMessage.style.display = 'block';
            document.getElementById('errorText').textContent = error.message;
        } finally {
            loading.style.display = 'none';
        }
    }

    async loadUsers() {
        try {
            const result = await window.api.getUsers();

            if (result.success) {
                // Filter to only regular users for task assignment
                this.users = (result.users || []).filter(user => user.role === 'user');
                this.populateUserSelects();
            } else {
                // Check if token expired
                if (result.tokenExpired) {
                    alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                    window.location.href = 'login.html';
                    return;
                }
                console.error('Failed to load users:', result.error);
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    populateUserSelects() {
        const selects = ['taskUser', 'editTaskUser'];

        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                // Clear existing options except the first one
                while (select.children.length > 1) {
                    select.removeChild(select.lastChild);
                }

                // Add user options
                this.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });
            }
        });
    }

    renderTasks() {
        const tasksList = document.getElementById('tasksList');
        tasksList.innerHTML = '';

        this.tasks.forEach(task => {
            const row = document.createElement('tr');
            const startDate = task.start_date ? new Date(task.start_date).toLocaleDateString('vi-VN') : 'N/A';
            const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('vi-VN') : 'N/A';
            // Hiển thị assigned_user nếu có, nếu không thì hiển thị creator (người tạo task)
            const assignedUser = task.assigned_user ? task.assigned_user.name :
                                 (task.creator ? task.creator.name : 'Không xác định');

            row.innerHTML = `
                <td>${task.id}</td>
                <td>
                    <div style="font-weight: 500;">${task.title}</div>
                    ${task.description ? `<div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">${task.description}</div>` : ''}
                </td>
                <td>${assignedUser}</td>
                <td>${startDate}</td>
                <td>${dueDate}</td>
                <td><span class="badge badge-${task.status}">${this.getStatusText(task.status)}</span></td>
                <td><span class="badge badge-${task.priority}">${this.getPriorityText(task.priority)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="openEditTaskModal(${task.id})">
                            <i class="fas fa-edit"></i>
                            Sửa
                        </button>
                        <button class="delete-btn" onclick="openDeleteTaskModal(${task.id})">
                            <i class="fas fa-trash"></i>
                            Xóa
                        </button>
                    </div>
                </td>
            `;
            tasksList.appendChild(row);
        });
    }

    async loadReports() {
        try {
            const result = await window.api.getTasks();
            
            if (result.success) {
                this.tasks = result.tasks;
                this.renderStats();
                this.renderUserStats();
            }
        } catch (error) {
            console.error('Error loading reports:', error);
        }
    }

    renderStats() {
        const totalTasks = this.tasks.length;
        const pendingTasks = this.tasks.filter(task => task.status === 'pending').length;
        const inProgressTasks = this.tasks.filter(task => task.status === 'in_progress').length;
        const completedTasks = this.tasks.filter(task => task.status === 'completed').length;

        document.getElementById('totalTasks').textContent = totalTasks;
        document.getElementById('pendingTasks').textContent = pendingTasks;
        document.getElementById('inProgressTasks').textContent = inProgressTasks;
        document.getElementById('completedTasks').textContent = completedTasks;
    }

    renderUserStats() {
        const userStatsList = document.getElementById('userStatsList');
        userStatsList.innerHTML = '';

        // Group tasks by user
        const userTasksMap = {};
        
        this.tasks.forEach(task => {
            // Ưu tiên assigned_user, nếu không có thì dùng creator
            const userId = task.assigned_user ? task.assigned_user.id :
                          (task.creator ? task.creator.id : 'unassigned');
            const userName = task.assigned_user ? task.assigned_user.name :
                            (task.creator ? task.creator.name : 'Không xác định');
            
            if (!userTasksMap[userId]) {
                userTasksMap[userId] = {
                    name: userName,
                    total: 0,
                    pending: 0,
                    in_progress: 0,
                    completed: 0
                };
            }
            
            userTasksMap[userId].total++;
            userTasksMap[userId][task.status]++;
        });

        // Render user stats
        Object.values(userTasksMap).forEach(userStats => {
            const completionRate = userStats.total > 0 ? 
                Math.round((userStats.completed / userStats.total) * 100) : 0;
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${userStats.name}</td>
                <td>${userStats.total}</td>
                <td>${userStats.pending}</td>
                <td>${userStats.in_progress}</td>
                <td>${userStats.completed}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="flex: 1; background: #f3f4f6; border-radius: 9999px; height: 8px;">
                            <div style="background: #059669; height: 100%; border-radius: 9999px; width: ${completionRate}%;"></div>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 500;">${completionRate}%</span>
                    </div>
                </td>
            `;
            userStatsList.appendChild(row);
        });
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

    updateUserProfile() {
        const userInitials = document.getElementById('userInitials');
        const userInitialsSidebar = document.getElementById('userInitialsSidebar');

        if (this.currentUser) {
            let initials;
            if (this.currentUser.name) {
                const nameParts = this.currentUser.name.trim().split(' ');
                if (nameParts.length >= 2) {
                    initials = nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0);
                } else {
                    initials = nameParts[0].substring(0, 2);
                }
            } else {
                const emailName = this.currentUser.email.split('@')[0];
                initials = emailName.substring(0, 2);
            }

            if (userInitials) {
                userInitials.textContent = initials.toUpperCase();
            }
            if (userInitialsSidebar) {
                userInitialsSidebar.textContent = initials.toUpperCase();
            }
        }
    }

    handleWindowClick(event) {
        const profileMenu = document.getElementById('profileMenu');
        
        // Close profile menu if clicking outside
        if (profileMenu && !profileMenu.contains(event.target) && !event.target.closest('.profile-button')) {
            profileMenu.classList.remove('show');
        }

        // Close modals if clicking on backdrop
        const modals = ['addTaskModal', 'editTaskModal', 'profileModal', 'passwordModal', 'confirmDeleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && event.target === modal) {
                this.closeModal(modalId);
            }
        });
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }
}

// Global functions for HTML onclick handlers
function toggleProfileMenu(event) {
    event.stopPropagation();
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
        profileMenu.classList.toggle('show');
    }
}

function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal && managerManager.currentUser) {
        // Fill form with current user data
        document.getElementById('profileName').value = managerManager.currentUser.name || '';
        document.getElementById('profileEmail').value = managerManager.currentUser.email || '';
        document.getElementById('profilePhone').value = managerManager.currentUser.phone || '';
        document.getElementById('profileAddress').value = managerManager.currentUser.address || '';

        modal.style.display = 'flex';
    }

    // Close profile menu
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
        profileMenu.classList.remove('show');
    }
}

function closeProfileModal() {
    managerManager.closeModal('profileModal');
    clearProfileMessages();
}

function openPasswordModal() {
    const modal = document.getElementById('passwordModal');
    if (modal) {
        modal.style.display = 'flex';
    }

    // Close profile menu
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
        profileMenu.classList.remove('show');
    }
}

function closePasswordModal() {
    managerManager.closeModal('passwordModal');
    clearPasswordMessages();
    document.getElementById('passwordForm').reset();
}

function openAddTaskModal() {
    const modal = document.getElementById('addTaskModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('addTaskForm').reset();
        document.getElementById('addTaskError').style.display = 'none';

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskStartDate').value = today;
    }
}

function closeAddTaskModal() {
    managerManager.closeModal('addTaskModal');
}

function openEditTaskModal(taskId) {
    const task = managerManager.tasks.find(t => t.id === taskId);
    if (!task) return;

    const modal = document.getElementById('editTaskModal');
    if (modal) {
        // Fill form with task data
        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editTaskTitle').value = task.title;
        document.getElementById('editTaskDescription').value = task.description || '';
        document.getElementById('editTaskUser').value = task.assigned_user ? task.assigned_user.id : '';
        document.getElementById('editTaskStartDate').value = task.start_date ? task.start_date.split('T')[0] : '';
        document.getElementById('editTaskDueDate').value = task.due_date ? task.due_date.split('T')[0] : '';
        document.getElementById('editTaskStatus').value = task.status;
        document.getElementById('editTaskPriority').value = task.priority;
        document.getElementById('editTaskError').style.display = 'none';

        modal.style.display = 'flex';
    }
}

function closeEditTaskModal() {
    managerManager.closeModal('editTaskModal');
}

function openDeleteTaskModal(taskId) {
    managerManager.deleteTaskId = taskId;
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeConfirmDeleteModal() {
    managerManager.deleteTaskId = null;
    managerManager.closeModal('confirmDeleteModal');
}

function confirmDeleteTask() {
    if (managerManager.deleteTaskId) {
        managerManager.handleDeleteTask(managerManager.deleteTaskId);
    }
}

async function handleLogout() {
    try {
        await window.api.logout();
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.html';
    }
}

function clearProfileMessages() {
    const successDiv = document.getElementById('profileSuccess');
    const errorDiv = document.getElementById('profileError');
    if (successDiv) successDiv.style.display = 'none';
    if (errorDiv) errorDiv.style.display = 'none';
}

function clearPasswordMessages() {
    const successDiv = document.getElementById('passwordSuccess');
    const errorDiv = document.getElementById('passwordError');
    if (successDiv) successDiv.style.display = 'none';
    if (errorDiv) errorDiv.style.display = 'none';
}

// Form handlers
async function handleProfileSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const profileData = {
        name: formData.get('name'),
        phone: formData.get('phone'),
        address: formData.get('address')
    };

    try {
        clearProfileMessages();

        const result = await window.api.updateProfile(profileData);

        if (result.success) {
            // Update current user data
            managerManager.currentUser = { ...managerManager.currentUser, ...result.user };

            // Update profile avatar initials
            managerManager.updateUserProfile();

            // Show success message
            const successDiv = document.getElementById('profileSuccess');
            if (successDiv) {
                successDiv.textContent = result.message || 'Cập nhật thông tin thành công!';
                successDiv.style.display = 'block';
            }

            // Close modal after 1 second
            setTimeout(() => {
                closeProfileModal();
            }, 1000);
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi cập nhật thông tin');
        }
    } catch (error) {
        const errorDiv = document.getElementById('profileError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi cập nhật thông tin';
            errorDiv.style.display = 'block';
        }
    }
}

async function handlePasswordSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const form = event.target;

    // Validate password confirmation
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('new_password_confirmation');

    if (newPassword !== confirmPassword) {
        const errorDiv = document.getElementById('passwordError');
        if (errorDiv) {
            errorDiv.textContent = 'Mật khẩu xác nhận không khớp';
            errorDiv.style.display = 'block';
        }
        return;
    }

    const passwordData = {
        current_password: formData.get('current_password'),
        new_password: newPassword,
        new_password_confirmation: confirmPassword
    };

    try {
        clearPasswordMessages();

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
                closePasswordModal();
            }, 1000);
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi đổi mật khẩu');
        }
    } catch (error) {
        const errorDiv = document.getElementById('passwordError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi đổi mật khẩu';
            errorDiv.style.display = 'block';
        }
    }
}

async function handleAddTask(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const form = event.target;

    const taskData = {
        title: formData.get('title'),
        description: formData.get('description'),
        user_id: formData.get('user_id'),
        start_date: formData.get('start_date'),
        due_date: formData.get('due_date'),
        status: formData.get('status'),
        priority: formData.get('priority')
    };

    try {
        document.getElementById('addTaskError').style.display = 'none';

        const result = await window.api.addTask(taskData);

        if (result.success) {
            closeAddTaskModal();
            managerManager.loadTasks(); // Reload tasks list
            form.reset();
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi thêm công việc');
        }
    } catch (error) {
        const errorDiv = document.getElementById('addTaskError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi thêm công việc';
            errorDiv.style.display = 'block';
        }
    }
}

async function handleEditTask(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const taskId = formData.get('taskId');

    const taskData = {
        title: formData.get('title'),
        description: formData.get('description'),
        user_id: formData.get('user_id'),
        start_date: formData.get('start_date'),
        due_date: formData.get('due_date'),
        status: formData.get('status'),
        priority: formData.get('priority')
    };

    try {
        document.getElementById('editTaskError').style.display = 'none';

        const result = await window.api.updateTask(taskId, taskData);

        if (result.success) {
            closeEditTaskModal();
            managerManager.loadTasks(); // Reload tasks list
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi cập nhật công việc');
        }
    } catch (error) {
        const errorDiv = document.getElementById('editTaskError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi cập nhật công việc';
            errorDiv.style.display = 'block';
        }
    }
}

// Add method to ManagerManager class
ManagerManager.prototype.handleDeleteTask = async function(taskId) {
    try {
        const result = await window.api.deleteTask(taskId);

        if (result.success) {
            closeConfirmDeleteModal();
            this.loadTasks(); // Reload tasks list
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi xóa công việc');
        }
    } catch (error) {
        alert('Đã xảy ra lỗi khi xóa công việc: ' + error.message);
        closeConfirmDeleteModal();
    }
};

// Initialize manager when DOM is loaded
let managerManager;
document.addEventListener('DOMContentLoaded', () => {
    managerManager = new ManagerManager();
});
