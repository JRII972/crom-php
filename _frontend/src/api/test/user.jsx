import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost/api'; // Adjust to your API URL

const UserManagement = () => {
  const [csrfToken, setCsrfToken] = useState('');
  const [userId, setUserId] = useState('');
  const [userData, setUserData] = useState(null);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');

  // Fetch CSRF token on mount
  useEffect(() => {
    const fetchCsrfToken = async () => {
      try {
        const response = await axios.post(
          `${API_BASE_URL}/csrf_token.php`,
          {},
          { withCredentials: true }
        );
        setCsrfToken(response.data.csrf_token);
      } catch (error) {
        setMessage('Error fetching CSRF token' + error);
      }
    };
    fetchCsrfToken();
  }, []);

  // Handle login
  const handleLogin = async () => {
    try {
      const response = await axios.post(
        `${API_BASE_URL}/connect_user.php`,
        { username, password },
        {
          headers: { 'X-CSRF-Token': csrfToken },
          withCredentials: true,
        }
      );
      if (response.data.error) {
        setMessage(response.data.error);
      } else {
        setUserId(response.data.user_id);
        setMessage(response.data.message);
      }
    } catch (error) {
      setMessage('Error logging in');
    }
  };

  // Handle get user
  const handleGetUser = async () => {
    try {
      const response = await axios.post(
        `${API_BASE_URL}/get_user.php`,
        { id: userId },
        {
          headers: { 'X-CSRF-Token': csrfToken },
          withCredentials: true,
        }
      );
      if (response.data.error) {
        setMessage(response.data.error);
      } else {
        setUserData(response.data.user);
        setMessage('User data fetched');
      }
    } catch (error) {
      setMessage('Error fetching user');
    }
  };

  // Handle logout
  const handleLogout = async () => {
    try {
      const response = await axios.post(
        `${API_BASE_URL}/disconnect_user.php`,
        {},
        {
          headers: { 'X-CSRF-Token': csrfToken },
          withCredentials: true,
        }
      );
      if (response.data.error) {
        setMessage(response.data.error);
      } else {
        setUserId('');
        setUserData(null);
        setMessage(response.data.message);
      }
    } catch (error) {
      setMessage('Error logging out');
    }
  };

  // Handle create user (example, can be expanded with a form)
  const handleCreateUser = async () => {
    try {
      const response = await axios.post(
        `${API_BASE_URL}/create_user.php`,
        {
          first_name: 'Jane',
          last_name: 'Doe',
          birth_date: '1995-02-15',
          sex: 'F',
          email: 'jane.doe@example.com',
          username: 'janedoe',
          password: 'securepassword',
          user_type: 'REGISTERED',
        },
        {
          headers: { 'X-CSRF-Token': csrfToken },
          withCredentials: true,
        }
      );
      if (response.data.error) {
        setMessage(response.data.error);
      } else {
        setMessage(`User created with ID: ${response.data.user_id}`);
      }
    } catch (error) {
      setMessage('Error creating user');
    }
  };

  return (
    <div className="max-w-md mx-auto p-4">
        <p>CSRF : {csrfToken}</p>
      <h1 className="text-2xl font-bold mb-4">User Management</h1>
      {!userId ? (
        <div className="mb-4">
          <input
            type="text"
            placeholder="Username"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            className="border p-2 w-full mb-2 rounded"
          />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="border p-2 w-full mb-2 rounded"
          />
          <button
            onClick={handleLogin}
            className="bg-blue-500 text-white p-2 rounded hover:bg-blue-600"
          >
            Login
          </button>
        </div>
      ) : (
        <div className="mb-4">
          <button
            onClick={handleGetUser}
            className="bg-green-500 text-white p-2 rounded mr-2 hover:bg-green-600"
          >
            Get User Data
          </button>
          <button
            onClick={handleLogout}
            className="bg-red-500 text-white p-2 rounded hover:bg-red-600"
          >
            Logout
          </button>
          <button
            onClick={handleCreateUser}
            className="bg-purple-500 text-white p-2 rounded mt-2 hover:bg-purple-600"
          >
            Create Sample User
          </button>
        </div>
      )}
      {userData && (
        <div className="border p-4 rounded">
          <h2 className="text-xl font-semibold">User Data</h2>
          <p><strong>Name:</strong> {userData.first_name} {userData.last_name}</p>
          <p><strong>Email:</strong> {userData.email}</p>
          <p><strong>Username:</strong> {userData.username}</p>
          <p><strong>User Type:</strong> {userData.user_type}</p>
        </div>
      )}
      {message && <p className="mt-4 text-red-500">{message}</p>}
    </div>
  );
};

export default UserManagement;