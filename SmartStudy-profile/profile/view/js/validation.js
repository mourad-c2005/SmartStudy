function openAddUserModal() {
    console.log('Opening modal...');
    const modal = document.getElementById('addUserModal');
    modal.style.display = 'block';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
    resetAddUserForm();
}

function loadUsers() {
    location.reload();
}

function editUser(id) {
    document.getElementById('edit-user-id').value = id;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    resetEditUserForm();
}

function deleteUser(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        // CORRIGÉ - URL correcte sans double ?id=
        window.location.href = 'http://localhost/smartstudy/controller/delete_page.php?id=' + id;
    }
}


// Validation functions
function validateEmail(email) {
    return email.includes('@') && email.endsWith('.com');
}

function showError(fieldId, errorId, show) {
    const field = document.getElementById(fieldId);
    const error = document.getElementById(errorId);
    
    if (show) {
        field.classList.add('error');
        error.style.display = 'block';
    } else {
        field.classList.remove('error');
        error.style.display = 'none';
    }
}

function validateAddUserForm() {
    let isValid = true;
    
    // Validate name
    const name = document.getElementById('user-name').value.trim();
    if (name === '') {
        showError('user-name', 'name-error', true);
        isValid = false;
    } else {
        showError('user-name', 'name-error', false);
    }
    
    // Validate email
    const email = document.getElementById('user-email').value.trim();
    if (email === '' || !validateEmail(email)) {
        showError('user-email', 'email-error', true);
        isValid = false;
    } else {
        showError('user-email', 'email-error', false);
    }
    
    // Validate role
    const role = document.getElementById('user-role').value;
    if (role === '') {
        showError('user-role', 'role-error', true);
        isValid = false;
    } else {
        showError('user-role', 'role-error', false);
    }
    
    // Validate password
    const password = document.getElementById('user-password').value;
    if (password === '') {
        showError('user-password', 'password-error', true);
        isValid = false;
    } else {
        showError('user-password', 'password-error', false);
    }
    
    if (isValid) {
        document.getElementById('add-user-form').submit();
    }
}

function validateEditUserForm() {
    let isValid = true;
    
    // Validate email only if provided
    const email = document.getElementById('edit-email').value.trim();
    if (email !== '' && !validateEmail(email)) {
        showError('edit-email', 'edit-email-error', true);
        isValid = false;
    } else {
        showError('edit-email', 'edit-email-error', false);
    }
    
    if (isValid) {
        document.getElementById('edit-user-form').submit();
    }
}

function resetAddUserForm() {
    document.getElementById('user-name').value = '';
    document.getElementById('user-email').value = '';
    document.getElementById('user-role').value = '';
    document.getElementById('user-password').value = '';
    
    // Reset errors
    showError('user-name', 'name-error', false);
    showError('user-email', 'email-error', false);
    showError('user-role', 'role-error', false);
    showError('user-password', 'password-error', false);
}

function resetEditUserForm() {
    document.getElementById('edit-nom').value = '';
    document.getElementById('edit-email').value = '';
    document.getElementById('edit-role').value = '';
    showError('edit-email', 'edit-email-error', false);
}

// Real-time validation for email field
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('user-email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email !== '' && !validateEmail(email)) {
                showError('user-email', 'email-error', true);
            } else {
                showError('user-email', 'email-error', false);
            }
        });
    }
    
    const editEmailField = document.getElementById('edit-email');
    if (editEmailField) {
        editEmailField.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email !== '' && !validateEmail(email)) {
                showError('edit-email', 'edit-email-error', true);
            } else {
                showError('edit-email', 'edit-email-error', false);
            }
        });
    }
});

// Fermer les modals en cliquant en dehors
window.onclick = function(event) {
    const addModal = document.getElementById('addUserModal');
    const editModal = document.getElementById('editModal');
    if (event.target === addModal) closeAddUserModal();
    if (event.target === editModal) closeModal();
}

// Auto-fermer les messages après 5 secondes
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert && alert.style) {
            alert.style.display = 'none';
        }
    });
}, 5000);