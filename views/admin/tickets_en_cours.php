<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$query = "SELECT * FROM tickets WHERE status = 'en_cours'";
$stmt = $pdo->prepare($query);
$stmt->execute();

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Requêtes en cours</h1>

<p><a href="index.php?action=dashboard">Retour dashboard</a></p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Description</th>
        <th>Priorité</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?= $ticket['id'] ?></td>
            <td><?= htmlspecialchars($ticket['title']) ?></td>
            <td><?= htmlspecialchars($ticket['description']) ?></td>
            <td><?= htmlspecialchars($ticket['priority']) ?></td>
            <td><?= htmlspecialchars($ticket['status']) ?></td>
            <td>
                <a href="index.php?action=traiter&id=<?= $ticket['id'] ?>">Traiter</a>
                |
                <a href="index.php?action=refuser&id=<?= $ticket['id'] ?>">Refuser</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>