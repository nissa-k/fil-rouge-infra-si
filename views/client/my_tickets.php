<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SESSION['role'] !== 'user') {
    header("Location: index.php?action=dashboard");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$query = "SELECT * FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'user_id' => $_SESSION['user_id']
]);

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Mes requêtes</h1>

<p><a href="index.php?action=client_dashboard">Retour dashboard client</a></p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Description</th>
        <th>Priorité</th>
        <th>Statut</th>
        <th>Date création</th>
    </tr>

    <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?= $ticket['id'] ?></td>
            <td><?= htmlspecialchars($ticket['title']) ?></td>
            <td><?= htmlspecialchars($ticket['description']) ?></td>
            <td><?= htmlspecialchars($ticket['priority']) ?></td>
            <td><?= htmlspecialchars($ticket['status']) ?></td>
            <td><?= htmlspecialchars($ticket['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>