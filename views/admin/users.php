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

$query = "SELECT * FROM users ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Gestion des utilisateurs</h1>

<p><a href="index.php?action=dashboard">Retour dashboard</a></p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Actif</th>
        <th>Action</th>
    </tr>

    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['is_active']) ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="index.php?action=delete_user&id=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                <?php else: ?>
                    Impossible
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>