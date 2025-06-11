// auth.js
import api from "./apiService.js";

const TOKEN_KEY = "authToken"; // Example key for localStorage

async function registerUser(userData) {
  // userData = { email, password, first_name, last_name }
  try {
    const response = await api.post("/user/register", userData);
    // Handle successful registration (e.g., show message, redirect to login)
    console.log("Registration successful:", response);
    return response;
  } catch (error) {
    console.error("Registration failed:", error.message, error.data);
    // Display error message to the user
    alert(`Registration failed: ${error.message} ${error.data?.message || ""}`);
    throw error;
  }
}

async function loginUser(credentials) {
  // credentials = { email, password }
  try {
    const response = await api.post("/user/login", credentials);
    // Assuming backend returns a token or session info
    if (response.data && response.data.token) {
      localStorage.setItem(TOKEN_KEY, response.data.token);
    }
    console.log("Login successful:", response);
    // Update UI, redirect, etc.
    return response;
  } catch (error) {
    console.error("Login failed:", error.message, error.data);
    alert(`Login failed: ${error.message} ${error.data?.message || ""}`);
    throw error;
  }
}

function logoutUser() {
  localStorage.removeItem(TOKEN_KEY);
  // Potentially call a backend logout endpoint
  // api.post('/user/logout');
  console.log("User logged out");
  // Update UI, redirect to login page
}

function isAuthenticated() {
  return !!localStorage.getItem(TOKEN_KEY);
}

function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

export { registerUser, loginUser, logoutUser, isAuthenticated, getToken };
