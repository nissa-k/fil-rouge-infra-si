<?php
session_start();
require_once __DIR__ . '/../../helpers/flash.php';
$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
</head>
<body>

    <h1>Créer un compte</h1>

    <?php if ($flash): ?>
        <p><?= htmlspecialchars($flash['message']) ?></p>
    <?php endif; ?>

    <p><a href="index.php?action=login">Retour connexion</a></p>

    <form method="POST" action="index.php?action=do_register">
        <label for="full_name">Nom complet :</label><br>
        <input type="text" name="full_name" id="full_name" maxlength="100" required>
        <br><br>

        <label for="email">Email :</label><br>
        <input type="email" name="email" id="email" required>
        <br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" name="password" id="password" minlength="6" required>
        <br><br>

        <button type="submit">Créer le compte</button>
    </form>

</body>
</html>