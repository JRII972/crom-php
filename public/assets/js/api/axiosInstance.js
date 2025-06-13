const DOMAINE = '/api';

const axiosInstance = axios.create({
  baseURL: DOMAINE,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Store CSRF token in memory
let csrfToken = null;

// Function to fetch CSRF token
const fetchCsrfToken = async () => {
  try {
    const response = await axios.get(`${DOMAINE}/csrf_token`);
    csrfToken = response.data.csrf_token;
    return csrfToken;
  } catch (error) {
    console.error('Failed to fetch CSRF token:', error);
    return null;
  }
};

// Request interceptor to add JWT and CSRF tokens
axiosInstance.interceptors.request.use(
  async (config) => {
    // Add JWT token if available
    const token = localStorage.getItem('token');
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // Add CSRF token for non-safe methods (POST, PUT, DELETE, etc.)
    if (config.method && ['post', 'put', 'delete'].includes(config.method.toLowerCase())) {
      if (!csrfToken) {
        // Fetch CSRF token if not already stored
        await fetchCsrfToken();
      }
      if (csrfToken && config.headers) {
        config.headers['X-CSRF-Token'] = csrfToken;
      }
    }

    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor for token refresh and error handling
axiosInstance.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      try {
        const refreshToken = localStorage.getItem('refreshToken');
        const response = await axios.post(`${DOMAINE}/utilisateurs/refresh`, {
          refresh_token: refreshToken,
        });
        const { token } = response.data.data;
        localStorage.setItem('token', token);
        originalRequest.headers.Authorization = `Bearer ${token}`;
        // Re-fetch CSRF token after refresh in case it's tied to the session
        await fetchCsrfToken();
        if (
          csrfToken &&
          originalRequest.method &&
          ['post', 'put', 'delete'].includes(originalRequest.method.toLowerCase())
        ) {
          originalRequest.headers['X-CSRF-Token'] = csrfToken;
        }
        return axiosInstance(originalRequest);
      } catch (refreshError) {
        localStorage.removeItem('token');
        localStorage.removeItem('refreshToken');
        window.location.href = '/login?redirect=' + window.location.pathname;
        return Promise.reject(refreshError);
      }
    }
    // Handle CSRF token invalidation (e.g., 403 Forbidden)
    if (error.response?.status === 403 && error.response?.data?.message?.includes('CSRF')) {
      // Re-fetch CSRF token and retry the request once
      if (!originalRequest._csrfRetry) {
        originalRequest._csrfRetry = true;
        await fetchCsrfToken();
        if (
          csrfToken &&
          originalRequest.method &&
          ['post', 'put', 'delete'].includes(originalRequest.method.toLowerCase())
        ) {
          originalRequest.headers['X-CSRF-Token'] = csrfToken;
        }
        return axiosInstance(originalRequest);
      }
    }
    return Promise.reject(error);
  }
);


export default axiosInstance;
