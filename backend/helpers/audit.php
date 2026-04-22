<?php

require_once __DIR__ . '/../config/database.php';

function logAction($userId, $action, $entity = null, $entityId = null): void
{
    try {

        $db = new Database();
        $pdo = $db->getConnection();

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $query = "
            INSERT INTO audit_logs 
            (user_id, action, entity, entity_id, ip, user_agent, created_at)
            VALUES (:user_id, :action, :entity, :entity_id, :ip, :user_agent, NOW())
        ";

        $stmt = $pdo->prepare($query);

        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);

    } catch (Throwable $e) {

        // On évite de casser l'application si le log échoue
        error_log("Erreur audit log : " . $e->getMessage());

    }
}