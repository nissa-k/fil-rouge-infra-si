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

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Ticket introuvable";
    exit;
}

$query = "SELECT * FROM tickets WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $id]);

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket introuvable";
    exit;
}
?>

<h1>Modifier la requête</h1>

<p><a href="index.php?action=tickets_en_cours">Retour requêtes en cours</a></p>

<form method="POST" action="index.php?action=update_ticket">
    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">

    <label>Titre :</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($ticket['title']) ?>" required>
    <br><br>

    <label>Description :</label><br>
    <textarea name="description" required><?= htmlspecialchars($ticket['description']) ?></textarea>
    <br><br>

    <label>Priorité :</label><br>
    <select name="priority">
        <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>low</option>
        <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>medium</option>
        <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>high</option>
    </select>
    <br><br>

    <label>Statut :</label><br>
    <select name="status">
        <option value="en_cours" <?= $ticket['status'] === 'en_cours' ? 'selected' : '' ?>>en_cours</option>
        <option value="traitee" <?= $ticket['status'] === 'traitee' ? 'selected' : '' ?>>traitee</option>
        <option value="refusee" <?= $ticket['status'] === 'refusee' ? 'selected' : '' ?>>refusee</option>
    </select>
    <br><br>

    <button type="submit">Mettre à jour</button>
</form>