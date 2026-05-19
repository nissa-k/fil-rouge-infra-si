<?php

require_once __DIR__ . '/../config/Database.php';

class Asset {

    private $db;

    public function __construct() {

        $this->db = Database::getConnection();
    }

    /* afficher tous les assets */

    public function getAll() {

        $stmt = $this->db->prepare("
            SELECT
                assets.*,

                users.full_name AS assigned_user

            FROM assets

            LEFT JOIN users
                ON assets.assigned_to = users.id

            ORDER BY assets.created_at DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* afficher un asset par son ID */

    public function getById($id) {

        $stmt = $this->db->prepare("
            SELECT
                assets.*,

                users.full_name AS assigned_user

            FROM assets

            LEFT JOIN users
                ON assets.assigned_to = users.id

            WHERE assets.id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*creer asset */

    public function create(
        $name,
        $type,
        $marque,
        $modele,
        $serial_number,
        $os,
        $ip_address,
        $mac_address,
        $statut,
        $assigned_to,
        $purchase_date
    ) {

        $stmt = $this->db->prepare("
            INSERT INTO assets (

                name,
                type,
                marque,
                modele,
                serial_number,
                os,
                ip_address,
                mac_address,
                statut,
                assigned_to,
                purchase_date

            ) VALUES (

                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        return $stmt->execute([

            $name,
            $type,
            $marque,
            $modele,
            $serial_number,
            $os,
            $ip_address,
            $mac_address,
            $statut,
            $assigned_to,
            $purchase_date
        ]);
    }

    /*mettre à jour asset */

    public function update(
        $id,
        $name,
        $type,
        $marque,
        $modele,
        $serial_number,
        $os,
        $ip_address,
        $mac_address,
        $statut,
        $assigned_to,
        $purchase_date
    ) {

        $stmt = $this->db->prepare("
            UPDATE assets

            SET
                name = ?,
                type = ?,
                marque = ?,
                modele = ?,
                serial_number = ?,
                os = ?,
                ip_address = ?,
                mac_address = ?,
                statut = ?,
                assigned_to = ?,
                purchase_date = ?

            WHERE id = ?
        ");

        return $stmt->execute([

            $name,
            $type,
            $marque,
            $modele,
            $serial_number,
            $os,
            $ip_address,
            $mac_address,
            $statut,
            $assigned_to,
            $purchase_date,
            $id
        ]);
    }

    /*supprimer asset */

    public function delete($id) {

        $stmt = $this->db->prepare("
            DELETE FROM assets
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}