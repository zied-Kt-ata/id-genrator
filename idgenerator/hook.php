<?php
function plugin_idgenerator_generate_id($item) {
    global $DB;

    $asset_types = ['Computer','Monitor','Printer','Phone','NetworkEquipment','Peripheral','HeadsetWireless','HeadsetWired','HeadsetFiliaried','Camera','Vacuum','Alarm','PCCharger','HeadsetCharger','DockingCharger','AirConditioner','Earpods','Iron','Mouse','WirelessMouse','DockingStation','PhoneStand','Bike','Keyboard'];

    if (!in_array($item->getType(), $asset_types)) return;

    error_log("ID Generator - Hook pre_item_add/pre_item_update pour type : " . $item->getType());
    error_log("ID Generator - Fields avant génération : " . print_r($item->fields,true));

    $current_uuid = $item->fields['uuid'] ?? '';
    $original_group_id = $item->fields['groups_id_tech'] ?? 0;

    $old_group_id = 0;
    if (isset($item->fields['id']) && $item->fields['id'] > 0) {
        $table = $item->getTable();
        $result = $DB->request(['SELECT'=>['groups_id_tech'],'FROM'=>$table,'WHERE'=>['id'=>$item->fields['id']]])->current();
        $old_group_id = $result['groups_id_tech'] ?? 0;
    }

    if (empty($current_uuid) || $original_group_id != $old_group_id) {
        $new_id = PluginIdgeneratorIdgenerator::generateId($item);
        if ($new_id) {
            $item->fields['uuid'] = $new_id;
            if (isset($item->fields['id']) && $item->fields['id'] > 0) {
                $table = $item->getTable();
                if ($DB->tableExists($table)) {
                    $DB->updateOrDie($table,['uuid'=>$new_id],['id'=>$item->fields['id']], "Erreur lors de la mise à jour du champ uuid pour {$item->getType()} ID ".$item->fields['id']);
                    error_log("ID Generator - Champ uuid mis à jour (pre_item) : $new_id");
                }
            }
        } else {
            error_log("ID Generator - Échec de génération pour {$item->getType()}");
            Session::addMessageAfterRedirect("Erreur : impossible de générer l'identifiant pour {$item->getType()}. Vérifiez le champ Groupe technique responsable.", false, ERROR);
        }
    } else {
        error_log("ID Generator - Identifiant inchangé : $current_uuid");
    }

    error_log("ID Generator - Fields après génération : " . print_r($item->fields,true));
}

function plugin_idgenerator_update_uuid($item) {
    global $DB;

    $asset_types = ['Computer','Monitor','Printer','Phone','NetworkEquipment','Peripheral','HeadsetWireless','HeadsetWired','HeadsetFiliaried','Camera','Vacuum','Alarm','PCCharger','HeadsetCharger','DockingCharger','AirConditioner','Earpods','Iron','Mouse','WirelessMouse','DockingStation','PhoneStand','Bike','Keyboard'];

    if (!in_array($item->getType(), $asset_types)) return;
    if (!isset($item->fields['id']) || $item->fields['id'] <= 0) return;

    error_log("ID Generator - Hook item_add/item_update pour {$item->getType()} ID ".$item->fields['id']);

    $current_uuid = $item->fields['uuid'] ?? '';
    $original_group_id = $item->fields['groups_id_tech'] ?? 0;

    $table = $item->getTable();
    $result = $DB->request(['SELECT'=>['groups_id_tech'],'FROM'=>$table,'WHERE'=>['id'=>$item->fields['id']]])->current();
    $old_group_id = $result['groups_id_tech'] ?? 0;

    if (!$current_uuid || $original_group_id != $old_group_id) {
        $new_id = PluginIdgeneratorIdgenerator::generateId($item);
        if ($new_id && $DB->tableExists($table)) {
            $DB->updateOrDie($table,['uuid'=>$new_id],['id'=>$item->fields['id']], "Erreur lors de la mise à jour du champ uuid pour {$item->getType()} ID ".$item->fields['id']);
            error_log("ID Generator - Champ uuid mis à jour (item_add/item_update) : $new_id");
        } else {
            error_log("ID Generator - Échec de génération pour {$item->getType()} ID ".$item->fields['id']);
        }
    } else {
        error_log("ID Generator - Aucun changement d'UUID nécessaire : $current_uuid");
    }
}
