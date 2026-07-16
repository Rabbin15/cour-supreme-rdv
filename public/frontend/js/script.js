// ============================================
// SCRIPT.JS - Fonctions communes
// ============================================

const API_URL = 'http://127.0.0.1:8000/api';

// Fonction pour faire des appels API
async function fetchAPI(endpoint, options = {}) {
    const url = `${API_URL}${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        ...options
    };

    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            // Afficher l'erreur complète dans la console
            console.error('❌ ERREUR API :', {
                status: response.status,
                statusText: response.statusText,
                url: url,
                data: data
            });
            // Lancer l'erreur avec le message renvoyé par le serveur
            throw new Error(data.message || data.error || 'Une erreur est survenue');
        }
        return data;
    } catch (error) {
        console.error('❌ Erreur fetchAPI :', error);
        throw error;
    }
}