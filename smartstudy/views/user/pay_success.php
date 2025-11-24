<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Paiement réussi | SmartStudy+</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --green: #4CAF50;
      --yellow: #FFEB3B;
      --light: #E8F5E8;
      --white: #ffffff;
      --dark: #2e7d32;
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Open Sans', sans-serif;
      background: var(--light);
      color: #333;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .success-card {
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      padding: 4rem;
      text-align: center;
      max-width: 600px;
      margin: 2rem;
    }

    .success-icon {
      font-size: 5rem;
      color: var(--green);
      margin-bottom: 1.5rem;
      animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
      from {
        transform: scale(0);
      }
      to {
        transform: scale(1);
      }
    }

    .success-card h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .success-card p {
      font-size: 1.2rem;
      color: #555;
      margin-bottom: 2rem;
    }

    .btn-home {
      background: var(--green);
      color: white;
      padding: 1rem 2.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      display: inline-block;
      transition: 0.3s;
    }

    .btn-home:hover {
      background: var(--dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    .btn-formations {
      background: var(--yellow);
      color: #333;
      padding: 1rem 2.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      display: inline-block;
      transition: 0.3s;
      margin-left: 1rem;
    }

    .btn-formations:hover {
      background: #fdd835;
      transform: translateY(-2px);
    }

    @media (max-width: 576px) {
      .success-card {
        padding: 2rem;
      }
      .success-card h1 {
        font-size: 2rem;
      }
      .btn-formations {
        margin-left: 0;
        margin-top: 1rem;
        display: block;
      }
    }
  </style>
</head>
<body>
  <div class="success-card">
    <div class="success-icon">
      <i class="fas fa-check-circle"></i>
    </div>
    <h1>Paiement réussi ! 🎉</h1>
    <p>Merci pour votre achat. Votre paiement a été effectué avec succès.</p>
    <p style="font-size: 1rem; color: #777;">Vous pouvez maintenant accéder à vos formations.</p>
    <div style="margin-top: 2rem;">
      <a href="index.php?controller=user&action=home" class="btn-home">
        <i class="fas fa-home"></i> Retour à l'accueil
      </a>
      <a href="index.php?controller=user&action=formations" class="btn-formations">
        <i class="fas fa-book"></i> Voir les formations
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

