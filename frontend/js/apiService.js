// apiService.js
const API_BASE_URL = "/DNTST_REG/backend"; // Or your full backend URL

async function request(endpoint, method = "GET", data = null, headers = {}) {
  const config = {
    method: method,
    headers: {
      "Content-Type": "application/json",
      ...headers,
    },
  };

  if (data && (method === "POST" || method === "PUT")) {
    config.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
    if (!response.ok) {
      // Try to parse error from backend if available
      let errorData;
      try {
        errorData = await response.json();
      } catch (e) {
        // If no JSON error body, use status text
        errorData = { message: response.statusText };
      }
      throw { status: response.status, ...errorData };
    }
    // If response is 204 No Content, there might not be a body
    if (response.status === 204) {
      return null;
    }
    return await response.json();
  } catch (error) {
    console.error(
      `API Error (${method} ${endpoint}):`,
      error.status,
      error.message,
      error.data || ""
    );
    throw error; // Re-throw to be caught by the caller
  }
}

export default {
  get: (endpoint, headers) => request(endpoint, "GET", null, headers),
  post: (endpoint, data, headers) => request(endpoint, "POST", data, headers),
  put: (endpoint, data, headers) => request(endpoint, "PUT", data, headers),
  delete: (endpoint, headers) => request(endpoint, "DELETE", null, headers),
};
