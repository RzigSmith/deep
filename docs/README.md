# Smith Collection - Site e-commerce

Smith Collection est une application web de e-commerce développée en PHP avec une architecture MVC simplifiée. Elle permet la gestion de produits, utilisateurs, commandes, panier, et propose une interface d'administration complète.

---

## Fonctionnalités principales

- **Gestion des utilisateurs** : inscription, connexion, profil, rôles (client/admin), adresses.
- **Catalogue produits** : affichage, ajout, modification, suppression (admin), catégories.
- **Panier** : ajout, modification, suppression d’articles, synchronisation avec la session et le localStorage.
- **Commandes** : historique, passage de commande, suivi du statut.
- **Administration** : tableau de bord, gestion des utilisateurs, produits, messages de contact.
- **Sécurité** : gestion de session, vérification des rôles, protection des pages sensibles, hashage des mots de passe.
- **Front-end** : design responsive avec CSS personnalisé, interactions dynamiques en JavaScript (sans framework).

---

## Structure du projet

```
deep/
│
├── admin/           # Interface et scripts d’administration (dashboard, gestion produits, utilisateurs, messages, etc.)
│   ├── admin.php
│   ├── admin_profile.php
│   ├── config.php
│   ├── dashboard.php
│   ├── data.php
│   ├── delete_product.php
│   ├── edit_product.php
│   ├── logout.php
│   ├── manage_users.php
│   ├── messages.php
│   ├── orders.php
│   ├── promote_user.php
│   ├── save_product.php
│   ├── styles.css
│   ├── users.css
│   ├── mydashboard.css
│   ├── messages.css
│   └── uploads/         # Dossier pour les images uploadées par l’admin
│
├── api/             # Endpoints AJAX pour le panier, produits, utilisateurs, etc.
│   ├── cart_add.php
│   └── ...           # Autres endpoints API
│
├── assets/          # Ressources statiques (CSS, JS, images)
│   ├── css/
│   │   ├── monaccueil.css
│   │   └── ...       # Autres feuilles de style
│   ├── js/
│   │   ├── panier.js
│   │   └── ...       # Autres scripts JS
│   └── images/       # Images produits, fonds, logos
│
├── classes/         # Classes PHP (logique métier : gestion utilisateurs, produits, panier, etc.)
│   ├── ProductManager.php
│   ├── CartManager.php
│   ├── UserManager.php
│   └── ...           # Autres classes métier
│
├── includes/        # Fichiers d’inclusion (configuration, fonctions utilitaires, connexion DB)
│   └── db.php
│
├── adresses.php     # Gestion des adresses de livraison
├── index.php        # Page d’accueil (affichage produits, navigation)
├── login.php        # Connexion utilisateur
├── logout.php       # Déconnexion
├── order.php        # Commandes utilisateur
├── profile.php      # Profil utilisateur
├── register.php     # Inscription utilisateur
├── welcome.php      # Page d’accueil post-connexion client
├── .gitattributes
├── .vscode/
│   └── settings.json
└── README.md        # Documentation du projet
```

---

## Détail des dossiers

- **/admin**  
  Pages réservées aux administrateurs : dashboard dynamique (statistiques, graphiques), gestion des utilisateurs, produits, commandes, messages de contact, etc.  
  CSS personnalisé, pas de framework.

- **/api**  
  Points d’entrée pour les appels AJAX (panier, produits, utilisateurs…).  
  Chaque endpoint appelle une classe métier du dossier `/classes` et retourne du JSON.

- **/assets**  
  - **css/** : Feuilles de style pour le front et l’admin.
  - **js/** : Scripts pour le panier, l’UI dynamique, etc.
  - **images/** : Images produits, fonds, logos.

- **/classes**  
  Logique métier centralisée :  
  - `ProductManager.php` : gestion des produits.
  - `CartManager.php` : gestion du panier (session).
  - `UserManager.php` : gestion des utilisateurs.
  - (Extensible pour commandes, messages…)

- **/includes**  
  Fichiers d’inclusion réutilisables : connexion à la base de données, fonctions utilitaires, configuration globale.

- **Pages principales**  
  - `index.php` : accueil, navigation, affichage produits.
  - `login.php` / `register.php` : authentification.
  - `profile.php` : gestion du profil utilisateur.
  - `order.php` : historique et gestion des commandes.
  - `adresses.php` : gestion des adresses de livraison.
  - `welcome.php` : page d’accueil après connexion client.
  - `logout.php` : déconnexion.

---

## Technologies utilisées

- **Backend** : PHP natif (POO), MySQL (PDO)
- **Frontend** : HTML5, CSS3, JavaScript (vanilla, Chart.js pour les graphiques)
- **Sécurité** : Sessions PHP, hashage des mots de passe, vérification des rôles, validation des entrées
- **Responsive** : CSS personnalisé

---

## Bonnes pratiques et organisation

- **Séparation claire** entre logique métier (classes), interface (API), et présentation (pages, assets).
- **Centralisation de la connexion DB** dans `/includes/db.php`.
- **Gestion du panier** : toutes les actions JS passent par l’API serveur, synchronisation avec la session.
- **Protection des pages sensibles** : vérification du rôle et de la connexion sur chaque page.
- **Code maintenable et évolutif** : chaque entité (produit, utilisateur, panier…) a sa propre classe et son endpoint API dédié.

---

## Auteur

MAMBOTE MAZULU Icksan  
L1/LMD-FASI

---

> Ce projet est un exemple pédagogique de site e-commerce en PHP natif.