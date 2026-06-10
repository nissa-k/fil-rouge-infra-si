# Fil Rouge - Backend

## Description

Backend du projet Fil Rouge Infrastructure SI développé en PHP et MySQL.

Le backend gère :

* L'authentification des utilisateurs
* La double authentification (2FA)
* La réinitialisation de mot de passe
* La gestion des utilisateurs
* La gestion des tickets
* La gestion des assets
* La journalisation des actions

---

## Technologies utilisées

* PHP 8+
* MySQL
* PDO
* Composer
* PHPMailer
* Dotenv (vlucas/phpdotenv)

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/nissa-k/fil-rouge-infra-si.git
```

### 2. Placer le projet

Copier le dossier dans :

```txt
xampp/htdocs/
```

### 3. Installer les dépendances

Dans le dossier backend :

```bash
composer install
```

### 4. Base de données

Créer une base de données :

```sql
filrouge
```

Puis importer le fichier SQL fourni avec le projet.

### 5. Configuration

Modifier le fichier :

```txt
backend/config/Database.php
```

avec les paramètres de votre base de données.

### 6. Démarrer les services

Lancer :

* Apache
* MySQL

depuis XAMPP.

---

## Fonctionnalités

### Authentification

* Connexion
* Déconnexion
* Double authentification par email
* Mot de passe oublié
* Changement obligatoire du mot de passe à la première connexion

### Tickets

* Création de tickets
* Consultation des tickets
* Modification des tickets
* Suppression des tickets
* Traitement ou refus des tickets

### Administration

* Gestion des utilisateurs
* Gestion des tickets
* Consultation des logs d'audit

---

## Sécurité

* Password hashing avec `password_hash()`
* Vérification avec `password_verify()`
* Sessions PHP
* Requêtes préparées PDO
* Contrôle d'accès par rôle
* Journalisation des actions

---

## Auteur

Karadag Nissa 
Zemmar Safaa 
Mallipoudy Sriram 
El Abdallaoui Sami