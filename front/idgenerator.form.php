<?php
include('../../../inc/includes.php');
Html::header('ID Generator', $_SERVER['PHP_SELF'], 'plugins', 'idgenerator');
echo "<div class='center'>";
echo "<h3>Configuration de ID Generator</h3>";
echo "<p>Le format actuel de l'identifiant est : TYPE-GRP-NUM</p>";
echo "<p>Exemples : PC-IT-001 (Ordinateur), ECR-IT-001 (Écran), TEL-IT-001 (Téléphone), SRV-IT-001 (Serveur), ROU-IT-001 (Routeur réseau), MC-IT-001 (Casque filiaire, avec fil ou sans fil), CAM-IT-001 (Caméra), CC-IT-001 (Chargeur casque), ALA-IT-001 (Alarme), CP-IT-001 (Chargeur PC), CSA-IT-001 (Chargeur station d'accueil), MDE-IT-001 (Clavier), CLI-IT-001 (Climatiseur), EAR-IT-001 (Earpods), FER-IT-001 (Fer à repasser), SOURIS-IT-001 (Souris), SA-IT-001 (Station d'accueil), SUP-IT-001 (Support téléphone), VEL-IT-001 (Vélo) avec Groupe technique 'IT'.</p>";
echo "<p>Le numéro s'incrémente lors de la création ou si le Groupe technique responsable change (suit la séquence du nouveau groupe, commence à 001 si aucune séquence).</p>";
echo "<p>L'identifiant reste fixe après la première création, sauf changement de Groupe technique responsable.</p>";
Html::footer();