// main.js
import {
  registerUser,
  loginUser,
  logoutUser,
  isAuthenticated,
} from "./auth.js";
// import { fetchAppointments } from './appointments.js'; // If you have this feature

document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM fully loaded and parsed");
  updateUIBasedOnAuthState();

  // Example: Registration Form
  const registrationForm = document.getElementById("registration-form");
  if (registrationForm) {
    registrationForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const formData = new FormData(registrationForm);
      const userData = Object.fromEntries(formData.entries());
      try {
        await registerUser(userData);
        // Handle post-registration UI (e.g., show success, clear form)
        registrationForm.reset();
        alert("Registration successful! Please log in.");
      } catch (e) {
        // Error already alerted in auth.js, maybe do more here if needed
      }
    });
  }

  // Example: Login Form
  const loginForm = document.getElementById("login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const formData = new FormData(loginForm);
      const credentials = Object.fromEntries(formData.entries());
      try {
        await loginUser(credentials);
        updateUIBasedOnAuthState();
        // Redirect or update view
        alert("Login successful!");
      } catch (e) {
        // Error already alerted in auth.js
      }
    });
  }

  // Example: Logout Button
  const logoutButton = document.getElementById("logout-button");
  if (logoutButton) {
    logoutButton.addEventListener("click", () => {
      logoutUser();
      updateUIBasedOnAuthState();
    });
  }
});

function updateUIBasedOnAuthState() {
  const loggedInElements = document.querySelectorAll(".logged-in");
  const loggedOutElements = document.querySelectorAll(".logged-out");

  if (isAuthenticated()) {
    loggedInElements.forEach((el) => (el.style.display = "")); // Or 'block', 'flex', etc.
    loggedOutElements.forEach((el) => (el.style.display = "none"));
  } else {
    loggedInElements.forEach((el) => (el.style.display = "none"));
    loggedOutElements.forEach((el) => (el.style.display = ""));
  }
}
