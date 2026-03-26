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
    echo "Ticket introuvable.";
    exit;
}

$query = "SELECT * FROM tickets WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $id]);

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket introuvable.";
    exit;
}
?>

<h1>Modifier une requête</h1>
<?php
require_once __DIR__ . '/../../helpers/flash.php';
$flash = getFlash();
?>

<?php if ($flash): ?>
    <p><?= htmlspecialchars($flash['message']) ?></p>
<?php endif; ?>

<p>
    <a href="index.php?action=tickets_en_cours">Retour requêtes en cours</a>
</p>

<form method="POST" action="index.php?action=update_ticket">
    <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">

    <label for="title">Titre :</label><br>
    <input
        type="text"
        name="title"
        id="title"
        value="<?= htmlspecialchars($ticket['title']) ?>"
        maxlength="150"
        required
    >
    <br><br>

    <label for="description">Description :</label><br>
    <textarea
        name="description"
        id="description"
        maxlength="1000"
        required
    ><?= htmlspecialchars($ticket['description']) ?></textarea>
    <br><br>

    <label for="priority">Priorité :</label><br>
    <select name="priority" id="priority" required>
        <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Faible</option>
        <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Moyenne</option>
        <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>Haute</option>
    </select>
    <br><br>

    <label for="status">Statut :</label><br>
    <select name="status" id="status" required>
        <option value="en_cours" <?= $ticket['status'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
        <option value="traitee" <?= $ticket['status'] === 'traitee' ? 'selected' : '' ?>>Traitée</option>
        <option value="refusee" <?= $ticket['status'] === 'refusee' ? 'selected' : '' ?>>Refusée</option>
    </select>
    <br><br>

    <button type="submit">Mettre à jour</button>
</form>