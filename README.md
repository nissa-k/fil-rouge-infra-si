fil-rouge-infra-si

Application web de gestion de requêtes IT réalisée dans le cadre du projet Fil Rouge Infrastructure SI.

Auteurs
Karadag Nissa
Zemmar Safaa
Mallipoudy Sriram
El Abdallaoui Sami
Description

L'application permet aux utilisateurs de créer des requêtes (tickets) et aux administrateurs de les gérer.

Fonctionnalités

Utilisateur

créer un compte
se connecter
créer une requête
consulter ses requêtes

Administrateur

voir les requêtes
traiter ou refuser une requête
modifier ou supprimer une requête
gérer les utilisateurs


consulter les logs d’audit
Sécurité
mots de passe hashés
requêtes préparées (PDO)
validation des données
contrôle d'accès par rôle
journalisation des actions (audit_logs)



fil-rouge-infra-si/
│
├── config/
│   ├── database.php
│   └── filrouge.sql
│
├── controllers/
│   └── AuthController.php
│
├── helpers/
│   ├── audit.php
│   └── flash.php
│
├── logs/
│   └── audit.log
│
├── models/
│   └── User.php
│
├── public/
│   └── css/
│       └── style.css
│
├── services/
│   ├── AuthService.php
│   └── TicketService.php
│
├── views/
│   │
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── edit_ticket.php
│   │   ├── tickets_en_cours.php
│   │   ├── tickets_refuses.php
│   │   ├── tickets_traites.php
│   │   └── users.php
│   │
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   │
│   └── client/
│       ├── create_ticket.php
│       ├── dashboard.php
│       ├── my_tickets.php
│
├── index.php
└── README.md



Comptes de test :

Admin
safaazemmar@gmail.com
mdp : admin123         
Client

email : safouzemmar@gmail.com
mdp :  123456

Installation
click sur ce lien 
https://github.com/nissa-k/fil-rouge-infra-si.git

placer le projet dans xampp/htdocs

créer la base de données filrouge ou importer le fichier SQL


ouvrir dans le navigateur :

http://localhost/fil-rouge-infra-si/frontend/login.html



## Déploiement sur deux serveurs

### Frontend
Modifier le fichier :

frontend/js/api.js

et remplacer :

const API_BASE = "https://URL_DU_BACKEND_ICI";

par l’URL réelle du backend.

### Backend
Modifier le fichier :

backend/public/index.php

et remplacer :

https://URL_DU_FRONTEND_ICI

par l’URL réelle du frontend.

### Base de données
Modifier :

backend/config/database.php

avec les identifiants MySQL du serveur.

### Important
Si le frontend et le backend sont sur deux domaines différents, HTTPS est nécessaire pour que les sessions PHP fonctionnent correctement avec les cookies.