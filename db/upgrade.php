<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for local_padmap.
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_padmap_upgrade(int $oldversion): bool
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025090701) {
        // Παραδείγματα αλλαγών schema στο μέλλον.

        upgrade_plugin_savepoint(true, 2025090701, 'local', 'padmap');
    }

    return true;
}
