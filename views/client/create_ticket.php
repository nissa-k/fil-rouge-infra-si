<?php
session_start();
require_once __DIR__ . '/../../helpers/flash.php';
$flash = getFlash();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SESSION['role'] !== 'user') {
    header("Location: index.php?action=dashboard");
    exit;
}
?>

<h1>Créer une requête</h1>

<?php if ($flash): ?>
    <p><?= htmlspecialchars($flash['message']) ?></p>
<?php endif; ?>

<p><a href="index.php?action=client_dashboard">Retour dashboard client</a></p>

<form method="POST" action="index.php?action=store_ticket">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" maxlength="150" required>
    <br><br>

    <label for="description">Description :</label><br>
    <textarea name="description" id="description" maxlength="1000" required></textarea>
    <br><br>

    <label for="priority">Priorité :</label><br>
    <select name="priority" id="priority" required>
        <option value="">-- Choisir une priorité --</option>
        <option value="low">Faible</option>
        <option value="medium">Moyenne</option>
        <option value="high">Haute</option>
    </select>
    <br><br>

    <button type="submit">Créer la requête</button>
</form>