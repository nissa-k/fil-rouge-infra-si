<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SESSION['role'] !== 'user') {
    header("Location: index.php?action=dashboard");
    exit;
}
?>

<h1>Dashboard Client</h1>

<p>Bienvenue <?= htmlspecialchars($_SESSION['user_name']) ?></p>

<ul>
    <li><a href="index.php?action=create_ticket">Formuler une requête</a></li>
    <li><a href="index.php?action=my_tickets">Mes requêtes</a></li>
    <li><a href="index.php?action=logout">Déconnexion</a></li>
</ul>