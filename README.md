# K-Store KR — E-commerce dynamique (PHP / MySQL/ HTML)

K-Store est un site e-commerce dynamique inspiré de l’univers coréen  
Le projet permet de consulter un catalogue, filtrer/rechercher les produits, ajouter au panier, gérer un compte utilisateur et (si admin) administrer produits/commandes.

---

## ✨ Fonctionnalités

### Côté client
- Catalogue de produits (avec catégories)
- Page produit détaillée (image, description, stock)
- Explorer : recherche + filtres (catégorie, prix min/max, tri, stock)
- Panier : ajout / suppression / mise à jour des quantités
- Authentification : inscription / connexion / déconnexion
- Mes commandes (si connecté)

### Côté admin (si rôle = `admin`)
- Gestion du stock / produits
- Gestion des commandes

---

## 🧰 Technologies
- **PHP** (backend)
- **MySQL / MariaDB** (base de données)
- **HTML / CSS** (interface)
- **XAMPP** (Apache + MySQL)

---

## 📁 Structure du projet

Exemple de structure :

-e-commerce-dynamique/
├─ admin/ # pages admin (stock, commandes)
├─ assets/
│ ├─ css/ # styles (style.css)
│ └─ img/ # images produits + placeholder
├─ config/
│ └─ db.php # connexion PDO à la BDD
├─ includes/
│ ├─ header.php # barre de navigation / layout
│ ├─ footer.php # pied de page
│ └─ functions.php # fonctions panier + utilitaires
└─ public/
├─ items.php # catalogue
├─ explorer.php # recherche + filtres
├─ item.php # fiche produit
├─ cart.php # panier
├─ login.php / register.php / logout.php
├─ my_orders.php
└─ about.php # page "Qui sommes-nous"

---

## ✅ Prérequis
- XAMPP installé (Apache + MySQL)
- PHP 8.x recommandé
- Un navigateur web (Chrome / Firefox)

---

## 🚀 Installation et lancement avec XAMPP

### 1) Mettre le projet dans `htdocs`
Copie le dossier du projet dans :

- Windows : `C:\xampp\htdocs\`
- Mac : `/Applications/XAMPP/htdocs/`

Tu dois obtenir par exemple :

`C:\xampp\htdocs\-e-commerce-dynamique\`

---

### 2) Démarrer Apache et MySQL
Ouvre **XAMPP Control Panel** puis clique sur :
- ✅ Start **Apache**
- ✅ Start **MySQL**

---

### 3) Créer la base de données
Va sur phpMyAdmin :

`http://localhost/phpmyadmin`

1. Crée une base (ex : `kstore`)
2. Importe ton fichier SQL (si tu en as un) ou crée les tables nécessaires.

### 4) Comment lancer 
aller dans le navigateur et mettre :
Catalogue : http://localhost/-e-commerce-dynamique/public/items.php


👤 Comptes & rôles

Un utilisateur connecté est stocké en session $_SESSION['user'].

Un admin est un utilisateur dont $_SESSION['user']['role'] === 'admin'.