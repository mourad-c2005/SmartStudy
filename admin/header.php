<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartRevision+ Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS INTÉGRÉ POUR HEADER */
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light: #E8F5E8;
            --white: #ffffff;
            --dark: #2e7d32;
        }
        
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
        
        /* Top Navigation - Style identique à plan.css */
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
            min-height: 70px;
            height: auto;
        }
        
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--green);
            text-decoration: none;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logo i {
            font-size: 1.8rem;
        }
        
        .nav-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-menu a {
            color: #555;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
            border-radius: 30px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            line-height: 1.5;
            white-space: nowrap;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background: var(--light);
            color: var(--green);
            transform: translateY(-1px);
        }
        
        .nav-menu a i {
            font-size: 0.9rem;
        }
        
        /* User Section */
        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 45px;
        }
        
        .user-info {
            text-align: right;
            line-height: 1.2;
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
            font-size: 0.95rem;
        }
        
        .user-info .role {
            font-size: 0.85rem;
            color: #777;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .user-photo {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--green);
            cursor: pointer;
            transition: 0.3s;
            display: block;
        }
        
        .user-photo:hover {
            transform: scale(1.05);
            border-color: var(--dark);
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
            line-height: 1.5;
        }
        
        .logout-btn:hover {
            background: #fdd835;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .logout-btn i {
            font-size: 0.9rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem 5%;
            min-height: calc(100vh - 70px - 60px);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .nav-menu {
                gap: 0.7rem;
            }
            
            .nav-menu a {
                font-size: 0.85rem;
                padding: 0.4rem 0.7rem;
            }
        }
        
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
                gap: 0.5rem;
                width: 100%;
            }
            
            .user-section { 
                flex-direction: row; 
                text-align: center; 
                height: auto;
                gap: 0.8rem;
                margin-top: 0.5rem;
            }
            
            .user-info { 
                text-align: center; 
            }
            
            .main-content { 
                padding: 1.5rem 3%; 
            }
        }
        
        @media (max-width: 768px) {
            .logo {
                font-size: 1.5rem;
            }
            
            .logo span {
                font-size: 0.9rem;
            }
            
            .nav-menu a {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }
            
            .nav-menu a i {
                font-size: 0.8rem;
            }
            
            .user-section {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .user-info .name {
                font-size: 0.9rem;
            }
            
            .logout-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<div class="top-nav">
    <a href="../../smartstudy/view/back_office/index.php" class="logo">
        <i class="fas fa-brain"></i> Smartstudy+admin <span>
    </a>
    
    <div class="nav-menu">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="attempts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'attempts.php' ? 'active' : ''; ?>">
            <i class="fas fa-history"></i> Tentatives
        </a>
        <a href="answers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'answers.php' ? 'active' : ''; ?>">
            <i class="fas fa-key"></i> Réponses
        </a>
        
        <a href="../matiere/index/1 " class="<?php echo basename($_SERVER['REQUEST_URI']) == '/Matiere/index.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i> Matières
        </a>
        <a href="../Chapitre/index/1" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'Chapitre') !== false ? 'active' : ''; ?>">
            <i class="fas fa-folder"></i> Chapitres
        </a>
        <a href="../Cours/index/1" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'Cours') !== false ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i> Cours
        </a>
        
    </div>
    
    <div class="user-section">
        <?php if (isset($_SESSION['admin_name'])): ?>
            <div class="user-info">
                <a href="profile.php" class="profile-link">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                    <div class="role"><i class="fas fa-shield-alt"></i> Administrateur</div>
                </a>
            </div>
            <img src="../assets/default-avatar.png" alt="Photo admin" class="user-photo" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"40\" r=\"20\" fill=\"%234CAF50\"/><circle cx=\"50\" cy=\"100\" r=\"40\" fill=\"%234CAF50\"/></svg>'">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déco
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">