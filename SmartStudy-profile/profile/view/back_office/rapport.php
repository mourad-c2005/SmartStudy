<?php
// rapports.php - Dans le dossier view/back_office
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Rapport.php';

// Initialisation
$rapportModel = new Rapport($pdo);

// Gestion des actions
$message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'marked_read': $message = '<div class="alert success">Rapport marqué comme lu!</div>'; break;
        case 'marked_unread': $message = '<div class="alert success">Rapport marqué comme non lu!</div>'; break;
        case 'pinned': $message = '<div class="alert success">Rapport épinglé!</div>'; break;
        case 'unpinned': $message = '<div class="alert success">Rapport désépinglé!</div>'; break;
        case 'deleted': $message = '<div class="alert success">Rapport supprimé!</div>'; break;
    }
}
if (isset($_GET['error'])) {
    $message = '<div class="alert error">Erreur lors de l\'opération</div>';
}

// Récupérer les rapports
$rapports = $rapportModel->all();
$stats = $rapportModel->getStats();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Gestion des Rapports</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <a href="index.php" class="logo">SmartStudy+ Admin</a>
        
        <div class="user-section">
            <div class="user-info">
                <a href="../profile.php" class="profile-link">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['user']['nom']); ?></div>
                </a>
            </div>
            <link rel="stylesheet" type="text/css" href="css/rapport.css">
            <a href="../login.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>

        <h2>Gestion des Rapports</h2>
        
        <div id="message-container">
            <?php echo $message; ?>
        </div>
        
        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card total">
                <h3><?php echo $stats['total']; ?></h3>
                <h5>Total Rapports</h5>
            </div>
            <div class="stat-card unread">
                <h3><?php echo $stats['unread']; ?></h3>
                <h5>Non Lus</h5>
            </div>
            <div class="stat-card pinned">
                <h3><?php echo $stats['pinned']; ?></h3>
                <h5>Épinglés</h5>
            </div>
            <div class="stat-card month">
                <h3><?php echo $stats['this_month']; ?></h3>
                <h5>Ce Mois</h5>
            </div>
        </div>

        <!-- Rapports Table -->
        <div class="card">
            <div style="overflow: hidden; margin-bottom: 16px;">
                <h5 style="float: left; margin: 0;">Liste des Rapports</h5>
                <div style="float: right;">
                    <button class="btn-refresh" onclick="window.location.reload()">
                        <i class="fas fa-refresh"></i> Actualiser
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rapports)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    Aucun rapport trouvé
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rapports as $rapport): ?>
                                <tr>
                                    <td><?php echo $rapport['id']; ?></td>
                                    <td class="email-cell"><?php echo htmlspecialchars($rapport['email']); ?></td>
                                    <td>
                                        <a class="title-link" onclick="openRapportModal(<?php echo $rapport['id']; ?>, '<?php echo htmlspecialchars($rapport['titre']); ?>', '<?php echo htmlspecialchars($rapport['email']); ?>', '<?php echo htmlspecialchars($rapport['message']); ?>', '<?php echo $rapport['created_at']; ?>', <?php echo $rapport['vu']; ?>, <?php echo $rapport['pin']; ?>)">
                                            <?php echo htmlspecialchars($rapport['titre']); ?>
                                        </a>
                                    </td>
                                    <td class="message-preview" title="<?php echo htmlspecialchars($rapport['message']); ?>">
                                        <?php echo htmlspecialchars(substr($rapport['message'], 0, 50)); ?>...
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($rapport['created_at'])); ?></td>
                                    <td>
                                        <?php if ($rapport['pin'] == 1): ?>
                                            <span class="status-badge status-pinned">
                                                <i class="fas fa-thumbtack"></i> Épinglé
                                            </span>
                                        <?php endif; ?>
                                        <span class="status-badge <?php echo $rapport['vu'] == 0 ? 'status-unread' : 'status-read'; ?>">
                                            <?php echo $rapport['vu'] == 0 ? 'Non lu' : 'Lu'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Bouton Lire - TOUJOURS visible -->
                                        <button class="btn-action btn-view" onclick="openRapportModal(<?php echo $rapport['id']; ?>, '<?php echo htmlspecialchars($rapport['titre']); ?>', '<?php echo htmlspecialchars($rapport['email']); ?>', '<?php echo htmlspecialchars($rapport['message']); ?>', '<?php echo $rapport['created_at']; ?>', <?php echo $rapport['vu']; ?>, <?php echo $rapport['pin']; ?>)">
                                            <i class="fas fa-eye"></i> Lire
                                        </button>
                                        
                                        <!-- Boutons Lu/Non lu - changent selon l'état -->
                                        <?php if ($rapport['vu'] == 0): ?>
                                            <button class="btn-action btn-read" onclick="markAsRead(<?php echo $rapport['id']; ?>)">
                                                <i class="fas fa-eye"></i> Lu
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-action btn-unread" onclick="markAsUnread(<?php echo $rapport['id']; ?>)">
                                                <i class="fas fa-eye-slash"></i> Non lu
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Bouton Épingler/Désépingler -->
                                        <button class="btn-action btn-pin" onclick="togglePin(<?php echo $rapport['id']; ?>)">
                                            <i class="fas fa-thumbtack"></i> 
                                            <?php echo $rapport['pin'] == 1 ? 'Désépingler' : 'Épingler'; ?>
                                        </button>
                                        
                                        <!-- Bouton Supprimer -->
                                        <button class="btn-action btn-delete" onclick="deleteRapport(<?php echo $rapport['id']; ?>)">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>SmartStudy+ © 2025 — Développé par <strong>bluepixel</strong></p>
    </footer>

    <!-- Modal pour lire les rapports -->
    <div id="rapportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Détail du Rapport</h3>
                <button class="close-modal" onclick="closeRapportModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="info-group">
                    <div class="info-label">Titre</div>
                    <div class="info-value" id="modalTitre"></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Email de l'expéditeur</div>
                    <div class="info-value" id="modalEmail"></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Date d'envoi</div>
                    <div class="info-value" id="modalDate"></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Message</div>
                    <div class="message-content" id="modalMessage"></div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-action btn-pin" onclick="togglePinModal()">
                    <i class="fas fa-thumbtack"></i> 
                    <span id="pinText">Épingler</span>
                </button>
                <button class="btn-action btn-unread" onclick="markAsUnreadModal()" id="unreadBtn">
                    <i class="fas fa-eye-slash"></i> Marquer non lu
                </button>
                <button class="btn-action btn-delete" onclick="deleteRapportModal()">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
                <button class="btn-action" style="background: #666; color: white;" onclick="closeRapportModal()">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentRapportId = null;
        let currentRapportPin = 0;
        let currentRapportVu = 0;

        function openRapportModal(id, titre, email, message, date, vu, pin) {
            currentRapportId = id;
            currentRapportPin = pin;
            currentRapportVu = vu;
            
            // Mettre à jour le contenu du modal
            document.getElementById('modalTitle').textContent = titre;
            document.getElementById('modalTitre').textContent = titre;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalMessage').textContent = message;
            
            // Formater la date
            const dateObj = new Date(date);
            const formattedDate = dateObj.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('modalDate').textContent = formattedDate;
            
            // Mettre à jour les boutons
            document.getElementById('pinText').textContent = pin == 1 ? 'Désépingler' : 'Épingler';
            document.getElementById('unreadBtn').style.display = vu == 1 ? 'inline-block' : 'none';
            
            // Marquer comme lu si ce n'est pas déjà fait
            if (vu == 0) {
                markAsRead(id);
            }
            
            // Afficher le modal
            document.getElementById('rapportModal').style.display = 'flex';
        }

        function closeRapportModal() {
            document.getElementById('rapportModal').style.display = 'none';
            currentRapportId = null;
        }

        function togglePinModal() {
            if (currentRapportId) {
                togglePin(currentRapportId);
            }
        }

        function markAsUnreadModal() {
            if (currentRapportId) {
                if (confirm('Marquer ce rapport comme non lu ?')) {
                    markAsUnread(currentRapportId);
                }
            }
        }

        function deleteRapportModal() {
            if (currentRapportId) {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?')) {
                    deleteRapport(currentRapportId);
                }
            }
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('rapportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRapportModal();
            }
        });

        // Fonctions pour les actions
        function markAsRead(rapportId) {
            window.location.href = `../../controller/rapport_action.php?action=read&id=${rapportId}`;
        }

        function markAsUnread(rapportId) {
            window.location.href = `../../controller/rapport_action.php?action=unread&id=${rapportId}`;
        }

        function togglePin(rapportId) {
            window.location.href = `../../controller/rapport_action.php?action=pin&id=${rapportId}`;
        }

        function deleteRapport(rapportId) {
            window.location.href = `../../controller/rapport_action.php?action=delete&id=${rapportId}`;
        }
    </script>
</body>
</html>
