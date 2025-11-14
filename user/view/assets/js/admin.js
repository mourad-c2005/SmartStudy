// view/assets/js/admin.js
// Gère :
// - Chargement dynamique des utilisateurs (admin/users.html)
// - Vérification de session (redirection si non-admin)
// - Bouton déconnexion
// - Mise à jour du nom/photo dans le header

document.addEventListener('DOMContentLoaded', async () => {
  const user = await checkSession();
  if (!user) {
    window.location.href = 'login.html';
    return;
  }

  // Mise à jour du header (nom + rôle)
  const nameEl = document.querySelector('.user .fw-bold');
  const roleEl = document.querySelector('.user small');
  const photoEl = document.querySelector('.user img');
  if (nameEl) nameEl.textContent = user.nom;
  if (roleEl) roleEl.textContent = capitalize(user.role);
  if (photoEl) photoEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.nom)}&background=4CAF50&color=fff`;

  // Si page admin/users.html → charger les utilisateurs
  if (location.pathname.includes('admin/users.html')) {
    loadUsers();
  }

  // Bouton déconnexion
  document.querySelectorAll('.logout-btn, [href="login.html"]').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      if (btn.href && btn.href.includes('login.html')) {
        e.preventDefault();
      }
      await fetch('../controller/AuthController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' })
      });
      window.location.href = 'login.html';
    });
  });
});

// Vérifie la session + redirige si besoin
async function checkSession() {
  const res = await fetch('../controller/AuthController.php');
  const data = await res.json();
  if (!data.logged || data.user.role !== 'admin') {
    window.location.href = 'login.html';
    return null;
  }
  return data.user;
}

// Charge la liste des utilisateurs
async function loadUsers() {
  const res = await fetch('../controller/UserController.php');
  const users = await res.json();

  const tbody = document.getElementById('usersTable');
  if (!tbody) return;

  tbody.innerHTML = users.map(u => `
    <tr>
      <td>${u.id}</td>
      <td>${u.nom}</td>
      <td>${u.email}</td>
      <td><span class="badge bg-${u.role === 'admin' ? 'danger' : u.role === 'professeur' ? 'warning' : 'success'}">${capitalize(u.role)}</span></td>
      <td>${formatDate(u.date_creation)}</td>
    </tr>
  `).join('');
}

// Utilitaires
function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('fr-FR');
}