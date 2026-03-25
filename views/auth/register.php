<h1>Créer un compte</h1>

<form method="POST" action="index.php?action=do_register">

    <label>Nom complet :</label><br>
    <input type="text" name="full_name" required>
    <br><br>

    <label>Email :</label><br>
    <input type="email" name="email" required>
    <br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Créer le compte</button>

</form>

<br>

<a href="index.php?action=login">Retour connexion</a>