<?php
define('PLUGIN_IDGENERATOR_VERSION', '1.1.4');

// Inclure la classe principale
include_once(Plugin::getPhpDir('idgenerator') . '/inc/idgenerator.class.php');

/**
 * Initialisation du plugin
 */
function plugin_init_idgenerator() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['idgenerator'] = true;

    // Types d'actifs pris en charge, y compris les périphériques
    $asset_types = ['Computer', 'Monitor', 'Printer', 'Phone', 'NetworkEquipment', 'Peripheral', 'HeadsetWireless', 'HeadsetWired', 'HeadsetFiliaried', 'Camera', 'Vacuum', 'Alarm', 'PCCharger', 'HeadsetCharger', 'DockingCharger', 'AirConditioner', 'Earpods', 'Iron', 'Mouse', 'WirelessMouse', 'DockingStation', 'PhoneStand', 'Bike', 'Keyboard'];

    // Ajouter des hooks pour chaque type d'actif
    foreach ($asset_types as $type) {
        $PLUGIN_HOOKS['pre_item_add']['idgenerator'][$type] = 'plugin_idgenerator_generate_id';
        $PLUGIN_HOOKS['pre_item_update']['idgenerator'][$type] = 'plugin_idgenerator_generate_id';
        $PLUGIN_HOOKS['item_add']['idgenerator'][$type] = 'plugin_idgenerator_update_uuid';
        $PLUGIN_HOOKS['item_update']['idgenerator'][$type] = 'plugin_idgenerator_update_uuid';
    }

    // Ajouter une entrée au menu Plugins
    $PLUGIN_HOOKS['menu_toadd']['idgenerator'] = [
        'plugins' => 'PluginIdgeneratorIdgenerator'
    ];
}

/**
 * Informations sur le plugin
 */
function plugin_version_idgenerator() {
    return [
        'name'           => 'ID Generator',
        'version'        => PLUGIN_IDGENERATOR_VERSION,
        'author'         => 'ZIED KTATA',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://votre-site.com',
        'requirements'   => [
            'glpi' => [
                'min' => '10.0'
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
            PRIMARY KEY (`id`)
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