<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>

    <h1>Connexion</h1>

    <form action="index.php?action=do_login" method="POST">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
        </div>

        <br>

        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
        </div>

        <br>

        <button type="submit">Se connecter</button>
    </form>

</body>
</html>