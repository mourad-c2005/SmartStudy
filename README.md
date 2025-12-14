# SmartStudy-
# 📚 SmartStudy+ Forum - Documentation

## 🎯 Objectif
Système de forum étudiant avec fonctionnalités avancées de modération et assistant IA.

## 🛠️ Technologies
- *Backend* : PHP 8.0 (Architecture MVC)
- *Frontend* : HTML5, CSS3, JavaScript ES6, Bootstrap 5
- *Base de données* : MySQL 8.0
- *Sécurité* : Validation double (client/serveur), préparation SQL, sessions

## 📁 Architecture MVC

### Model (Modèle)
- Forum.php : Entité forum (getters/setters)
- Reply.php : Entité réponse

### Controller (Contrôleur)
- ForumController.php : Logique métier forums (CRUD, recherche, filtres)
- ReplyController.php : Logique réponses (imbrication, likes, solutions)

### View (Vue)
- *FrontOffice* : Interface utilisateur
- *BackOffice* : Administration, modération, statistiques

## 🔐 Sécurité
1. *XSS* : htmlspecialchars() systématique
2. *SQL Injection* : Requêtes préparées PDO
3. *CSRF* : Tokens de session
4. *Validation* : Double validation (JS + PHP)

## 🌟 Fonctionnalités clés
✅ Forums avec catégories
✅ Réponses imbriquées (parent/child)
✅ Système de likes anti-spam
✅ Modération (épinglage, verrouillage, signalements)
✅ Statistiques avancées
✅ Assistant IA bien-être

## 📊 Base de données
- *forums* : id, title, category, author, content, views, is_pinned, is_locked
- *replies* : id, forum_id, parent_id, author, content, is_solution, likes
- *reports* : id, reply_id, reporter_name, reason, status
