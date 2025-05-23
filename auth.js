// Connexion
async function login(login, password, keepLoggedIn = false) {
    const response = await fetch('/api/utilisateurs/connexion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ login, mot_de_passe: password, keep_logged_in: keepLoggedIn })
    });
    const data = await response.json();
    if (data.status === 'success') {
        localStorage.setItem('jwt', data.data.token);
        if (data.data.refresh_token) {
            localStorage.setItem('refresh_token', data.data.refresh_token);
        }
    }
    return data;
}

// Renouvellement du token
async function refreshToken() {
    const refreshToken = localStorage.getItem('refresh_token');
    if (!refreshToken) {
        throw new Error('Aucun refresh token disponible');
    }
    const response = await fetch('/api/utilisateurs/refresh', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refresh_token: refreshToken })
    });
    const data = await response.json();
    if (data.status === 'success') {
        localStorage.setItem('jwt', data.data.token);
    }
    return data;
}

// Requête authentifiée
async function fetchWithAuth(url, options = {}) {
    const token = localStorage.getItem('jwt');
    const csrfToken = localStorage.getItem('csrf_token');
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'X-CSRF-Token': csrfToken
    };
    try {
        const response = await fetch(url, { ...options, headers });
        if (response.status === 401) {
            // Token expiré, essayer de renouveler
            await refreshToken();
            // Réessayer la requête avec le nouveau token
            headers['Authorization'] = `Bearer ${localStorage.getItem('jwt')}`;
            return fetch(url, { ...options, headers });
        }
        return response;
    } catch (error) {
        throw new Error('Erreur réseau ou authentification');
    }
}