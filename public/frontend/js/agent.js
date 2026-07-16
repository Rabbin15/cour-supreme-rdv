// ============================================
// AGENT.JS - Dashboard agent (version corrigée)
// ============================================

let currentAgent = null;
let currentCreneaux = [];
let currentRdvs = [];

// Vérifier si l'agent est connecté
const token = localStorage.getItem('token');
if (!token) {
    window.location.href = 'login-agent.html';
}

// ============================================
// INITIALISATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard chargé');
    initAgent();
    initTabs();
    initCreneauForm();
    initRefusForm();
    
    loadData();
});

// ============================================
// CHARGER LES INFOS DE L'AGENT
// ============================================
function initAgent() {
    try {
        currentAgent = JSON.parse(localStorage.getItem('agent'));
        if (currentAgent) {
            document.getElementById('agent-name').textContent = `${currentAgent.prenom} ${currentAgent.nom}`;
            document.getElementById('agent-structure').textContent = currentAgent.structure?.nom_structure || 'Structure';
            document.getElementById('avatar').textContent = currentAgent.prenom.charAt(0).toUpperCase();
            console.log('✅ Agent chargé :', currentAgent.email);
        }
    } catch (e) {
        console.error('❌ Erreur chargement agent:', e);
    }
}

// ============================================
// ONGLETS
// ============================================
function initTabs() {
    document.querySelectorAll('.dashboard-tabs button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.dashboard-tabs button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
}

// ============================================
// DÉCONNEXION - VERSION CORRIGÉE
// ============================================



// ============================================
// CHARGEMENT DES DONNÉES
// ============================================
async function loadData() {
    await loadCreneaux();
    await loadRdvs();
}

async function loadCreneaux() {
    const list = document.getElementById('creneaux-list');
    try {
        console.log('📡 Chargement des créneaux...');
        const data = await fetchAPI('/agent/creneaux');
        console.log('📦 Créneaux reçus :', data);
        currentCreneaux = data.data || [];
        renderCreneaux();
    } catch (error) {
        console.error('❌ Erreur chargement créneaux :', error);
        list.innerHTML = `<p class="empty-msg">Erreur : ${error.message}</p>`;
    }
}

function renderCreneaux() {
    const list = document.getElementById('creneaux-list');
    if (currentCreneaux.length === 0) {
        list.innerHTML = '<p class="empty-msg">Aucun créneau programmé.</p>';
        return;
    }
    list.innerHTML = currentCreneaux.map(c => `
        <div class="creneau-item">
            <div class="info">
                <span class="day">${c.jour_semaine}</span>
                <span class="hour">${c.heure_debut} - ${c.heure_fin}</span>
                <span class="status ${c.est_disponible ? 'status-accepte' : 'status-refuse'}">
                    ${c.est_disponible ? '✅ Disponible' : '❌ Indisponible'}
                </span>
            </div>
        </div>
    `).join('');
}

async function loadRdvs() {
    const list = document.getElementById('rdv-list');
    try {
        console.log('📡 Chargement des rendez-vous...');
        const data = await fetchAPI('/agent/rendez-vous');
        console.log('📦 RDV reçus :', data);
        currentRdvs = data.data || [];
        renderRdvs();
    } catch (error) {
        console.error('❌ Erreur chargement RDV :', error);
        list.innerHTML = `<p class="empty-msg">Erreur : ${error.message}</p>`;
    }
}

function renderRdvs() {
    const list = document.getElementById('rdv-list');
    if (currentRdvs.length === 0) {
        list.innerHTML = '<p class="empty-msg">Aucune demande de rendez-vous reçue.</p>';
        return;
    }
    list.innerHTML = currentRdvs.map(r => {
        const statusClass = `status-${r.statut}`;
        const statusLabels = {
            'en_attente': '⏳ En attente',
            'accepte': '✅ Accepté',
            'refuse': '❌ Refusé',
            'annule': '🚫 Annulé',
            'termine': '🏁 Terminé'
        };
        const actions = r.statut === 'en_attente' ? `
            <div class="rdv-actions">
                <button class="btn-sm btn-accept" onclick="accepterRdv(${r.id_rdv})">Accepter</button>
                <button class="btn-sm btn-refuse" onclick="ouvrirRefus(${r.id_rdv})">Refuser</button>
            </div>
        ` : `<span class="status ${statusClass}">${statusLabels[r.statut] || r.statut}</span>`;

        return `
            <div class="rdv-item">
                <div class="info">
                    <span class="name">${r.nom_complet}</span>
                    <span class="email">${r.email_citoyen}</span>
                    <span class="motif">"${r.motif}"</span>
                    <span class="status ${statusClass}">${statusLabels[r.statut] || r.statut}</span>
                </div>
                ${actions}
            </div>
        `;
    }).join('');
}

// ============================================
// GESTION DES CRÉNEAUX (Ajout uniquement)
// ============================================
function initCreneauForm() {
    const btnAdd = document.getElementById('btn-add-creneau');
    if (btnAdd) {
        btnAdd.style.display = 'none'; // Cacher le bouton car les créneaux sont fixes
    }
}

// ============================================
// GESTION DES RENDEZ-VOUS
// ============================================
function initRefusForm() {
    const cancelBtn = document.getElementById('modal-refus-cancel');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('modal-refus').classList.remove('active');
        });
    }

    document.getElementById('modal-refus').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    document.getElementById('refus-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('refus-rdv-id').value;
        const motif = document.getElementById('refus-motif').value.trim();
        if (!motif) {
            alert('Veuillez indiquer un motif de refus.');
            return;
        }
        try {
            const result = await fetchAPI(`/agent/rendez-vous/${id}/refuser`, {
                method: 'PUT',
                body: JSON.stringify({ motif_refus: motif })
            });
            if (result.status === 'success') {
                document.getElementById('modal-refus').classList.remove('active');
                await loadRdvs();
            }
        } catch (error) {
            alert('Erreur: ' + error.message);
        }
    });
}

async function accepterRdv(id) {
    if (!confirm('Accepter ce rendez-vous ?')) return;
    try {
        const result = await fetchAPI(`/agent/rendez-vous/${id}/accepter`, {
            method: 'PUT'
        });
        if (result.status === 'success') {
            await loadRdvs();
        }
    } catch (error) {
        alert('Erreur: ' + error.message);
    }
}

function ouvrirRefus(id) {
    document.getElementById('refus-rdv-id').value = id;
    document.getElementById('refus-motif').value = '';
    document.getElementById('modal-refus').classList.add('active');
}
// ============================================
// DÉCONNEXION DIRECTE (appelée depuis le HTML)
// ============================================
function deconnexionDirecte() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        localStorage.removeItem('token');
        localStorage.removeItem('agent');
        window.location.href = 'login-agent.html';
    }
}