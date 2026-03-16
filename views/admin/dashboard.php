<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=client_dashboard");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>

    <h1>Dashboard Admin</h1>
    <p>Bienvenue <?= htmlspecialchars($_SESSION['user_name']) ?></p>

    <ul>
        <li><a href="index.php?action=tickets_en_cours">Requêtes en cours</a></li>
        <li><a href="index.php?action=tickets_traites">Requêtes traitées</a></li>
        <li><a href="index.php?action=tickets_refuses">Requêtes refusées</a></li>
        <li><a href="index.php?action=create_ticket">Créer une requête</a></li>
        <li><a href="index.php?action=logout">Déconnexion</a></li>
    </ul>

</body>
</html>