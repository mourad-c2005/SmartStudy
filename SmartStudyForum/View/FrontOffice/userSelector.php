<?php
require_once('../../sessionHelper.php');

// Gérer le changement d'utilisateur
if (isset($_POST['change_user'])) {
    setCurrentUser($_POST['username']);
    setAdmin(isset($_POST['is_admin']));
    header('Location: ' . $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit();
}
?>

<!-- Widget de sélection utilisateur -->
<div class="user-selector" style="position:fixed;top:10px;right:10px;background:#fff;padding:10px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;min-width:250px">
    <form method="POST" class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(getCurrentUser()); ?>&background=4CAF50&color=fff&size=40" 
                 class="rounded-circle" alt="Avatar">
            <div class="flex-grow-1">
                <input type="text" name="username" class="form-control form-control-sm" 
                       value="<?php echo htmlspecialchars(getCurrentUser()); ?>" 
                       placeholder="Votre nom">
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_admin" id="adminCheck" 
                   <?php echo isAdmin() ? 'checked' : ''; ?>>
            <label class="form-check-label small" for="adminCheck">
                Mode Admin 👑
            </label>
        </div>
        <button type="submit" name="change_user" class="btn btn-success btn-sm">
            <i class="fas fa-save"></i> Changer
        </button>
    </form>
</div>