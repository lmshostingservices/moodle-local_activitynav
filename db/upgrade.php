<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade steps for Activity Navigation.
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_activitynav_upgrade($oldversion) {

    // v1.4.9: FORMAT FIX — numeric version corrected from 12-digit (202603271103) to
    //   13-digit YYYYMMDD00XXX format (2026041000149). No DB schema changes.
    if ($oldversion < 2026041000149) {
        upgrade_plugin_savepoint(true, 2026041000149, 'local', 'activitynav');
    }

    // v1.4.10: ZIP FIX — Rebuilt ZIP with correct local_activitynav/ wrapper folder.
    //   Previous ZIP was missing the wrapping directory causing Moodle to reject
    //   the install with "version.php not found after extraction". No code changes.
    //   No DB schema changes. version.php → 2026041100150.
    if ($oldversion < 2026041100150) {
        upgrade_plugin_savepoint(true, 2026041100150, 'local', 'activitynav');
    }

    if ($oldversion < 2026061500002) {
        // FIX-API-DOMAIN: Reverted API endpoint to lms-labs.com (correct domain).
        // essaygraderai.app was the original single-plugin domain; lms-labs.com is correct.
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['version.php', 'db/upgrade.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) { opcache_invalidate($_full, true); }
            }
        } elseif (function_exists('opcache_reset')) { opcache_reset(); }
        upgrade_plugin_savepoint(true, 2026061500002, 'local', 'activitynav');
    }

    if ($oldversion < 2026061500003) {
        // Domain update: lms-labs.com → lms-labs.com
        if (function_exists('opcache_invalidate')) {
            $_pluginDir = realpath(__DIR__ . '/..');
            foreach (['version.php', 'lib.php', 'db/upgrade.php'] as $_f) {
                $_full = $_pluginDir . '/' . $_f;
                if (file_exists($_full)) { opcache_invalidate($_full, true); }
            }
        } elseif (function_exists('opcache_reset')) { opcache_reset(); }
        upgrade_plugin_savepoint(true, 2026061500003, 'local', 'activitynav');
    }

    return true;
}