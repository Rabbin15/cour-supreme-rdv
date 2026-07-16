// ============================================
// CITOYEN.JS - Gestion des rendez-vous côté public
// ============================================

// Charger les structures au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    chargerStructures();
});

async function chargerStructures() {
    try {
        const data = await fetchAPI('/structures');
        const structures = data.data;
        const grid = document.getElementById('structures-grid');
        
        if (grid) {
            grid.innerHTML = structures.map(structure => `
                <div class="structure-card" onclick="voirCreneaux(${structure.id_structure})">
                    <h4>${structure.nom_structure}</h4>
                    <p>${structure.description || 'Structure de la Cour Suprême'}</p>
                    <span class="badge">Prendre RDV →</span>
                </div>
            `).join('');
        }
    } catch (error) {
        document.getElementById('structures-grid').innerHTML = 
            '<p>Erreur de chargement des structures. Veuillez réessayer.</p>';
    }
}

function voirCreneaux(id_structure) {
    // Rediriger vers la page des créneaux
    window.location.href = `creneaux.html?id=${id_structure}`;
}