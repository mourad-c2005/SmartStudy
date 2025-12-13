<?php
// En haut de smartstudy/View/FrontOffice/forums.php
session_start();

// ✅ Vérifier connexion
if (!isset($_SESSION['user'])) {
    header('Location: ../../../view/login.php'); // Chemin vers le login du système utilisateur
    exit();
}

$currentUser = $_SESSION['user']['nom'];
$isAdminUser = ($_SESSION['user']['role'] === 'admin');
require_once('../../Controller/ForumController.php');

$forumC = new ForumController();

// Gestion de la recherche et du filtre
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($search)) {
    $list = $forumC->searchForums($search);
} elseif (!empty($category)) {
    $list = $forumC->filterByCategory($category);
} else {
    $list = $forumC->listForums();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Forum</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        /* VARIABLES CSS */
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light: #E8F5E8;
            --white: #ffffff;
            --dark: #2e7d32;
            --blue: #2196F3;
            --orange: #FF9800;
        }
        
        /* RESET ET BASE */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--light);
            color: #333;
            margin: 0;
            padding: 0;
            padding-bottom: 60px;
        }
        
        /* TOP NAVIGATION - Identique à votre exemple */
        .top-nav {
            background: var(--white);
            padding: 1rem 5%;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 120px;
        }
        
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--green);
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            gap: 1.2rem;
        }
        
        .nav-menu a {
            color: #555;
            text-decoration: none;
            font-weight: 600;
            padding: 0.4rem 0.9rem;
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background: var(--light);
            color: var(--green);
        }
        
        /* User Section - Identique à votre exemple */
        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info .profile-link {
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
            padding: 0.5rem;
            border-radius: 8px;
            display: block;
        }
        
        .user-info .profile-link:hover {
            background: var(--light);
            transform: translateY(-1px);
        }
        
        .user-info .name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.2rem;
        }
        
        .user-info .role {
            font-size: 0.85rem;
            color: #777;
        }
        
        .user-photo {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--green);
            cursor: pointer;
            transition: 0.3s;
        }
        
        .user-photo:hover {
            transform: scale(1.05);
            border-color: var(--dark);
        }
        
        .admin-btn {
            background: var(--dark);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-btn:hover {
            background: #1b5e20;
            transform: translateY(-1px);
        }
        
        .logout-btn {
            background: var(--yellow);
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logout-btn:hover {
            background: #fdd835;
            transform: translateY(-1px);
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem 5%;
            min-height: calc(100vh - 70px - 60px);
        }
        
        .page-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }
        
        /* Forum Actions */
        .forum-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .search-box {
            flex: 1;
            min-width: 220px;
            padding: 0.8rem 1rem;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-box:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .category-select {
            padding: 0.8rem 1rem;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            background: var(--white);
            width: 220px;
            cursor: pointer;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        
        .btn-search {
            background: var(--green);
            color: white;
        }
        
        .btn-search:hover {
            background: var(--dark);
            transform: translateY(-2px);
        }
        
        .btn-reset {
            background: #f5f5f5;
            color: #666;
            border: 2px solid #ddd;
        }
        
        .btn-reset:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        .btn-new-topic {
            background: var(--green);
            color: white;
            font-weight: 600;
        }
        
        .btn-new-topic:hover {
            background: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Topics List */
        .topics-list {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }
        
        .topic-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            border-left: 4px solid var(--green);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            transition: all 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .topic-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .topic-card.pinned {
            border-left-color: #ffd700;
            background: #fffef5;
        }
        
        .topic-card.locked {
            opacity: 0.8;
            background: #f9f9f9;
        }
        
        .topic-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .topic-title:hover {
            color: var(--green);
        }
        
        .topic-excerpt {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .topic-meta {
            font-size: 0.85rem;
            color: #777;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        
        .badge-category {
            background: #f0f7f0;
            color: var(--green);
        }
        
        .badge-pinned {
            background: #ffd700;
            color: #333;
        }
        
        .badge-locked {
            background: #ffcccb;
            color: #c00;
        }
        
        .topic-stats {
            text-align: right;
            min-width: 120px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #888;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        /* Footer */
        footer {
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
            position: relative;
        }
        
        footer strong {
            color: var(--yellow);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .top-nav {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
                min-height: auto;
            }
            
            .nav-menu {
                order: 3;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .user-section {
                flex-direction: column;
                text-align: center;
                gap: 0.8rem;
            }
            
            .user-info {
                text-align: center;
            }
            
            .main-content {
                padding: 1.5rem;
            }
            
            .forum-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box, .category-select {
                width: 100%;
            }
            
            .topic-card {
                flex-direction: column;
            }
            
            .topic-stats {
                text-align: left;
                margin-top: 1rem;
                min-width: auto;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation - Identique à votre exemple -->
<nav class="top-nav">
    <a href="../index.php" class="logo">SmartStudy+</a>
    <div class="nav-menu">
        <a href="../index.php">Accueil</a>
        <a href="../../index.php">Mes Plans</a>
        <a href="forums.php" class="active">Forums</a>
        <a href="../groupes.php">Groupes</a>
        <a href="../progres.php">Progrès</a>
        <a href="chatbot.php">Assistant</a>
    </div>
    <div class="user-section">
        <div class="user-info">
            <a href="../profile.php" class="profile-link">
                <div class="name"><?php echo htmlspecialchars($_SESSION['user']['nom']); ?></div>
                <div class="role"><?php echo ucfirst($_SESSION['user']['role']); ?></div>
            </a>
        </div>
        
        
        <a href="../login.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1>
            <i class="fas fa-comments"></i>
            Forum & Discussions
        </h1>
        <p>Échangez, partagez et apprenez avec la communauté</p>
    </div>

    <!-- Search and Filter -->
    <div class="forum-actions">
        <form method="GET" action="forums.php" style="display: contents;">
            <input type="text" 
                   name="search" 
                   class="search-box" 
                   placeholder="Rechercher un sujet, mot-clé..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="category" class="category-select" onchange="this.form.submit()">
                <option value="">Toutes catégories</option>
                <option value="Planning" <?php echo $category === 'Planning' ? 'selected' : ''; ?>>Planning</option>
                <option value="Méthodes" <?php echo $category === 'Méthodes' ? 'selected' : ''; ?>>Méthodes</option>
                <option value="Technique" <?php echo $category === 'Technique' ? 'selected' : ''; ?>>Technique</option>
                <option value="Général" <?php echo $category === 'Général' ? 'selected' : ''; ?>>Général</option>
            </select>
            
            <button type="submit" class="btn btn-search">
                <i class="fas fa-search"></i> Rechercher
            </button>
            
            <?php if (!empty($search) || !empty($category)): ?>
                <a href="forums.php" class="btn btn-reset">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            <?php endif; ?>
        </form>
        
        <a href="../BackOffice/addForum.php" class="btn btn-new-topic">
            <i class="fas fa-plus"></i> Nouveau sujet
        </a>
    </div>

    <!-- Topics List -->
    <div class="topics-list">
        <?php if (!empty($list)): ?>
            <?php foreach($list as $forum): 
                $repliesCount = $forumC->countReplies($forum['id']);
                $lastReply = $forumC->getLastReplyDate($forum['id']);
                $lastDate = $lastReply ? date('d/m/Y', strtotime($lastReply)) : date('d/m/Y', strtotime($forum['created_at']));
                $excerpt = strlen($forum['content']) > 120 ? substr($forum['content'], 0, 120) . '...' : $forum['content'];
                
                $cardClass = 'topic-card';
                if ($forum['is_pinned']) $cardClass .= ' pinned';
                if ($forum['is_locked']) $cardClass .= ' locked';
            ?>
                <div class="<?php echo $cardClass; ?>">
                    <div style="flex: 1;">
                        <a href="thread.php?id=<?php echo $forum['id']; ?>" class="topic-title">
                            <?php echo htmlspecialchars($forum['title']); ?>
                        </a>
                        
                        <?php if ($forum['is_pinned']): ?>
                            <span class="badge badge-pinned">
                                <i class="fas fa-thumbtack"></i> Épinglé
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($forum['is_locked']): ?>
                            <span class="badge badge-locked">
                                <i class="fas fa-lock"></i> Verrouillé
                            </span>
                        <?php endif; ?>
                        
                        <span class="badge badge-category">
                            <?php echo htmlspecialchars($forum['category']); ?>
                        </span>
                        
                        <p class="topic-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                        
                        <div class="topic-meta">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($forum['author']); ?> • 
                            <i class="fas fa-eye"></i> <?php echo $forum['views']; ?> vues • 
                            <i class="far fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($forum['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="topic-stats">
                        <div class="stat-item">
                            <i class="fas fa-comments"></i>
                            <span><?php echo $repliesCount; ?> réponses</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span>Dernier: <?php echo $lastDate; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucun sujet trouvé</h3>
                <p>
                    <?php if (!empty($search) || !empty($category)): ?>
                        Aucun résultat pour votre recherche.
                        <br>
                        <a href="forums.php" style="color: var(--green); font-weight: 600;">Voir tous les sujets</a>
                    <?php else: ?>
                        Le forum est vide pour le moment. Soyez le premier à créer un sujet !
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>SmartStudy+ © 2025 – Nature • Croissance • Sérénité</p>
    <p>Développé par <strong>BLUEPIXEL 2032</strong></p>
</footer>

</body>
</html>