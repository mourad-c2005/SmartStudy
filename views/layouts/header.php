<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>SmartStudy+</title>

    <!-- Fonts + Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS perso (card-planning, planning-table, badges, etc.) -->
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light-green: #E8F5E9;
            --dark: #333333;
            --white: #ffffff;
            --blue: #2563eb;
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--light-green);
            margin: 0;
            padding: 0;
        }

        /* ===== NAVBAR ===== */
        .navbar-wrapper {
            padding: 1.5rem 8%;
        }

        .navbar-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 0.9rem 2.5rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--green);
        }

        .navbar-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .navbar-links a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            transition: 0.2s ease;
        }

        .navbar-links a:hover {
            background: rgba(76, 175, 80, 0.08);
            color: var(--green);
        }

        .navbar-links .active {
            background: rgba(76, 175, 80, 0.14);
            color: var(--green);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .navbar-user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .navbar-user-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .navbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: var(--green);
        }

        /* ===== CONTENU ===== */
        .main-content {
            padding: 2.5rem 8%;
            min-height: calc(100vh - 100px);
        }

        .section-title {
            text-align: center;
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--green);
            margin-bottom: 1.4rem;
            letter-spacing: 0.04em;
        }

        .section-subtitle {
            text-align: center;
            color: #6b7280;
            margin-top: -0.8rem;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* Cartes / planning déjà existants chez toi */
        .card-soft {
            background: #ffffff;
            border-radius: 20px;
            padding: 1.8rem;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .planning-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .planning-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 1.4rem 1.3rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .planning-card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
            color: #111827;
        }

        .planning-card-icon {
            font-size: 2rem;
            margin-right: 0.6rem;
            color: var(--green);
        }

        .planning-meta {
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 0.2rem;
        }

        .badge-level {
            display: inline-block;
            padding: 0.18rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            margin-left: 0.4rem;
        }

        .badge-diff {
            display: inline-block;
            padding: 0.18rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            background: rgba(34, 197, 94, 0.08);
            color: #15803d;
            margin-left: 0.4rem;
        }

        .planning-footer {
            margin-top: 0.8rem;
            display: flex;
            justify-content: flex-start;
        }

        /* Formulaires */
        .page-title {
            text-align: center;
            margin: 1.5rem 0 1.2rem;
            font-size: 2rem;
            font-weight: 700;
            color: var(--green);
            letter-spacing: 0.04em;
        }

        .form-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 800px;
            margin: 0 auto 3rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.15);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: .3rem;
        }

        .form-control {
            border-radius: 14px;
            border: 1px solid #CBD5F5;
            padding: 0.7rem 1rem;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 0.6rem 1.6rem;
            border-radius: 999px;
            border: none;
            font-size: 0.95rem;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--green));
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.45);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-danger {
            background: #ef4444;
            color: #ffffff;
        }

        .btn-green {
            background: var(--green);
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.35);
        }

        .btn-green:hover {
            background: #16a34a;
        }

        /* Alertes */
        .alert-danger {
            max-width: 800px;
            margin: 0 auto 1.5rem;
            border-radius: 16px;
            border: 1px solid rgba(239, 68, 68, 0.35);
            background: #fef2f2;
            color: #991b1b;
            padding: 0.9rem 1.2rem;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 1.2rem;
        }
    </style>
</head>

<body>

<div class="navbar-wrapper">
    <div class="navbar-card">
        <div class="navbar-logo">SmartStudy+</div>

        <div class="navbar-links">
            <a href="index.php?controller=home&action=index"
               class="<?= (($_GET['controller'] ?? '') === 'home') ? 'active' : '' ?>">
                Accueil
            </a>
            <a href="index.php?controller=planning&action=index"
               class="<?= (($_GET['controller'] ?? '') === 'planning') ? 'active' : '' ?>">
                Planning
            </a>
            <a href="#">Groupes</a>
            <a href="#">Progrès</a>
        </div>

        <div class="navbar-user">
            <div>
                <div class="navbar-user-name">Yahya Chebbi</div>
                <div class="navbar-user-role">Étudiant</div>
            </div>
            <div class="navbar-avatar"></div>
        </div>
    </div>
</div>

<div class="main-content">
