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

email : admin@test.com
mdp : admin123

Client

email : client@test.com
mdp : client123

Installation
click sur ce lien 
https://github.com/nissa-k/fil-rouge-infra-si.git

placer le projet dans xampp/htdocs

créer la base de données filrouge ou importer le fichier SQL


ouvrir dans le navigateur :

http://localhost/FIL-ROUGE-INFRA-SI