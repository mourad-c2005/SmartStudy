# SmartStudy+
# BLUEPIXEL - Module d'Authentification et Gestion des Utilisateurs

Ce module gère l'inscription, la connexion, les profils utilisateurs, l'upload de photos, les rapports étudiants vers les administrateurs, ainsi que le CRUD des utilisateurs et rapports avec possibilité de blocage de comptes.

## 🚀 Fonctionnalités

### 🔐 Authentification
- **Inscription** avec email, mot de passe et informations personnelles
- **Connexion** sécurisée avec token JWT
- **Récupération de mot de passe** par email
- **Blocage de compte** après plusieurs tentatives échouées

### 👤 Gestion des Profils
- Profil utilisateur complet avec photo
- **Upload de photo de profil** (stockée en backend)
- Édition des informations personnelles
- Changement de mot de passe

### 📊 Rapports Étudiants
- Les étudiants peuvent **soumettre des rapports** aux administrateurs
- Types de rapports : problèmes techniques, questions académiques, signalements
- Suivi de l'état des rapports (en attente, en cours, résolu)

### 🛠️ Back Office Administrateur
- **CRUD complet des utilisateurs** (Create, Read, Update, Delete)
- **CRUD des rapports** avec traitement par les admins
- **Blocage/déblocage des comptes** utilisateurs
- Interface d'administration sécurisée avec rôles

## 🏗️ Architecture Technique

### Backend
- **Framework** : Express.js (Node.js) / Django / Spring Boot (selon ton choix)
- **Base de données** : pdo
- **Authentification** : js 
- **Email** : php mailer

### Frontend
- **Framework** : React / Vue.js / Angular
- **HTTP Client** : Axios / Fetch API
- **Gestion d'état** : Redux / Vuex / Context API
- **UI Components** : Material-UI / Bootstrap / Tailwind CSS

## 📁 Structure des Dossiers
C:.
│   composer.json
│   composer.lock
│
├───config
│       database.php
│       mailer.php
│
├───controller
│       AdminUserController.php
│       AuthController.php
│       CookieController.php
│       delete_page.php
│       insert_data.php
│       process-reset-password.php
│       rapport_action.php
│       send-password-reset.php
│       update.php
│       update_autorisation.php
│       UserController
│       UserController.php
│
├───lib
│       mailer.php
│
├───model
│       profile.php
│       rapport.php
│       user.php
│
├───vendor
│   │   autoload.php
│   │
│   ├───composer
│   │       autoload_classmap.php
│   │       autoload_namespaces.php
│   │       autoload_psr4.php
│   │       autoload_real.php
│   │       autoload_static.php
│   │       ClassLoader.php
│   │       installed.json
│   │       installed.php
│   │       InstalledVersions.php
│   │       LICENSE
│   │       platform_check.php
│   │
│   └───phpmailer
│       └───phpmailer
│           │   COMMITMENT
│           │   composer.json
│           │   get_oauth_token.php
│           │   LICENSE
│           │   README.md
│           │   SECURITY.md
│           │   SMTPUTF8.md
│           │   VERSION
│           │
│           ├───language
│    
│           │
│           └───src
│                   DSNConfigurator.php
│                   Exception.php
│                   OAuth.php
│                   OAuthTokenProvider.php
│                   PHPMailer.php
│                   POP3.php
│                   SMTP.php
│
└───view
    │   check-current-tokens.php
    │   check-password.php
    │   composer.json
    │   composer.lock
    │   create-test-token.php
    │   debug-db.php
    │   debug-token.php
    │   diagnose-token.php
    │   forget-password.php
    │   index.php
    │   inscrire.php
    │   link.php
    │   login.php
    │   mailer.php
    │   profile.php
    │   rapport.php
    │   reset-password.php
    │   reset-password2.css
    │   send-password-reset.php
    │   test-reset-direct.php
    │   test-token-save.php
    │   verifier.php
    │
    ├───back_office
    │   │   index.php
    │   │   rapports.php
    │   │   user.php
    │   │
    │   └───css
    │           index.css
    │           rapport.css
    │           style.css
    │
    ├───css
    │       forget-password.css
    │       inscrire.css
    │       login.css
    │       plan.css
    │       profile.css
    │       rapport.css
    │
    ├───js
    │       validation.js
    │
    ├───pic
    │       .htaccess
    │       profile_23_1765033520.jpg
    │       profile_23_1765033541.jpg
    │       profile_23_1765033846.jpg
    │       profile_23_1765034103.jpg
    │ 
    └───vendor
        │   autoload.php
        │
        ├───composer
        │     
        │
        └───phpmailer
            
              
