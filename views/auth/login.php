<?php
session_start();
require_once __DIR__ . '/../../helpers/flash.php';
$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>

    <h1>Connexion</h1>

    <?php if ($flash): ?>
        <p><?= htmlspecialchars($flash['message']) ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?action=do_login">
        <label for="email">Email :</label><br>
        <input type="email" name="email" id="email" required>
        <br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" name="password" id="password" required>
        <br><br>

        <button type="submit">Se connecter</button>
    </form>

    <p>
        Pas encore de compte ?
        <a href="index.php?action=register">Créer un compte</a>
    </p>

</body>
</html>