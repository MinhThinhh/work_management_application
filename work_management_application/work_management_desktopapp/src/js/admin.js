// Admin Panel JavaScript
class AdminManager {
    constructor() {
        this.currentUser = null;
        this.users = [];
        this.tasks = [];
        this.currentSection = 'users';
        this.deleteUserId = null;
        this.init();
    }

    async init() {
        await this.checkAuth();
        this.bindEvents();
        this.loadUsers();
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
                
                // Check if user is admin
                if (this.currentUser.role !== 'admin') {
                    alert('Bạn không có quyền truy cập trang này. Yêu cầu quyền admin.');
                    window.location.href = 'tasks.html';
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

        // Profile dropdown events
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');

        if (profileButton && profileMenu) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
            });
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', (event) => {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                const profileMenu = document.getElementById('profileMenu');
                if (profileMenu) {
                    profileMenu.classList.remove('show');
                }
            }
        });

        // Profile menu items
        const profileInfo = document.getElementById('profileInfo');
        const changePassword = document.getElementById('changePassword');
        const logoutBtn = document.getElementById('logoutBtnProfile');

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

        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
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
        const addUserBtn = document.getElementById('addUserBtn');

        switch (section) {
            case 'users':
                pageTitle.textContent = 'Quản lý người dùng';
                addUserBtn.style.display = 'flex';
                this.loadUsers();
                break;
            case 'teams':
                pageTitle.textContent = 'Quản lý Team';
                addUserBtn.style.display = 'none';
                this.loadTeams();
                break;
            case 'kpi':
                pageTitle.textContent = 'KPI Dashboard';
                addUserBtn.style.display = 'none';
                this.loadKpiDashboard();
                break;
            case 'tasks-view':
                pageTitle.textContent = 'Xem công việc';
                addUserBtn.style.display = 'none';
                this.loadTasks();
                break;
        }

        this.currentSection = section;
    }

    async loadUsers() {
        const loading = document.getElementById('loading');
        const errorMessage = document.getElementById('errorMessage');

        try {
            loading.style.display = 'flex';
            errorMessage.style.display = 'none';

            const result = await window.api.getUsers();
            
            if (result.success) {
                this.users = result.users;
                this.renderUsers();
            } else {
                // Check if token expired
                if (result.tokenExpired) {
                    alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                    window.location.href = 'login.html';
                    return;
                }
                throw new Error(result.error || 'Không thể tải danh sách người dùng');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            errorMessage.style.display = 'block';
            document.getElementById('errorText').textContent = error.message;
        } finally {
            loading.style.display = 'none';
        }
    }

    renderUsers() {
        const usersList = document.getElementById('usersList');
        usersList.innerHTML = '';

        this.users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge badge-${user.role}">${this.getRoleText(user.role)}</span></td>
                <td>${new Date(user.created_at).toLocaleDateString('vi-VN')}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="openEditUserModal(${user.id})">
                            <i class="fas fa-edit"></i>
                            Sửa
                        </button>
                        <button class="delete-btn" onclick="openDeleteUserModal(${user.id})">
                            <i class="fas fa-trash"></i>
                            Xóa
                        </button>
                    </div>
                </td>
            `;
            usersList.appendChild(row);
        });
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

    renderTasks() {
        const tasksList = document.getElementById('tasksList');
        tasksList.innerHTML = '';

        this.tasks.forEach(task => {
            const row = document.createElement('tr');
            const startDate = task.start_date ? new Date(task.start_date).toLocaleDateString('vi-VN') : 'N/A';
            const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('vi-VN') : 'N/A';
            
            row.innerHTML = `
                <td>${task.id}</td>
                <td>
                    <div style="font-weight: 500;">${task.title}</div>
                    ${task.description ? `<div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">${task.description}</div>` : ''}
                </td>
                <td>${task.creator ? task.creator.name : 'N/A'}</td>
                <td>${startDate}</td>
                <td>${dueDate}</td>
                <td><span class="badge badge-${task.status}">${this.getStatusText(task.status)}</span></td>
                <td><span class="badge badge-${task.priority}">${this.getPriorityText(task.priority)}</span></td>
            `;
            tasksList.appendChild(row);
        });
    }

    getRoleText(role) {
        const roleTexts = {
            'user': 'User',
            'manager': 'Manager',
            'admin': 'Admin'
        };
        return roleTexts[role] || role;
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

        if (userInitials && this.currentUser) {
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
            userInitials.textContent = initials.toUpperCase();
        }
    }

    handleWindowClick(event) {
        const profileMenu = document.getElementById('profileMenu');
        
        // Close profile menu if clicking outside
        if (profileMenu && !profileMenu.contains(event.target) && !event.target.closest('.profile-button')) {
            profileMenu.classList.remove('show');
        }

        // Close modals if clicking on backdrop
        const modals = ['addUserModal', 'editUserModal', 'profileModal', 'passwordModal', 'confirmDeleteModal'];
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
    if (modal && adminManager.currentUser) {
        // Fill form with current user data
        document.getElementById('profileName').value = adminManager.currentUser.name || '';
        document.getElementById('profileEmail').value = adminManager.currentUser.email || '';
        document.getElementById('profilePhone').value = adminManager.currentUser.phone || '';
        document.getElementById('profileAddress').value = adminManager.currentUser.address || '';
        
        modal.style.display = 'flex';
    }
    
    // Close profile menu
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
        profileMenu.classList.remove('show');
    }
}

function closeProfileModal() {
    adminManager.closeModal('profileModal');
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
    adminManager.closeModal('passwordModal');
    clearPasswordMessages();
    document.getElementById('passwordForm').reset();
}

function openAddUserModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('addUserForm').reset();
        document.getElementById('addUserError').style.display = 'none';
    }
}

function closeAddUserModal() {
    adminManager.closeModal('addUserModal');
}

function openEditUserModal(userId) {
    const user = adminManager.users.find(u => u.id === userId);
    if (!user) return;

    const modal = document.getElementById('editUserModal');
    if (modal) {
        // Fill form with user data
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editUserName').value = user.name;
        document.getElementById('editUserEmail').value = user.email;
        document.getElementById('editUserRole').value = user.role;
        document.getElementById('editUserPassword').value = '';
        document.getElementById('editUserPasswordConfirm').value = '';
        document.getElementById('editUserError').style.display = 'none';
        
        modal.style.display = 'flex';
    }
}

function closeEditUserModal() {
    adminManager.closeModal('editUserModal');
}

function openDeleteUserModal(userId) {
    adminManager.deleteUserId = userId;
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeConfirmDeleteModal() {
    adminManager.deleteUserId = null;
    adminManager.closeModal('confirmDeleteModal');
}

function confirmDeleteUser() {
    if (adminManager.deleteUserId) {
        adminManager.handleDeleteUser(adminManager.deleteUserId);
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
            adminManager.currentUser = { ...adminManager.currentUser, ...result.user };

            // Update profile avatar initials
            adminManager.updateUserProfile();

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

async function handleAddUser(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const form = event.target;

    // Validate password confirmation
    const password = formData.get('password');
    const confirmPassword = formData.get('password_confirmation');

    if (password !== confirmPassword) {
        const errorDiv = document.getElementById('addUserError');
        if (errorDiv) {
            errorDiv.textContent = 'Mật khẩu xác nhận không khớp';
            errorDiv.style.display = 'block';
        }
        return;
    }

    const userData = {
        name: formData.get('name'),
        email: formData.get('email'),
        role: formData.get('role'),
        password: password,
        password_confirmation: confirmPassword
    };

    try {
        document.getElementById('addUserError').style.display = 'none';

        const result = await window.api.addUser(userData);

        if (result.success) {
            closeAddUserModal();
            adminManager.loadUsers(); // Reload users list
            form.reset();
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi thêm người dùng');
        }
    } catch (error) {
        const errorDiv = document.getElementById('addUserError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi thêm người dùng';
            errorDiv.style.display = 'block';
        }
    }
}

async function handleEditUser(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const userId = formData.get('userId');

    // Validate password confirmation if password is provided
    const password = formData.get('password');
    const confirmPassword = formData.get('password_confirmation');

    if (password && password !== confirmPassword) {
        const errorDiv = document.getElementById('editUserError');
        if (errorDiv) {
            errorDiv.textContent = 'Mật khẩu xác nhận không khớp';
            errorDiv.style.display = 'block';
        }
        return;
    }

    const userData = {
        name: formData.get('name'),
        role: formData.get('role')
    };

    if (password) {
        userData.password = password;
        userData.password_confirmation = confirmPassword;
    }

    try {
        document.getElementById('editUserError').style.display = 'none';

        const result = await window.api.updateUser(userId, userData);

        if (result.success) {
            closeEditUserModal();
            adminManager.loadUsers(); // Reload users list
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi cập nhật người dùng');
        }
    } catch (error) {
        const errorDiv = document.getElementById('editUserError');
        if (errorDiv) {
            errorDiv.textContent = error.message || 'Đã xảy ra lỗi khi cập nhật người dùng';
            errorDiv.style.display = 'block';
        }
    }
}

// Add method to AdminManager class
AdminManager.prototype.handleDeleteUser = async function(userId) {
    try {
        const result = await window.api.deleteUser(userId);

        if (result.success) {
            closeConfirmDeleteModal();
            this.loadUsers(); // Reload users list
        } else {
            // Check if token expired
            if (result.tokenExpired) {
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = 'login.html';
                return;
            }
            throw new Error(result.error || 'Có lỗi xảy ra khi xóa người dùng');
        }
    } catch (error) {
        alert('Đã xảy ra lỗi khi xóa người dùng: ' + error.message);
        closeConfirmDeleteModal();
    }
};

// Team Management Methods
AdminManager.prototype.loadTeams = async function() {
    try {
        // For now, show placeholder data
        this.displayTeams([
            {
                id: 1,
                name: 'Development Team',
                manager: { name: 'Manager A' },
                active_members_count: 3,
                is_active: true
            }
        ]);
        this.updateTeamStats();
    } catch (error) {
        console.error('Error loading teams:', error);
    }
};

AdminManager.prototype.displayTeams = function(teams) {
    const tbody = document.getElementById('teamsList');
    if (!tbody) return;

    tbody.innerHTML = teams.map(team => `
        <tr>
            <td>${team.id}</td>
            <td>${team.name}</td>
            <td>${team.manager?.name || 'N/A'}</td>
            <td>${team.active_members_count || 0}</td>
            <td>
                <span class="status-badge ${team.is_active ? 'active' : 'inactive'}">
                    ${team.is_active ? 'Hoạt động' : 'Không hoạt động'}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editTeam(${team.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-info" onclick="manageTeamMembers(${team.id})">
                    <i class="fas fa-users"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteTeam(${team.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
};

AdminManager.prototype.updateTeamStats = function() {
    // Placeholder stats
    document.getElementById('totalTeams').textContent = '1';
    document.getElementById('totalManagers').textContent = '1';
    document.getElementById('totalMembers').textContent = '3';
};

AdminManager.prototype.loadKpiDashboard = async function() {
    try {
        // Placeholder KPI data
        this.updateKpiStats();
        this.loadTopPerformers();
    } catch (error) {
        console.error('Error loading KPI dashboard:', error);
    }
};

AdminManager.prototype.updateKpiStats = function() {
    document.getElementById('avgCompletionRate').textContent = '85%';
    document.getElementById('avgQualityScore').textContent = '4.2';
    document.getElementById('onTimeRate').textContent = '78%';
    document.getElementById('topPerformers').textContent = '5';
};

AdminManager.prototype.loadTopPerformers = function() {
    const topPerformersList = document.getElementById('topPerformersList');
    if (!topPerformersList) return;

    const performers = [
        { rank: 1, name: 'Nguyễn Văn A', score: '95%' },
        { rank: 2, name: 'Trần Thị B', score: '92%' },
        { rank: 3, name: 'Lê Văn C', score: '88%' }
    ];

    topPerformersList.innerHTML = performers.map(performer => `
        <div class="performer-item">
            <div class="performer-rank">${performer.rank}</div>
            <div class="performer-info">
                <div class="performer-name">${performer.name}</div>
                <div class="performer-score">${performer.score}</div>
            </div>
        </div>
    `).join('');
};

// Global functions for team management
function openAddTeamModal() {
    document.getElementById('addTeamModal').style.display = 'flex';
    loadManagersForSelect('teamManager');
}

function closeAddTeamModal() {
    document.getElementById('addTeamModal').style.display = 'none';
    document.getElementById('addTeamForm').reset();
}

function editTeam(teamId) {
    // Placeholder - would load team data and show edit modal
    console.log('Edit team:', teamId);
}

function manageTeamMembers(teamId) {
    document.getElementById('teamMembersModal').style.display = 'flex';
    // Load team members
    console.log('Manage team members:', teamId);
}

function closeTeamMembersModal() {
    document.getElementById('teamMembersModal').style.display = 'none';
}

function deleteTeam(teamId) {
    if (confirm('Bạn có chắc chắn muốn xóa team này?')) {
        console.log('Delete team:', teamId);
    }
}

function loadManagersForSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    // Placeholder managers
    const managers = [
        { id: 1, name: 'Manager A' },
        { id: 2, name: 'Manager B' }
    ];

    select.innerHTML = '<option value="">Chọn Manager</option>' +
        managers.map(manager => `<option value="${manager.id}">${manager.name}</option>`).join('');
}

function handleAddTeam(event) {
    event.preventDefault();
    // Placeholder - would create team
    console.log('Add team form submitted');
    closeAddTeamModal();
}

function handleEditTeam(event) {
    event.preventDefault();
    // Placeholder - would update team
    console.log('Edit team form submitted');
}

function handleAddMember(event) {
    event.preventDefault();
    // Placeholder - would add member to team
    console.log('Add member form submitted');
}

// Initialize admin manager when DOM is loaded
let adminManager;
document.addEventListener('DOMContentLoaded', () => {
    adminManager = new AdminManager();
});
