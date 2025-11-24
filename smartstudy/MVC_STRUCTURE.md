# Structure MVC - SmartStudy+

## Architecture MVC

Le projet SmartStudy+ utilise maintenant une architecture MVC (Model-View-Controller) complète.

## Structure des dossiers

```
smartstudy/
├── config/              # Configuration
│   ├── config.php       # Configuration générale
│   └── Database.php     # Connexion à la base de données
│
├── controllers/         # Contrôleurs (logique métier)
│   ├── FrontController.php          # Routeur principal
│   ├── AuthController.php           # Authentification (login, signup, logout)
│   ├── UserController.php          # Pages publiques (accueil, formations)
│   ├── AdminController.php         # Dashboard admin
│   ├── AdminSectionController.php  # CRUD Sections
│   ├── AdminCategoryController.php # CRUD Catégories
│   ├── AdminFormationController.php # CRUD Formations
│   └── PanierController.php       # Gestion du panier
│
├── models/             # Modèles (accès aux données)
│   ├── Section.php
│   ├── Category.php
│   ├── formation.php
│   └── Panier.php
│
├── views/              # Vues (présentation)
│   ├── auth/           # Vues d'authentification
│   │   ├── login.php
│   │   └── signup.php
│   ├── user/           # Vues publiques
│   │   ├── home.php
│   │   ├── formations.php
│   │   ├── formation_detail.php
│   │   └── panier.php
│   └── admin/          # Vues admin
│       ├── dashboard.php
│       ├── sections/
│       ├── categories/
│       └── formations/
│
└── index.php           # Point d'entrée unique (Front Controller)

```

## Système de routage

Toutes les requêtes passent par `index.php` qui utilise le format suivant :

```
index.php?controller=CONTROLLER&action=ACTION&parametres...
```

### Routes disponibles

#### Authentification
- `index.php?controller=auth&action=login` - Page de connexion
- `index.php?controller=auth&action=signup` - Page d'inscription
- `index.php?controller=auth&action=logout` - Déconnexion

#### Pages publiques (User)
- `index.php?controller=user&action=home` - Page d'accueil
- `index.php?controller=user&action=formations` - Liste des formations
- `index.php?controller=user&action=formation_detail&id=X` - Détail d'une formation

#### Panier
- `index.php?controller=panier&action=show` - Voir le panier
- `index.php?controller=panier&action=add&id=X` - Ajouter au panier
- `index.php?controller=panier&action=remove&id=X` - Retirer du panier
- `index.php?controller=panier&action=clear` - Vider le panier

#### Admin
- `index.php?controller=admin&action=dashboard` - Dashboard admin
- `index.php?controller=admin&action=sections` - Liste des sections
- `index.php?controller=admin&action=sections_add` - Ajouter une section
- `index.php?controller=admin&action=sections_edit&id=X` - Modifier une section
- `index.php?controller=admin&action=sections_delete&id=X` - Supprimer une section
- `index.php?controller=admin&action=categories` - Liste des catégories
- `index.php?controller=admin&action=categories_add` - Ajouter une catégorie
- `index.php?controller=admin&action=categories_edit&id=X` - Modifier une catégorie
- `index.php?controller=admin&action=categories_delete&id=X` - Supprimer une catégorie
- `index.php?controller=admin&action=formations` - Liste des formations
- `index.php?controller=admin&action=formations_add` - Ajouter une formation
- `index.php?controller=admin&action=formations_edit&id=X` - Modifier une formation
- `index.php?controller=admin&action=formations_delete&id=X` - Supprimer une formation

## Contrôleurs

### FrontController
Route toutes les requêtes vers les bons contrôleurs selon les paramètres `controller` et `action`.

### AuthController
Gère l'authentification :
- `login()` - Traite la connexion
- `signup()` - Traite l'inscription
- `logout()` - Déconnexion
- `showLogin()` - Affiche le formulaire de connexion
- `showSignup()` - Affiche le formulaire d'inscription

### UserController
Gère les pages publiques :
- `home()` - Page d'accueil
- `formations()` - Liste des formations avec filtres
- `formationDetail()` - Détail d'une formation

### AdminController
Gère le dashboard admin :
- `dashboard()` - Affiche les statistiques

### AdminSectionController, AdminCategoryController, AdminFormationController
Gèrent les CRUD pour chaque entité :
- `list()` - Liste les éléments
- `add()` - Ajoute un élément
- `edit()` - Modifie un élément
- `delete()` - Supprime un élément

### PanierController
Gère le panier utilisateur :
- `add()` - Ajoute une formation au panier
- `show()` - Affiche le panier
- `remove()` - Retire une formation du panier
- `clear()` - Vide le panier
- `pay()` - Traite le paiement

## Modèles

Les modèles contiennent la logique d'accès aux données :
- `Section::getAll()` - Récupère toutes les sections
- `Category::getBySection($id)` - Récupère les catégories d'une section
- `Formation::getAll()` - Récupère toutes les formations
- `Formation::getById($id)` - Récupère une formation par ID
- `Formation::getByCategory($id)` - Récupère les formations d'une catégorie
- `Panier::addFormation($id)` - Ajoute une formation au panier
- `Panier::getItems()` - Récupère les items du panier
- `Panier::removeFormation($id)` - Retire une formation du panier
- `Panier::clear()` - Vide le panier

## Vues

Les vues sont organisées par fonctionnalité :
- `views/auth/` - Authentification
- `views/user/` - Pages publiques
- `views/admin/` - Interface d'administration

## Avantages de cette architecture

1. **Séparation des responsabilités** : Chaque composant a un rôle clair
2. **Maintenabilité** : Code organisé et facile à modifier
3. **Réutilisabilité** : Les modèles et contrôleurs peuvent être réutilisés
4. **Sécurité** : Vérifications centralisées dans les contrôleurs
5. **Évolutivité** : Facile d'ajouter de nouvelles fonctionnalités

## Migration depuis l'ancienne structure

Les anciens fichiers sont toujours présents mais ne sont plus utilisés :
- `login.html` → `views/auth/login.php`
- `signup.html` → `views/auth/signup.php`
- `admin_dashboard.php` → `views/admin/dashboard.php`
- `admin_sections.php` → `views/admin/sections/list.php`
- `formations.php` → `views/user/formations.php`
- `formation_detail.php` → `views/user/formation_detail.php`
- `panier.php` → `views/user/panier.php`

## Prochaines étapes

Pour compléter la migration MVC, il faut :
1. Créer toutes les vues manquantes dans `views/user/` et `views/admin/`
2. Mettre à jour tous les liens dans les vues pour utiliser le nouveau système de routage
3. Tester toutes les fonctionnalités

