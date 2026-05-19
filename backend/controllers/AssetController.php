<?php

require_once __DIR__ . '/../models/Asset.php';

class AssetController {

    private $assetModel;

    public function __construct() {

        $this->assetModel = new Asset();
    }

    /* afficher tous les assets */

    public function index() {

        $assets = $this->assetModel->getAll();

        echo json_encode([
            "success" => true,
            "assets" => $assets
        ]);
    }

    /* afficher un asset */

    public function show($id) {

        $asset = $this->assetModel->getById($id);

        if (!$asset) {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Matériel introuvable"
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "asset" => $asset
        ]);
    }

    /*creer asset */

    public function store() {

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        $name = trim($data['name'] ?? '');
        $type = trim($data['type'] ?? '');
        $marque = trim($data['marque'] ?? '');
        $modele = trim($data['modele'] ?? '');
        $serial_number = trim($data['serial_number'] ?? '');
        $os = trim($data['os'] ?? '');
        $ip_address = trim($data['ip_address'] ?? '');
        $mac_address = trim($data['mac_address'] ?? '');
        $statut = trim($data['statut'] ?? 'actif');
        $assigned_to = $data['assigned_to'] ?? null;
        $purchase_date = $data['purchase_date'] ?? null;

        if (!$name || !$type) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Nom et type obligatoires"
            ]);

            return;
        }

        $allowedTypes = [
            'pc',
            'laptop',
            'serveur',
            'switch',
            'routeur',
            'imprimante'
        ];

        if (!in_array($type, $allowedTypes)) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Type invalide"
            ]);

            return;
        }

        $result = $this->assetModel->create(
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
        );

        if (!$result) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur création matériel"
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Matériel ajouté"
        ]);
    }

    /* mettre à jour asset */

    public function update($id) {

        $asset = $this->assetModel->getById($id);

        if (!$asset) {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Matériel introuvable"
            ]);

            return;
        }

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        $name = trim($data['name'] ?? '');
        $type = trim($data['type'] ?? '');
        $marque = trim($data['marque'] ?? '');
        $modele = trim($data['modele'] ?? '');
        $serial_number = trim($data['serial_number'] ?? '');
        $os = trim($data['os'] ?? '');
        $ip_address = trim($data['ip_address'] ?? '');
        $mac_address = trim($data['mac_address'] ?? '');
        $statut = trim($data['statut'] ?? 'actif');
        $assigned_to = $data['assigned_to'] ?? null;
        $purchase_date = $data['purchase_date'] ?? null;

        $result = $this->assetModel->update(
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
        );

        if (!$result) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur modification matériel"
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Matériel modifié"
        ]);
    }

    /*supprimer asset */

    public function delete($id) {

        $asset = $this->assetModel->getById($id);

        if (!$asset) {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Matériel introuvable"
            ]);

            return;
        }

        $result = $this->assetModel->delete($id);

        if (!$result) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Erreur suppression matériel"
            ]);

            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Matériel supprimé"
        ]);
    }
}