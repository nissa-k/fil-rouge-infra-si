# Fil Rouge - Frontend

## Description

Interface utilisateur du projet Fil Rouge Infrastructure SI.

L'application permet aux utilisateurs de créer et suivre leurs tickets, tandis que les administrateurs peuvent gérer les demandes et les utilisateurs.

---

## Technologies utilisées

* HTML5
* CSS3
* JavaScript
* Fetch API

---

## Fonctionnalités

### Utilisateur

* Connexion
* Double authentification
* Changement de mot de passe
* Création de tickets
* Consultation de ses tickets
* Réinitialisation du mot de passe

### Administrateur

* Consultation de tous les tickets
* Traitement ou refus des tickets
* Modification des tickets
* Suppression des tickets
* Gestion des utilisateurs
* Consultation des journaux d'audit

---

## Lancement

Après avoir démarré Apache et MySQL :

```txt
http://localhost/fil-rouge-infra-si/frontend/login.html
```

---

## Comptes de démonstration

### Administrateur

Email :

```txt
safaazemmar@gmail.com
```

Mot de passe :

```txt
admin123
```

### Client

Email :

```txt
safouzemmar@gmail.com
```

Mot de passe :

```txt
123456
```

---

## Déploiement

### Frontend

Modifier :

```txt
frontend/js/api.js
```

et remplacer l'URL de l'API par celle du serveur backend.

### Backend

Modifier :

```txt
backend/config/Database.php
```

avec les informations de connexion à la base de données du serveur.

---

## Auteur

Karadag Nissa 
Zemmar Safaa 
Mallipoudy Sriram 
El Abdallaoui Sami