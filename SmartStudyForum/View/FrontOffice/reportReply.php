<?php
require_once('../../sessionHelper.php');
require_once('../../config.php');

$currentUser = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_id = intval($_POST['reply_id']);
    $forum_id = intval($_POST['forum_id']);
    $reason = trim($_POST['reason']);
    
    if (!empty($reason)) {
        $db = ConfigForum::getConnexion();
        $sql = "INSERT INTO reports (reply_id, reporter_name, reason) VALUES (:reply_id, :reporter_name, :reason)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'reply_id' => $reply_id,
            'reporter_name' => $currentUser,
            'reason' => $reason
        ]);
        
        header("Location: thread.php?id=$forum_id&report=success");
        exit();
    }
}

$reply_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$forum_id = isset($_GET['forum_id']) ? intval($_GET['forum_id']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler une réponse | SmartStudy+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
    </style>
</head>
<body>
    <?php include('userSelector.php'); ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-flag"></i> Signaler un contenu inapproprié</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-user"></i> Signalé par : <strong><?php echo htmlspecialchars($currentUser); ?></strong>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="reply_id" value="<?php echo $reply_id; ?>">
                            <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>">

                            <div class="mb-3">
                                <label for="reason" class="form-label">Raison du signalement <span class="text-danger">*</span></label>
                                <select class="form-select mb-3" id="reason_type">
                                    <option value="">-- Choisir une raison --</option>
                                    <option value="Spam ou publicité">Spam ou publicité</option>
                                    <option value="Contenu offensant">Contenu offensant</option>
                                    <option value="Harcèlement">Harcèlement</option>
                                    <option value="Fausses informations">Fausses informations</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                <textarea class="form-control" id="reason" name="reason" rows="5" 
                                          placeholder="Décrivez pourquoi ce contenu est inapproprié..." required></textarea>
                            </div>

                            <div class="text-end">
                                <a href="thread.php?id=<?php echo $forum_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-flag"></i> Signaler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('reason_type').addEventListener('change', function() {
            const textarea = document.getElementById('reason');
            if (this.value) {
                textarea.value = this.value + ': ';
                textarea.focus();
            }
        });
    </script>
</body>
</html>