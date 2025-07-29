ID Generator Plugin for GLPI
Overview
The ID Generator plugin for GLPI automatically generates unique identifiers for assets in a TYPE-GRP-NUM format (e.g., PC-IT-001 for a Computer in the IT group). It supports a wide range of asset types, including Computers, Monitors, Printers, Phones, Network Equipment, and various peripherals like headsets, chargers, and docking stations. The plugin ensures consistent asset tracking by assigning fixed IDs that only change if the responsible technical group is modified.
Features

Automatic ID Generation: Creates unique IDs based on asset type, technical group, and an incremental number.
Supported Asset Types: Includes Computers, Monitors, Printers, Phones, Network Equipment, and peripherals like Headsets, Cameras, Keyboards, and more.
Dynamic ID Updates: Regenerates IDs when the technical group changes, using the sequence of the new group.
Configurable Format: Uses a TYPE-GRP-NUM format (e.g., PC-IT-001, SRV-IT-001, CAM-IT-001).
Error Logging: Detailed logs for debugging and tracking ID generation processes.
GLPI Integration: Seamlessly integrates with GLPI 10.0+ via hooks for item creation and updates.

Requirements

GLPI Version: 10.0 or higher
PHP Version: 7.4 or higher

Installation

Download: Obtain the plugin from the download link.
Extract: Unzip the plugin archive and place the idgenerator folder in the plugins directory of your GLPI installation (e.g., /path/to/glpi/plugins/idgenerator).
Install via GLPI:
Log in to GLPI as an administrator.
Navigate to Setup > Plugins.
Locate ID Generator in the plugin list and click Install.
Activate the plugin by clicking Enable.


Database Setup: The plugin automatically creates a configuration table (glpi_plugin_idgenerator_configs) during installation with the default ID format TYPE-GRP-NUM.

Configuration

Access Configuration:
Go to Setup > Plugins > ID Generator in GLPI.
The configuration page displays the current ID format (TYPE-GRP-NUM) and example IDs (e.g., PC-IT-001, ECR-IT-001, TEL-IT-001).


Technical Group Requirement:
Ensure that assets have a valid Technical Group Responsible (groups_id_tech) assigned, as this is required for ID generation.
The group name is used to create a two-letter abbreviation (e.g., "Information Technology" → IT).


ID Format:
The ID consists of:
TYPE: An abbreviation based on the asset type (e.g., PC for Computer, SRV for Server, CAM for Camera).
GRP: A two-letter abbreviation derived from the technical group name.
NUM: A three-digit incremental number (e.g., 001, 002), reset per group and type.


The format is fixed but can be modified by updating the id_format value in the glpi_plugin_idgenerator_configs table.



Usage

Creating Assets: When adding an asset (e.g., Computer, Monitor, Peripheral) in GLPI, the plugin automatically generates a unique ID if the technical group is specified.
Updating Assets: If the technical group changes, a new ID is generated based on the new group’s sequence.
Supported Asset Types:
Standard: Computer (PC), Monitor (ECR), Printer (IMP), Phone (TEL)
Network Equipment: Server (SRV), Router (ROU), Generic (NET)
Peripherals: Headset (MC), Camera (CAM), Charger (CC, CP, CSA), Keyboard (MDE), Air Conditioner (CLI), Earpods (EAR), Iron (FER), Mouse (SOURIS), Docking Station (SA), Phone Stand (SUP), Bike (VEL)


ID Persistence: IDs remain fixed after creation unless the technical group changes.
Error Handling: If the technical group is missing or invalid, an error message is displayed, and logs are recorded for debugging.

Examples

A Computer in the "Information Technology" group: PC-IT-001
A Monitor in the "Support Team" group: ECR-ST-001
A Camera in the "Maintenance" group: CAM-MA-001
A Server (Network Equipment) in the "IT" group: SRV-IT-001

Troubleshooting

No ID Generated:
Check that the asset has a valid Technical Group Responsible assigned.
Review GLPI logs for errors (e.g., /path/to/glpi/files/_log/).


Incorrect ID Format:
Verify the id_format setting in the glpi_plugin_idgenerator_configs table.
Ensure the group name is valid and contains at least one or two words for abbreviation.


Database Errors:
Confirm that the glpi_plugin_idgenerator_configs table was created during installation.
Check database permissions and GLPI version compatibility.



Uninstallation

Navigate to Setup > Plugins in GLPI.
Locate ID Generator, click Disable, then Uninstall.
The plugin removes its configuration table (glpi_plugin_idgenerator_configs) during uninstallation.

Support

Issues: Report bugs or issues at [https://votre-site.com/issues](https://github.com/zied-Kt-ata/id-genrator/issues).
Documentation: Full documentation is available at [https://votre-site.com/readme/idgenerator](https://github.com/zied-Kt-ata/id-genrator?tab=readme-ov-file#readme).
Contact: Reach out to the author, ZIED KTATA, via the plugin’s homepage.

License
This plugin is licensed under GPLv2+.
Version

Current Version: 1.1.4
Compatibility: GLPI 10.0+

Screenshots

Configuration Page
Example ID Generation

Contributing
Contributions are welcome! Please submit pull requests or issues via the issue tracker.
