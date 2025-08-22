<?php
define('PLUGIN_IDGENERATOR_VERSION', '1.1.4');

include_once(Plugin::getPhpDir('idgenerator') . '/inc/idgenerator.class.php');

/**
 * Initialisation du plugin
 */
function plugin_init_idgenerator() {
    global $PLUGIN_HOOKS;

    // Plugin CSRF compliant
    $PLUGIN_HOOKS['csrf_compliant']['idgenerator'] = true;

    // Types d'actifs pris en charge
    $asset_types = [
        'Computer', 'Monitor', 'Printer', 'Phone', 'NetworkEquipment', 'Peripheral',
        'HeadsetWireless', 'HeadsetWired', 'HeadsetFiliaried', 'Camera', 'Vacuum',
        'Alarm', 'PCCharger', 'HeadsetCharger', 'DockingCharger', 'AirConditioner',
        'Earpods', 'Iron', 'Mouse', 'WirelessMouse', 'DockingStation', 'PhoneStand', 'Keyboard'
    ];

    // Ajouter des hooks pour chaque type d'actif
    foreach ($asset_types as $type) {
        $PLUGIN_HOOKS['pre_item_add']['idgenerator'][$type]    = 'plugin_idgenerator_generate_id';
        $PLUGIN_HOOKS['pre_item_update']['idgenerator'][$type] = 'plugin_idgenerator_generate_id';
        $PLUGIN_HOOKS['item_add']['idgenerator'][$type]        = 'plugin_idgenerator_update_uuid';
        $PLUGIN_HOOKS['item_update']['idgenerator'][$type]     = 'plugin_idgenerator_update_uuid';
    }

    // Ajouter une entrée dans le menu Plugins
    $PLUGIN_HOOKS['menu_toadd']['idgenerator'] = [
        'plugins' => 'PluginIdgeneratorIdgenerator'
    ];
}

/**
 * Informations sur le plugin
 */
function plugin_version_idgenerator() {
    return [
        'name'         => 'ID Generator',
        'version'      => PLUGIN_IDGENERATOR_VERSION,
        'author'       => 'ZIED KTATA',
        'license'      => 'GPLv2+',
        'homepage'     => 'https://github.com/zied-Kt-ata/id-genrator',
        'requirements' => [
            'glpi' => [
                'min' => '10.0',
                'max' => '11.9'
            ],
            'php' => [
                'min' => '7.4'
            ]
        ]
    ];
}

/**
 * Vérification des prérequis
 */
function plugin_idgenerator_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '10.0', '<')) {
        echo "Ce plugin nécessite GLPI >= 10.0";
        return false;
    } elseif (version_compare(GLPI_VERSION, '12.0', '>=')) {
        echo "Ce plugin n'est pas compatible avec GLPI >= 12.0";
        return false;
    }
    return true;
}

/**
 * Vérification de la configuration
 */
function plugin_idgenerator_check_config() {
    return true;
}

/**
 * Installation du plugin
 */
function plugin_idgenerator_install() {
    global $DB;

    $table = 'glpi_plugin_idgenerator_configs';
    if (!$DB->tableExists($table)) {
        $query = "CREATE TABLE `$table` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            `value` VARCHAR(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_unique` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->queryOrDie($query, "Erreur lors de la création de la table $table");
    }

    $DB->insertOrDie($table, [
        'name'  => 'id_format',
        'value' => 'TYPE-GRP-NUM'
    ], "Erreur lors de l'insertion de la configuration par défaut");

    return true;
}

/**
 * Désinstallation du plugin
 */
function plugin_idgenerator_uninstall() {
    global $DB;

    $table = 'glpi_plugin_idgenerator_configs';
    if ($DB->tableExists($table)) {
        $query = "DROP TABLE `$table`";
        $DB->queryOrDie($query, "Erreur lors de la suppression de la table $table");
    }

    return true;
}

/**
 * Fonction pour générer l'ID avant création ou mise à jour
 */
function plugin_idgenerator_generate_id($item) {
    if (!isset($item->fields['name']) || !empty($item->fields['idnumber'])) {
        return;
    }

    $type = get_class($item);
    $group = $item->fields['entities_id'] ?? 0;

    // Récupérer le prochain numéro disponible
    $next_num = plugin_idgenerator_get_next_number($type, $group);

    // Générer l'ID selon le format TYPE-GRP-NUM
    $item->fields['idnumber'] = strtoupper($type) . '-' . $group . '-' . str_pad($next_num, 3, '0', STR_PAD_LEFT);
}

/**
 * Fonction pour mettre à jour l'UUID après création ou mise à jour
 */
function plugin_idgenerator_update_uuid($item) {
    // Ici tu peux ajouter un mécanisme pour stocker l'UUID ou synchroniser d'autres tables
}

/**
 * Récupération du prochain numéro disponible pour un type et un groupe
 */
function plugin_idgenerator_get_next_number($type, $group) {
    global $DB;

    $table = 'glpi_plugin_idgenerator_counters';
    if (!$DB->tableExists($table)) {
        $query = "CREATE TABLE `$table` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(50) NOT NULL,
            `group_id` INT(11) NOT NULL,
            `last_num` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `type_group_unique` (`type`,`group_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->queryOrDie($query, "Erreur lors de la création de la table $table");
    }

    $record = $DB->request($table, ['type' => $type, 'group_id' => $group])->next();
    if ($record) {
        $next_num = $record['last_num'] + 1;
        $DB->update($table, ['last_num' => $next_num], ['id' => $record['id']]);
    } else {
        $next_num = 1;
        $DB->insert($table, ['type' => $type, 'group_id' => $group, 'last_num' => $next_num]);
    }

    return $next_num;
}
