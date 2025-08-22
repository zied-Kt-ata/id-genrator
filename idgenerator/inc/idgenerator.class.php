<?php
class PluginIdgeneratorIdgenerator extends CommonDBTM {
    static function getTypeName($nb = 0) {
        return 'ID Generator';
    }

    static function getMenuContent() {
        $menu = [];
        $menu['title'] = self::getTypeName();
        $menu['page'] = '/plugins/idgenerator/front/idgenerator.form.php';
        return $menu;
    }

    static function generateId($item) {
        global $DB;
        $group_id = $item->fields['groups_id_tech'] ?? 0;

        // Définir l'abréviation fixe basée sur le type d'actif ou le nom pour NetworkEquipment et Peripheral
        $type_abbr = self::getTypeAbbreviation($item->getType(), $item->fields['name'] ?? '');
        error_log("ID Generator - Type: {$item->getType()}, Abbreviation fixe: '$type_abbr'");

        // Vérifier que le champ Groupe technique responsable est rempli
        if ($group_id == 0) {
            error_log("ID Generator - Erreur : Group ID: $group_id");
            return false;
        }

        // Récupérer le nom du groupe
        $group = new Group();
        $group_name = '';
        if ($group_id > 0) {
            error_log("ID Generator - Tentative de récupération du groupe avec ID : $group_id");
            if ($group->getFromDB($group_id)) {
                $group_name = trim(strtolower($group->fields['name']));
                error_log("ID Generator - Group Name brut: '$group_name'");
            } else {
                error_log("ID Generator - Erreur : Impossible de récupérer le groupe pour group_id: $group_id");
                return false;
            }
        }

        // Extraire les initiales pour le groupe
        $group_words = explode(' ', $group_name);
        error_log("ID Generator - Group words: " . print_r($group_words, true));
        $group_abbr = '';
        if (count($group_words) >= 2) {
            $group_abbr = strtoupper(substr($group_words[0], 0, 1) . substr($group_words[1], 0, 1));
        } else {
            $group_abbr = strtoupper(substr(preg_replace('#[^a-zA-Z0-9]#', '', $group_name), 0, 2));
        }
        error_log("ID Generator - Group abbreviation: '$group_abbr'");

        // Calculer le prochain numéro incrémental pour le groupe
        $table = $item->getTable();
        $next_number = 1;
        $query = "SELECT MAX(CAST(SUBSTRING_INDEX(uuid, '-', -1) AS UNSIGNED)) as max_num 
                  FROM $table 
                  WHERE uuid LIKE '$type_abbr-$group_abbr-%' 
                  AND groups_id_tech = $group_id";
        $result = $DB->query($query);
        if ($result && $DB->numRows($result) > 0) {
            $row = $DB->fetchAssoc($result);
            $max_num = $row['max_num'] ?? 0;
            $next_number = $max_num + 1;
        }
        error_log("ID Generator - Prochain numéro incrémental : $next_number");

        // Générer l'identifiant
        $generated_id = "{$type_abbr}-{$group_abbr}-" . str_pad($next_number, 3, '0', STR_PAD_LEFT);
        error_log("ID Generator - Identifiant généré : $generated_id");

        return $generated_id;
    }

    /**
     * Retourne l'abréviation fixe basée sur le type d'actif
     * Gestion spéciale pour NetworkEquipment et Peripheral (basée sur le nom)
     */
    static function getTypeAbbreviation($type, $name = '') {
        $abbreviations = [
            'Computer' => 'PC',
            'Monitor' => 'ECR',
            'Printer' => 'IMP',
            'Phone' => 'TEL'
        ];

        if ($type === 'NetworkEquipment') {
            $name = trim(strtolower($name));
            if (strpos($name, 'serveur') !== false) {
                return 'SRV';
            } elseif (strpos($name, 'routeur') !== false || strpos($name, 'reseau') !== false) {
                return 'ROU';
            }
            return 'NET'; // Par défaut si aucun critère ne correspond
        }

        if ($type === 'Peripheral' || in_array($type, ['HeadsetWireless', 'HeadsetWired', 'HeadsetFiliaried', 'Camera', 'HeadsetCharger', 'Alarm', 'PCCharger', 'DockingCharger', 'Keyboard', 'AirConditioner', 'Earpods', 'Iron', 'Mouse', 'WirelessMouse', 'DockingStation', 'PhoneStand', 'Bike'])) {
            $name = trim(strtolower($name));
            if (strpos($name, 'casque filiaire') !== false || strpos($name, 'casque avec fil') !== false || strpos($name, 'casque sans fil') !== false) {
                return 'MC';
            } elseif (strpos($name, 'chargeur casque') !== false) {
                return 'CC';
            } elseif (strpos($name, 'chargeur pc') !== false) {
                return 'CP';
            } elseif (strpos($name, 'chargeur station daccueil') !== false) {
                return 'CSA';
            } elseif (strpos($name, 'clavier') !== false) {
                return 'MDE';
            } elseif (strpos($name, 'climatiseur') !== false) {
                return 'CLI';
            } elseif (strpos($name, 'earpods') !== false) {
                return 'EAR';
            } elseif (strpos($name, 'fer') !== false) {
                return 'FER';
            } elseif (strpos($name, 'souris') !== false) {
                return 'SOURIS';
            } elseif (strpos($name, 'station daccueil') !== false) {
                return 'SA';
            } elseif (strpos($name, 'camera') !== false) {
                return 'CAM';
            } elseif (strpos($name, 'alarm') !== false) {
                return 'ALA';
            }
            return 'PRF'; // Par défaut pour Peripheral si aucun critère ne correspond
        }

        return $abbreviations[$type] ?? 'UNK'; // 'UNK' en cas d'erreur pour autres types
    }
}