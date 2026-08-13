<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_activitynav_upgrade($oldversion) {
    if ($oldversion < 2026061500) {
        upgrade_plugin_savepoint(true, 2026061500, 'local', 'activitynav');
    }
    return true;
}
