# 🛒 K-Store KR — E-commerce dynamique (PHP / MySQL)

K-Store KR est un site e-commerce dynamique inspiré de l’univers coréen.  
Il permet de consulter un catalogue, rechercher et filtrer des produits, gérer un panier, passer commande et administrer le site via un back-office.

---

## ✨ Fonctionnalités

### Côté client
- Catalogue de produits avec catégories
- Fiche produit détaillée (image, description, stock, variantes)
- Page Explorer : recherche + filtres (catégorie, prix min/max, tri, stock)
- Panier : ajout, suppression, mise à jour des quantités
- Authentification : inscription / connexion / déconnexion
- Historique des commandes + détail d’une commande

### Côté admin (rôle = `admin`)
- Gestion des produits / stock (CRUD)
- Gestion des commandes (statut, archivage)
- Gestion des utilisateurs

---

## 🧰 Technologies utilisées

- PHP 8+
- MySQL / MariaDB
- PDO
- HTML / CSS
- XAMPP (Apache + MySQL)

---

## 📁 Structure du projet

-e-commerce-dynamique/
├─ admin/ # back-office (produits, commandes, utilisateurs)
├─ public/ # pages client (catalogue, panier, commandes…)
├─ includes/ # header, footer, auth, fonctions panier
├─ config/ # connexion PDO (db.php)
├─ assets/
│ ├─ css/ # styles
│ └─ img/ # images produits
└─ README.md


## ✅ Prérequis

- XAMPP installé (Apache + MySQL)
- PHP 8.x recommandé
- Navigateur web moderne


## 🚀 Installation (XAMPP)

### 1) Copier le projet dans `htdocs`

Windows :
C:\xampp\htdocs-e-commerce-dynamique\


Mac :
/Applications/XAMPP/htdocs/-e-commerce-dynamique/


### 2) Démarrer les services

Dans XAMPP Control Panel :
- Start **Apache**
- Start **MySQL**


### 3) Créer la base de données

Ouvre :

http://localhost/phpmyadmin

markdown
Copier le code

- Crée une base (ex: `kstore`)
- Importe le fichier SQL du projet (ou crée les tables)

Configure ensuite la connexion dans :

config/db.php


### 4) Lancer le site

Catalogue :

http://localhost/-e-commerce-dynamique/public/items.php

## 👤 Comptes & rôles

- Un utilisateur connecté est stocké dans :

```php
$_SESSION['user']
Un administrateur est défini par :


$_SESSION['user']['role'] === 'admin'

📌 Notes techniques
Requêtes sécurisées avec PDO (prepared statements)

Transactions utilisées pour la création de commandes

Gestion du stock automatique

Architecture volontairement simple (sans framework)

👨‍💻 Auteur
Projet réalisé par [zemmar safaa]

