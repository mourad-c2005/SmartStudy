<?php
// user.php - Dans le dossier view/back_office
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/User.php';

// Initialisation
$userModel = new User($pdo);
$users = $userModel->getAllWithAutorisation(); // Utiliser la nouvelle méthode

// Gestion de la recherche
$searchTerm = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    // Recherche les utilisateurs
    $users = $userModel->search($searchTerm);
}

// Fonction pour surligner les termes de recherche
function highlightText($text, $term) {
    if (empty($term)) return htmlspecialchars($text);
    return preg_replace("/($term)/i", '<span class="highlight">$1</span>', htmlspecialchars($text));
}

// Gestion des messages de succès/erreur
$message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created': $message = '<div class="alert success">Utilisateur créé avec succès!</div>'; break;
        case 'updated': $message = '<div class="alert success">Utilisateur modifié avec succès!</div>'; break;
        case 'deleted': $message = '<div class="alert success">Utilisateur supprimé avec succès!</div>'; break;
        case 'autorisation_updated': $message = '<div class="alert success">Autorisation mise à jour avec succès!</div>'; break;
    }
}
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'create_failed': $message = '<div class="alert error">Erreur lors de la création</div>'; break;
        case 'update_failed': $message = '<div class="alert error">Erreur lors de la modification</div>'; break;
        case 'delete_failed': $message = '<div class="alert error">Erreur lors de la suppression</div>'; break;
        case 'autorisation_failed': $message = '<div class="alert error">Erreur lors de la mise à jour de l\'autorisation</div>'; break;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartStudy+ | Gestion des Utilisateurs</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
 

</head>
<body>
  <div class="top-nav">
    <a href="index.php" class="logo">SmartStudy+ Admin</a>
    <div class="user">
      <img src="https://ui-avatars.com/api/?name=Admin&background=4CAF50&color=fff" alt="Admin" id="admin-avatar">
      <div class="user-info">
        <div class="user-name" id="admin-name">Administrateur</div>
        <div class="user-role">Administrateur</div>
      </div>
    </div>
  </div>

  <div class="main">
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>

    <h2>Gestion des Utilisateurs</h2>
    
    <div id="error-container">
      <?php echo $message; ?>
    </div>
    
    <div class="stats-container">
      <div class="stat-card">
        <h5>Utilisateurs</h5>
        <h3 id="total-users"><?php echo count($users); ?></h3>
        <p>Total utilisateurs</p>
      </div>
      <div class="stat-card">
        <h5>Étudiants</h5>
        <h3 id="total-etudiants"><?php echo count(array_filter($users, function($u) { 
            return ($u['role'] ?? $u['user_role'] ?? '') === 'etudiant'; 
        })); ?></h3>
        <p>Inscrits</p>
      </div>
      <div class="stat-card">
        <h5>Professeurs</h5>
        <h3 id="total-professeurs"><?php echo count(array_filter($users, function($u) { 
            return ($u['role'] ?? $u['user_role'] ?? '') === 'professeur'; 
        })); ?></h3>
        <p>Inscrits</p>
      </div>
      <div class="stat-card">
        <h5>Actifs</h5>
        <h3 id="total-actifs"><?php echo count(array_filter($users, function($u) { 
            return ($u['autorisation'] ?? 0) == 1; 
        })); ?></h3>
        <p>Comptes autorisés</p>
      </div>
    </div>
    <div class="clear"></div>

    <!-- Barre de recherche -->
    <div class="search-container">
      <form method="GET" action="" class="search-box">
        <input type="text" 
               name="search" 
               class="search-input" 
               placeholder="Rechercher par nom ou email..."
               value="<?php echo htmlspecialchars($searchTerm); ?>"
               autocomplete="off">
        <i class="fas fa-search search-icon"></i>
      </form>
      
      
    </div>

    <?php if (!empty($searchTerm)): ?>
      <div class="search-results-info">
        <strong>Résultats de recherche pour : "<?php echo htmlspecialchars($searchTerm); ?>"</strong>
        - <?php echo count($users); ?> utilisateur(s) trouvé(s)
      </div>
    <?php endif; ?>

    <div class="card">
      <div style="overflow: hidden; margin-bottom: 16px;">
        <h5 style="float: left; margin: 0;">
          <?php echo !empty($searchTerm) ? 'Résultats de la recherche' : 'Liste des Utilisateurs'; ?>
        </h5>
        <div style="float: right;">
          <button type="button" class="btn-add" onclick="openAddUserModal()">
            <i class="fas fa-plus"></i> Add User
          </button>
          <button class="btn-refresh" onclick="loadUsers()">
            <i class="fas fa-refresh"></i> Actualiser
          </button>
        </div>
      </div>
      <div class="clear"></div>
      
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Date d'inscription</th>
              <th>Autorisation</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="8" class="no-results">
                  <?php if (!empty($searchTerm)): ?>
                    Aucun utilisateur trouvé pour "<?php echo htmlspecialchars($searchTerm); ?>"
                  <?php else: ?>
                    Aucun utilisateur trouvé
                  <?php endif; ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $user): 
                $nom = $user['nom'] ?? $user['name'] ?? '';
                $email = $user['email'] ?? '';
                $role = $user['role'] ?? $user['user_role'] ?? '';
                $date = $user['date_creation'] ?? $user['date_inscription'] ?? $user['created_at'] ?? 'N/A';
                $autorisation = $user['autorisation'] ?? 0;
                $userId = $user['id'] ?? 0;
              ?>
                <tr>
                  <td><?php echo $userId; ?></td>
                  <td><?php echo !empty($searchTerm) ? highlightText($nom, $searchTerm) : htmlspecialchars($nom); ?></td>
                  <td><?php echo !empty($searchTerm) ? highlightText($email, $searchTerm) : htmlspecialchars($email); ?></td>
                  <td><?php echo htmlspecialchars($role); ?></td>
                  <td><?php echo htmlspecialchars($date); ?></td>
                  <td>
                    <div class="autorisation-buttons">
                      <button class="autorisation-btn <?php echo $autorisation == 1 ? 'yes' : ''; ?>" 
                              onclick="toggleAutorisation(<?php echo $userId; ?>, 1)" 
                              <?php echo $autorisation == 1 ? 'disabled' : ''; ?>>
                        Oui
                      </button>
                      <button class="autorisation-btn <?php echo $autorisation == 0 ? 'no' : ''; ?>" 
                              onclick="toggleAutorisation(<?php echo $userId; ?>, 0)" 
                              <?php echo $autorisation == 0 ? 'disabled' : ''; ?>>
                        Non
                      </button>
                    </div>
                  </td>
                  <td>
                    <span class="autorisation-status <?php echo $autorisation == 1 ? 'status-active' : 'status-blocked'; ?>">
                      <?php echo $autorisation == 1 ? 'Actif' : 'Bloqué'; ?>
                    </span>
                  </td>
                  <td>
                    <button class='btn-modifier' onclick='editUser(<?php echo $userId; ?>)'>
                      <i class='fas fa-edit'></i> Modifier 
                    </button>
                    <form action="../../controller/delete_page.php" method="GET" style="display: inline;">
                      <input type="hidden" name="id" value="<?php echo $userId; ?>">
                      <button type="submit" class='btn-supprimer' onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                        <i class='fas fa-trash'></i> Supprimer
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal pour ajouter un utilisateur -->
    <div id="addUserModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New User</h5>
          <button type="button" class="close" onclick="closeAddUserModal()">×</button>
        </div>
        <div class="modal-body">
          <form id="add-user-form" action="../../controller/insert_data.php" method="post">
            <input type="hidden" name="add_user" value="1">
            <input type="hidden" name="js_validation" value="1">
            
            <div class="form-group">
              <label class="form-label">Nom</label>
              <input type="text" class="form-control" id="user-name" name="nom">
              <div class="error-message" id="name-error">Le nom est requis</div>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" id="user-email" name="email">
              <div class="error-message" id="email-error">L'email est requis et doit contenir '@' et se terminer par '.com'</div>
            </div>
            <div class="form-group">
              <label class="form-label">Role</label>
              <select class="form-control" id="user-role" name="role">
                <option value="">Sélectionnez un rôle</option>
                <option value="etudiant">Étudiant</option>
                <option value="professeur">Professeur</option>
                <option value="admin">Administrateur</option>
              </select>
              <div class="error-message" id="role-error">Le rôle est requis</div>
            </div>
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" id="user-password" name="password">
              <div class="error-message" id="password-error">Le mot de passe est requis</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Close</button>
              <button type="button" class="btn btn-success" onclick="validateAddUserForm()">Save User</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal pour modifier l'utilisateur -->
    <div id="editModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modifier l'utilisateur</h5>
          <button type="button" class="close" onclick="closeModal()">×</button>
        </div>
        <form id="edit-user-form" action="../../controller/update.php" method="post">
          <input type="hidden" id="edit-user-id" name="user_id">
          <div class="form-group">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" id="edit-nom" name="nom">
            <small class="form-text text-muted">Laisser vide pour ne pas modifier</small>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" id="edit-email" name="email">
            <div class="error-message" id="edit-email-error">L'email doit contenir '@' et se terminer par '.com'</div>
            <small class="form-text text-muted">Laisser vide pour ne pas modifier</small>
          </div>
          <div class="form-group">
            <label class="form-label">Rôle</label>
            <select class="form-control" id="edit-role" name="role">
              <option value="">Sélectionnez un rôle (optionnel)</option>
              <option value="etudiant">Étudiant</option>
              <option value="professeur">Professeur</option>
              <option value="admin">Administrateur</option>
            </select>
            <small class="form-text text-muted">Laisser vide pour ne pas modifier</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
            <button type="button" class="btn btn-primary" onclick="validateEditUserForm()">Sauvegarder</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>SmartStudy+ © 2025 — Développé par <strong>bluepixel</strong></footer>

  <script>
    // Fonction pour basculer l'autorisation
    function toggleAutorisation(userId, autorisation) {
        if (confirm('Êtes-vous sûr de vouloir ' + (autorisation == 1 ? 'activer' : 'désactiver') + ' cet utilisateur ?')) {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('autorisation', autorisation);
            formData.append('update_autorisation', '1');

            fetch('../../controller/update_autorisation.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'user.php?success=autorisation_updated';
                } else {
                    alert('Erreur: ' + data.message);
                    window.location.href = 'user.php?error=autorisation_failed';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour');
                window.location.href = 'user.php?error=autorisation_failed';
            });
        }
    }

    // Fonctions existantes...
    function openAddUserModal() {
        document.getElementById('addUserModal').style.display = 'block';
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function loadUsers() {
        window.location.reload();
    }

    function validateAddUserForm() {
        document.getElementById('add-user-form').submit();
    }

    function validateEditUserForm() {
        document.getElementById('edit-user-form').submit();
    }

    function editUser(userId) {
        document.getElementById('edit-user-id').value = userId;
        document.getElementById('editModal').style.display = 'block';
    }

    // Recherche en temps réel
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-input');
        const searchForm = document.querySelector('form.search-box');
        
        if (searchInput && searchForm) {
            // Soumettre le formulaire automatiquement après un délai
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.trim().length >= 2 || this.value.trim().length === 0) {
                        searchForm.submit();
                    }
                }, 500);
            });
            
            // Soumettre avec Enter
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.submit();
                }
            });
        }
        
        // Corriger le bouton de recherche pour qu'il soumette le formulaire
        const searchBtn = document.querySelector('.btn-search');
        if (searchBtn && searchForm) {
            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchForm.submit();
            });
        }
    });
  </script>
  
  <script src="../js/validation.js"></script>
</body>
</html>