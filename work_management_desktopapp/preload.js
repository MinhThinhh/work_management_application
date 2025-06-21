const { contextBridge, ipcRenderer } = require('electron');

console.log('Preload script starting...');

try {
  // Expose protected methods that allow the renderer process to use
  // the ipcRenderer without exposing the entire object
  contextBridge.exposeInMainWorld(
    'api', {
      login: (credentials) => ipcRenderer.invoke('login', credentials),
      getToken: () => ipcRenderer.invoke('get-token'),
      checkToken: () => ipcRenderer.invoke('check-token'),
      getTasks: () => ipcRenderer.invoke('get-tasks'),
      getUsers: () => ipcRenderer.invoke('get-users'),
      getUserData: () => ipcRenderer.invoke('get-user-data'),
      updateProfile: (profileData) => ipcRenderer.invoke('update-profile', profileData),
      changePassword: (passwordData) => ipcRenderer.invoke('change-password', passwordData),
      addUser: (userData) => ipcRenderer.invoke('add-user', userData),
      updateUser: (userId, userData) => ipcRenderer.invoke('update-user', userId, userData),
      deleteUser: (userId) => ipcRenderer.invoke('delete-user', userId),
      addTask: (taskData) => ipcRenderer.invoke('add-task', taskData),
      updateTask: (taskId, taskData) => ipcRenderer.invoke('update-task', taskId, taskData),
      deleteTask: (taskId) => ipcRenderer.invoke('delete-task', taskId),
      getUsers: () => ipcRenderer.invoke('get-users'),
      logout: () => ipcRenderer.invoke('logout'),
      checkApiConnection: () => ipcRenderer.invoke('check-api-connection')
    }
  );
  console.log('API methods exposed successfully');
} catch (error) {
  console.error('Error in preload script:', error);
}
