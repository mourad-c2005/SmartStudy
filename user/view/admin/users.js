// users.js
var currentUser = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Page users.html chargée');
    checkAdminAuth();
});

function showError(message) {
    var errorContainer = document.getElementById('error-container');
    errorContainer.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-triangle"></i> ' + message + '</div>';
}

function hideError() {
    var errorContainer = document.getElementById('error-container');
    errorContainer.innerHTML = '';
}

function checkAdminAuth() {
    console.log('Vérification authentification admin...');
    var xhr = new XMLHttpRequest();
    
    var path = '/smartstudy/controller/AuthController.php';
    xhr.open('GET', path, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('URL appelée:', path);
            console.log('Statut HTTP:', xhr.status);
            console.log('Réponse:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    console.log('Données auth:', data);
                    
                    if (!data.logged || data.user.role !== 'admin') {
                        showError('Accès non autorisé. Redirection...');
                        setTimeout(function() {
                            window.location.href = '/smartstudy/view/login.html';
                        }, 2000);
                        return;
                    }

                    currentUser = data.user;
                    document.getElementById('admin-name').textContent = currentUser.nom;
                    
                    var avatar = document.getElementById('admin-avatar');
                    avatar.src = 'https://ui-avatars.com/api/?name=' + 
                                encodeURIComponent(currentUser.nom) + 
                                '&background=4CAF50&color=fff';
                    
                    loadUsers();
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    showError('Erreur d\'authentification: ' + e.message);
                }
            } else {
                showError('Erreur serveur (' + xhr.status + ') - URL: ' + path);
            }
        }
    };
    
    xhr.onerror = function() {
        showError('Erreur réseau - Impossible de joindre le serveur');
    };
    
    xhr.send();
}

function loadUsers() {
    console.log('Chargement des utilisateurs...');
    var tbody = document.getElementById('users-table-body');
    tbody.innerHTML = '<tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</td></tr>';
    hideError();

    var xhr = new XMLHttpRequest();
    var path = '/smartstudy/controller/AdminUserController.php';
    xhr.open('GET', path, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('URL users appelée:', path);
            console.log('Statut users:', xhr.status);
            console.log('Réponse users:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    
                    if (result.success) {
                        displayUsers(result.users);
                        updateStats(result.users);
                        hideError();
                    } else {
                        throw new Error(result.message || 'Erreur inconnue');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showError('Erreur: ' + error.message);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur: ' + error.message + '</td></tr>';
                }
            } else {
                showError('Erreur HTTP ' + xhr.status + ' - URL: ' + path);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement (HTTP ' + xhr.status + ')</td></tr>';
            }
        }
    };
    xhr.onerror = function() {
        showError('Erreur réseau - Impossible de charger les utilisateurs');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur réseau</td></tr>';
    };
    xhr.send();
}

function displayUsers(users) {
    var tbody = document.getElementById('users-table-body');
    
    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Aucun utilisateur trouvé</td></tr>';
        return;
    }

    var html = '';
    for (var i = 0; i < users.length; i++) {
        var user = users[i];
        var roleClass = getRoleClass(user.role);
        var roleLabel = getRoleLabel(user.role);
        var tempsSurSite = calculateTempsSurSite(user.date_creation);
        
        html += '<tr>' +
            '<td>' + user.id + '</td>' +
            '<td>' + escapeHtml(user.nom) + '</td>' +
            '<td>' + escapeHtml(user.email) + '</td>' +
            '<td><span class="role-badge ' + roleClass + '">' + roleLabel + '</span></td>' +
            '<td>' + (user.date_creation || 'N/A') + '</td>' +
            '<td>' + tempsSurSite + '</td>' +
            '<td>' +
            '<button class="btn-modifier" onclick="openEditModal(' + user.id + ', \'' + escapeHtml(user.nom) + '\', \'' + escapeHtml(user.email) + '\', \'' + user.role + '\')">' +
            '<i class="fas fa-edit"></i> Modifier' +
            '</button>' +
            '<button class="btn-supprimer" onclick="deleteUser(' + user.id + ', \'' + escapeHtml(user.nom) + '\')">' +
            '<i class="fas fa-trash"></i> Supprimer' +
            '</button>' +
            '</td>' +
            '</tr>';
    }
    
    tbody.innerHTML = html;
}

function updateStats(users) {
    var totalUsers = users.length;
    var totalEtudiants = 0;
    var totalProfesseurs = 0;
    
    for (var i = 0; i < users.length; i++) {
        if (users[i].role === 'etudiant') {
            totalEtudiants++;
        } else if (users[i].role === 'professeur') {
            totalProfesseurs++;
        }
    }
    
    document.getElementById('total-users').textContent = totalUsers;
    document.getElementById('total-etudiants').textContent = totalEtudiants;
    document.getElementById('total-professeurs').textContent = totalProfesseurs;
}

function openEditModal(id, nom, email, role) {
    document.getElementById('edit-user-id').value = id;
    document.getElementById('edit-nom').value = nom;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function updateUser() {
    var id = document.getElementById('edit-user-id').value;
    var nom = document.getElementById('edit-nom').value.trim();
    var email = document.getElementById('edit-email').value.trim();
    var role = document.getElementById('edit-role').value;

    if (!nom || !email || !role) {
        alert('Tous les champs sont requis');
        return;
    }

    var params = 'id=' + encodeURIComponent(id) + 
                 '&nom=' + encodeURIComponent(nom) + 
                 '&email=' + encodeURIComponent(email) + 
                 '&role=' + encodeURIComponent(role);

    var xhr = new XMLHttpRequest();
    xhr.open('PUT', '/smartstudy/controller/AdminUserController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            try {
                var result = JSON.parse(xhr.responseText);
                if (result.success) {
                    alert('Utilisateur modifié avec succès');
                    closeModal();
                    loadUsers();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (e) {
                alert('Erreur lors de la modification');
            }
        }
    };
    xhr.send(params);
}

function deleteUser(id, nom) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur "' + nom + '" ?')) {
        return;
    }

    var params = 'id=' + encodeURIComponent(id);
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', '/smartstudy/controller/AdminUserController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            try {
                var result = JSON.parse(xhr.responseText);
                if (result.success) {
                    alert('Utilisateur supprimé avec succès');
                    loadUsers();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (e) {
                alert('Erreur lors de la suppression');
            }
        }
    };
    xhr.send(params);
}

function getRoleClass(role) {
    if (role === 'admin') return 'role-admin';
    if (role === 'etudiant') return 'role-etudiant';
    if (role === 'professeur') return 'role-professeur';
    return 'role-etudiant';
}

function getRoleLabel(role) {
    if (role === 'admin') return 'Admin';
    if (role === 'etudiant') return 'Étudiant';
    if (role === 'professeur') return 'Professeur';
    return role;
}

function calculateTempsSurSite(dateCreation) {
    if (!dateCreation) return 'N/A';
    var dateInscription = new Date(dateCreation);
    var maintenant = new Date();
    var diffTemps = maintenant - dateInscription;
    var jours = Math.floor(diffTemps / (1000 * 60 * 60 * 24));
    if (jours === 0) return 'Aujourd\'hui';
    if (jours === 1) return '1 jour';
    if (jours < 30) return jours + ' jours';
    var mois = Math.floor(jours / 30);
    if (mois === 1) return '1 mois';
    if (mois < 12) return mois + ' mois';
    var annees = Math.floor(mois / 12);
    return annees === 1 ? '1 an' : annees + ' ans';
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('click', function(event) {
    var modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeModal();
    }
});