<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}
?>

<h1>Créer une requête</h1>

<p><a href="index.php?action=dashboard">Retour dashboard</a></p>

<form method="POST" action="index.php?action=store_ticket">

    <label>Titre :</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Priorité :</label><br>
    <select name="priority">
        <option value="low">Faible</option>
        <option value="medium">Moyenne</option>
        <option value="high">Haute</option>
    </select>

    <br><br>

    <button type="submit">Créer la requête</button>

</form>