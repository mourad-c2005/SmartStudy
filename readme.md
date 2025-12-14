📘 SmartStudy+
BLUEPIXEL – Module Gestion des Cours, Matières, Chapitres & Quiz avec Chatbot IA

Ce module permet la gestion complète du contenu pédagogique (matières, chapitres, cours), l’intégration de vidéos explicatives, des quiz interactifs, ainsi qu’un chatbot intelligent capable de fournir des résumés de cours et d’assister les étudiants.
Un dashboard administrateur avancé permet le suivi des performances des étudiants (notes, temps, tentatives).

🚀 Fonctionnalités
📚 Gestion Pédagogique

Gestion des matières

Organisation en chapitres

Création de cours détaillés

Chaque cours contient :

📄 Contenu textuel

🎥 Vidéo explicative intégrée

🤖 Résumé automatique via chatbot IA

📝 Quiz associé

🧠 Chatbot Intelligent

Résumé automatique des cours

Explication simplifiée des chapitres

Réponses aux questions des étudiants

Assistance pédagogique personnalisée

Support bien-être académique

📝 Quiz & Évaluations

Quiz interactifs par cours

Bouton « Lancer le Quiz »

Chronomètre automatique

Calcul du score en temps réel

Soumission et enregistrement des résultats

Anti-triche basique (temps limité, tentative unique)

👨‍🎓 Espace Étudiant

Consultation des matières et chapitres

Accès aux cours avec vidéos

Lecture des résumés générés par l’IA

Participation aux quiz

Historique personnel :

Scores

Temps passé

Progression globale

🛠️ Back Office Administrateur

CRUD Matières

CRUD Chapitres

CRUD Cours

CRUD Quiz & Questions

Ajout / modification / suppression :

Cours

Vidéos

Quiz

Gestion des étudiants

Modération du contenu pédagogique

📊 Dashboard Administrateur

Liste des étudiants ayant passé les quiz

Statistiques détaillées :

Notes obtenues

Temps passé par quiz

Nombre de tentatives

Classement des étudiants

Taux de réussite par cours / chapitre / matière

Graphiques de progression

🛠️ Technologies Utilisées

Backend : PHP 8.0 (Architecture MVC)

Frontend : HTML5, CSS3, JavaScript ES6, Bootstrap 5

Base de données : MySQL 8.0

IA : Chatbot pour résumé et assistance pédagogique

Sécurité :

Validation double (JS + PHP)

PDO & requêtes préparées

Sessions sécurisées

Tokens CSRF

📁 Architecture MVC
🧩 Model

Matiere.php

Chapitre.php

Cours.php

Quiz.php

Question.php

Resultat.php

User.php

🎮 Controller

MatiereController.php

ChapitreController.php

CoursController.php

QuizController.php

ChatbotController.php

DashboardController.php

🎨 View

FrontOffice Étudiant

Matières & cours

Vidéos & quiz

Chatbot IA

BackOffice Admin

Gestion du contenu

Dashboard & statistiques

🔐 Sécurité

Protection XSS : htmlspecialchars()

Protection SQL Injection : PDO préparé

CSRF : Tokens de session

Contrôle d’accès par rôles (Admin / Étudiant)

Historique des actions administrateur

🌟 Fonctionnalités Clés

✅ Organisation hiérarchique (Matière → Chapitre → Cours)
✅ Vidéos pédagogiques intégrées
✅ Quiz chronométrés
✅ Chatbot IA pour résumé et explication
✅ Dashboard avancé
✅ Suivi des performances étudiants
✅ Administration complète du contenu

📊 Base de Données (Exemple)

matieres : id, nom, description

chapitres : id, matiere_id, titre

cours : id, chapitre_id, titre, contenu, video_url

quiz : id, cours_id, titre, duree

questions : id, quiz_id, question, options, correct_answer

resultats : id, user_id, quiz_id, score, temps

users : id, nom, email, role, status
