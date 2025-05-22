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
      addTask: (taskData) => ipcRenderer.invoke('add-task', taskData),
      logout: () => ipcRenderer.invoke('logout'),
      checkApiConnection: () => ipcRenderer.invoke('check-api-connection')
    }
  );
  console.log('API methods exposed successfully');
} catch (error) {
  console.error('Error in preload script:', error);
}
