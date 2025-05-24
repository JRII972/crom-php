<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDR Assoc React App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/react@18/umd/react.development.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@babel/standalone@7.27.1/babel.min.js"></script>
</head>
<body>
    <div id="root" class="container mx-auto p-4"></div>
    <script type="text/babel">
        const { useState, useEffect } = React;

        const App = () => {$$
            const [csrfToken, setCsrfToken] = useState('');
            const [userId, setUserId] = useState('');
            const [userData, setUserData] = useState(null);
            const [username, setUsername] = useState('');
            const [password, setPassword] = useState('');
            const [message, setMessage] = useState('');
            
            // Fetch CSRF token on mount
            useEffect(() => {
                fetch('http://localhost/api/csrf_token.php', {
                    method: 'POST',
                    credentials: 'include'
                })
                    .then(res => res.json())
                    .then(data => setCsrfToken(data.csrf_token))
                    .catch(err => setMessage('Error fetching CSRF token'));
            }, []);

            // Handle login
            const handleLogin = () => {
                fetch('http://localhost/api/connect_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    credentials: 'include',
                    body: JSON.stringify({ username, password })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            setMessage(data.error);
                        } else {
                            setUserId(data.user_id);
                            setMessage(data.message);
                        }
                    })
                    .catch(err => setMessage('Error logging in'));
            };

            // Handle get user
            const handleGetUser = () => {
                fetch('http://localhost/api/get_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    credentials: 'include',
                    body: JSON.stringify({ id: userId })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            setMessage(data.error);
                        } else {
                            setUserData(data.user);
                            setMessage('User data fetched');
                        }
                    })
                    .catch(err => setMessage('Error fetching user'));
            };

            return (
                <div className="max-w-md mx-auto">
                    <h1 className="text-2xl font-bold mb-4">User Management</h1>
                    <div className="mb-4">
                        <input
                            type="text"
                            placeholder="Username"
                            value={username}
                            onChange={e => setUsername(e.target.value)}
                            className="border p-2 w-full mb-2"
                        />
                        <input
                            type="password"
                            placeholder="Password"
                            value={password}
                            onChange={e => setPassword(e.target.value)}
                            className="border p-2 w-full mb-2"
                        />
                        <button
                            onClick={handleLogin}
                            className="bg-blue-500 text-white p-2 rounded"
                        >
                            Login
                        </button>
                    </div>
                    {userId && (
                        <div className="mb-4">
                            <button
                                onClick={handleGetUser}
                                className="bg-green-500 text-white p-2 rounded"
                            >
                                Get User Data
                            </button>
                        </div>
                    )}
                    {userData && (
                        <div className="border p-4">
                            <h2 className="text-xl">User Data</h2>
                            <p><strong>Name:</strong> {userData.first_name} {userData.last_name}</p>
                            <p><strong>Email:</strong> {userData.email}</p>
                            <p><strong>Username:</strong> {userData.username}</p>
                        </div>
                    )}
                    {message && <p className="mt-4 text-red-500">{message}</p>}
                </div>
            );
        };

        ReactDOM.render(<App />, document.getElementById('root'));
    </script>
</body>
</html>