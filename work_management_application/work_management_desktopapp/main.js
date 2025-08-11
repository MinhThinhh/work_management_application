const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const axios = require('axios');
const keytar = require('keytar');
const Store = require('electron-store');

// Initialize electron-store
const store = new Store();

// Define constants
const API_BASE_URL = 'http://127.0.0.1:8000/api';
const SERVICE_NAME = 'work-management-app';

// Check API connection
async function checkApiConnection() {
  try {
    console.log('Checking API connection to:', API_BASE_URL);
    const response = await axios.get(`http://127.0.0.1:8000/health-check`, { timeout: 5000 });
    console.log('API connection successful:', response.data);
    return true;
  } catch (error) {
    console.error('API connection check failed:', error.message);
    return false;
  }
}

// Enable error logging
process.on('uncaughtException', (error) => {
  console.error('Uncaught Exception:', error);
});

// Debug logs
console.log('Starting application...');
console.log('Current directory:', __dirname);

let mainWindow;

function createWindow() {
  console.log('Creating window...');
  try {
    mainWindow = new BrowserWindow({
      width: 1000,
      height: 800,
      webPreferences: {
        nodeIntegration: false,
        contextIsolation: true,
        preload: path.join(__dirname, 'preload.js')
      }
    });

    console.log('Loading file:', path.join(__dirname, 'src/login.html'));
    mainWindow.loadFile(path.join(__dirname, 'src/login.html'));

    // Open DevTools in development environment
    mainWindow.webContents.openDevTools();

    mainWindow.on('closed', () => {
      mainWindow = null;
    });
  } catch (error) {
    console.error('Error creating window:', error);
  }
}

// Add API connection check handler
ipcMain.handle('check-api-connection', async () => {
  return await checkApiConnection();
});

app.whenReady().then(() => {
  createWindow();

  // Check API connection on startup
  checkApiConnection().then(isConnected => {
    console.log('API connection status:', isConnected ? 'Connected' : 'Not connected');
  });

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

// Handle login
ipcMain.handle('login', async (_, { email, password }) => {
  try {
    console.log('Attempting login with:', email);
    const response = await axios.post(`${API_BASE_URL}/desktop-login`, {
      email,
      password
    });

    console.log('Login response:', response.data);

    if (response.data.success && response.data.token) {
      // Save token to keychain
      await keytar.setPassword(SERVICE_NAME, email, response.data.token);

      // Save user info to store
      store.set('user', response.data.user);

      return { success: true, user: response.data.user };
    } else {
      return { success: false, error: response.data.error || 'Login failed' };
    }
  } catch (error) {
    console.error('Login error:', error);

    // Check if connection error
    if (error.code === 'ECONNREFUSED') {
      return {
        success: false,
        error: 'Cannot connect to API server. Please make sure the server is running.',
        connectionError: true
      };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Login failed'
    };
  }
});

// Get token from keychain
ipcMain.handle('get-token', async () => {
  const user = store.get('user');
  if (!user || !user.email) {
    return null;
  }

  try {
    return await keytar.getPassword(SERVICE_NAME, user.email);
  } catch (error) {
    console.error('Error getting token:', error);
    return null;
  }
});

// Check token
ipcMain.handle('check-token', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { valid: false, error: 'Token not found' };
  }

  try {
    const response = await axios.get(`${API_BASE_URL}/token-info`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    return response.data;
  } catch (error) {
    console.error('Token check error:', error);
    return { valid: false, error: error.message };
  }
});





// Get users list (for managers and admins)
ipcMain.handle('get-users', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || (user.role !== 'manager' && user.role !== 'admin')) {
    return { success: false, error: 'Unauthorized' };
  }

  try {
    console.log('Fetching users with token:', token.substring(0, 10) + '...');

    let endpoint;
    if (user.role === 'admin') {
      endpoint = `${API_BASE_URL}/admin/users`;
    } else {
      endpoint = `${API_BASE_URL}/manager/users`;
    }

    console.log('Final endpoint for users:', endpoint);

    const response = await axios.get(endpoint, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    console.log('Users response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Get users error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not fetch users'
    };
  }
});

// Get tasks list (for all roles)
ipcMain.handle('get-tasks', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user) {
    return { success: false, error: 'User info not found' };
  }

  try {
    console.log('Fetching tasks with token:', token.substring(0, 10) + '...');
    console.log('User role:', user.role);

    let endpoint;
    if (user.role === 'admin') {
      // Admin xem tất cả tasks qua API thường
      endpoint = `${API_BASE_URL}/tasks`;
    } else if (user.role === 'manager') {
      // Manager xem tất cả tasks qua manager API endpoint
      endpoint = `${API_BASE_URL}/manager/tasks`;
    } else {
      // User chỉ xem tasks của mình
      endpoint = `${API_BASE_URL}/desktop-tasks`;
    }

    console.log('Final endpoint for tasks:', endpoint);

    const response = await axios.get(endpoint, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    console.log('Tasks response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Get tasks error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not fetch tasks'
    };
  }
});

// Get user data
ipcMain.handle('get-user-data', async () => {
  const user = store.get('user');
  if (!user) {
    return { success: false, error: 'User data not found' };
  }
  return { success: true, user: user };
});

// Update profile
ipcMain.handle('update-profile', async (_, profileData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Updating profile with token:', token.substring(0, 10) + '...');
    console.log('Profile data:', profileData);

    const response = await axios.put(`${API_BASE_URL}/desktop-update-profile`, profileData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Update profile response:', response.data);

    // Update local user data if successful
    if (response.data.success && response.data.user) {
      store.set('user', response.data.user);
    }

    return response.data;
  } catch (error) {
    console.error('Update profile error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not update profile'
    };
  }
});

// Change password
ipcMain.handle('change-password', async (_, passwordData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Changing password with token:', token.substring(0, 10) + '...');

    const response = await axios.post(`${API_BASE_URL}/desktop-change-password`, passwordData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Change password response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Change password error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not change password'
    };
  }
});

// Add user (Admin only)
ipcMain.handle('add-user', async (_, userData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || user.role !== 'admin') {
    return { success: false, error: 'Unauthorized. Admin access required.' };
  }

  try {
    console.log('Adding user with token:', token.substring(0, 10) + '...');
    console.log('User data:', userData);

    const response = await axios.post(`${API_BASE_URL}/admin/users`, userData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Add user response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Add user error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not add user'
    };
  }
});

// Update user (Admin only)
ipcMain.handle('update-user', async (_, userId, userData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || user.role !== 'admin') {
    return { success: false, error: 'Unauthorized. Admin access required.' };
  }

  try {
    console.log('Updating user', userId, 'with token:', token.substring(0, 10) + '...');
    console.log('User data:', userData);

    const response = await axios.put(`${API_BASE_URL}/admin/users/${userId}`, userData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Update user response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Update user error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not update user'
    };
  }
});

// Delete user (Admin only)
ipcMain.handle('delete-user', async (_, userId) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || user.role !== 'admin') {
    return { success: false, error: 'Unauthorized. Admin access required.' };
  }

  try {
    console.log('Deleting user', userId, 'with token:', token.substring(0, 10) + '...');

    const response = await axios.delete(`${API_BASE_URL}/admin/users/${userId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    console.log('Delete user response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Delete user error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not delete user'
    };
  }
});

// Add task (All authenticated users)
ipcMain.handle('add-task', async (_, taskData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user) {
    return { success: false, error: 'User not found' };
  }

  try {
    console.log('Adding task with token:', token.substring(0, 10) + '...');
    console.log('Task data:', taskData);

    // Use different endpoints based on user role
    let endpoint;
    if (user.role === 'manager' || user.role === 'admin') {
      endpoint = `${API_BASE_URL}/manager/tasks`;
    } else {
      endpoint = `${API_BASE_URL}/desktop-tasks`;
    }

    const response = await axios.post(endpoint, taskData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Add task response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Add task error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not add task'
    };
  }
});

// Update task (Manager only)
ipcMain.handle('update-task', async (_, taskId, taskData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || (user.role !== 'manager' && user.role !== 'admin')) {
    return { success: false, error: 'Unauthorized. Manager or admin access required.' };
  }

  try {
    console.log('Updating task', taskId, 'with token:', token.substring(0, 10) + '...');
    console.log('Task data:', taskData);

    const response = await axios.put(`${API_BASE_URL}/manager/tasks/${taskId}`, taskData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Update task response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Update task error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not update task'
    };
  }
});



// Delete task (Manager only)
ipcMain.handle('delete-task', async (_, taskId) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || (user.role !== 'manager' && user.role !== 'admin')) {
    return { success: false, error: 'Unauthorized. Manager or admin access required.' };
  }

  try {
    console.log('Deleting task', taskId, 'with token:', token.substring(0, 10) + '...');

    const response = await axios.delete(`${API_BASE_URL}/manager/tasks/${taskId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    console.log('Delete task response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Delete task error:', error);

    // Check if token expired
    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not delete task'
    };
  }
});

// Get users with teams (Admin only)
ipcMain.handle('get-users-with-teams', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || user.role !== 'admin') {
    return { success: false, error: 'Unauthorized. Admin access required.' };
  }

  try {
    console.log('Getting users with teams with token:', token.substring(0, 10) + '...');

    const response = await axios.get(`${API_BASE_URL}/desktop-users-with-teams`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Get users with teams response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Get users with teams error:', error);

    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not get users with teams'
    };
  }
});

// Get teams
ipcMain.handle('get-teams', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Getting teams with token:', token.substring(0, 10) + '...');

    const response = await axios.get(`${API_BASE_URL}/desktop-teams`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Get teams response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Get teams error:', error);

    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not get teams'
    };
  }
});

// Get managers
ipcMain.handle('get-managers', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Getting managers with token:', token.substring(0, 10) + '...');

    const response = await axios.get(`${API_BASE_URL}/admin/users`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    if (response.data.success) {
      const managers = response.data.users.filter(user => user.role === 'manager');
      return { success: true, managers };
    }

    return response.data;
  } catch (error) {
    console.error('Get managers error:', error);

    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not get managers'
    };
  }
});

// Add team (Admin only)
ipcMain.handle('add-team', async (_, teamData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  const user = store.get('user');
  if (!user || user.role !== 'admin') {
    return { success: false, error: 'Unauthorized. Admin access required.' };
  }

  try {
    console.log('Adding team with token:', token.substring(0, 10) + '...');
    console.log('Team data:', teamData);

    const response = await axios.post(`${API_BASE_URL}/desktop-teams`, teamData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('Add team response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Add team error:', error);

    if (error.response && error.response.status === 401) {
      return { success: false, error: 'Token expired or invalid', tokenExpired: true };
    }

    return {
      success: false,
      error: error.response?.data?.error || error.message || 'Could not add team'
    };
  }
});

// Logout
ipcMain.handle('logout', async () => {
  const user = store.get('user');
  if (user && user.email) {
    try {
      await keytar.deletePassword(SERVICE_NAME, user.email);
    } catch (error) {
      console.error('Error deleting token:', error);
    }
  }

  store.delete('user');
  return { success: true };
});
