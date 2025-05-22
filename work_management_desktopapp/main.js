const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const axios = require('axios');
const keytar = require('keytar');
const Store = require('electron-store');

// Initialize electron-store
const store = new Store();

// Define constants
const API_BASE_URL = 'http://127.0.0.1:8000';
const SERVICE_NAME = 'work-management-app';

// Check API connection
async function checkApiConnection() {
  try {
    console.log('Checking API connection to:', API_BASE_URL);
    const response = await axios.get(`${API_BASE_URL}/health-check`, { timeout: 5000 });
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
      store.set('user', {
        email: email,
        id: response.data.user.id,
        role: response.data.user.role
      });

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

// Get tasks list
ipcMain.handle('get-tasks', async () => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Fetching tasks with token:', token.substring(0, 10) + '...');
    const response = await axios.get(`${API_BASE_URL}/desktop-tasks`, {
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

// Add new task
ipcMain.handle('add-task', async (_, taskData) => {
  const token = await keytar.getPassword(SERVICE_NAME, store.get('user.email'));
  if (!token) {
    return { success: false, error: 'Token not found' };
  }

  try {
    console.log('Adding task with token:', token.substring(0, 10) + '...');
    console.log('Task data:', taskData);

    const response = await axios.post(`${API_BASE_URL}/desktop-tasks`, taskData, {
      headers: {
        'Authorization': `Bearer ${token}`
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
