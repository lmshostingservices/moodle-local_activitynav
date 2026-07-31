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
 * Settings for Activity Navigation plugin.
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_activitynav', get_string('pluginname', 'local_activitynav'));

    // Check if Central Config plugin is installed (provides site-wide credentials)
    $centralconfiginstalled = file_exists($CFG->dirroot . '/local/aiconfig/version.php');
    
    // API Credentials heading
    $settings->add(new admin_setting_heading(
        'local_activitynav/apicredentials',
        get_string('apicredentials', 'local_activitynav'),
        get_string('apicredentials_desc', 'local_activitynav')
    ));
    
    // Site ID (fallback if Central Config not installed)
    $settings->add(new admin_setting_configtext(
        'local_activitynav/siteid',
        get_string('siteid', 'local_activitynav'),
        get_string('siteid_desc', 'local_activitynav') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'local_activitynav') : ''),
        '',
        PARAM_TEXT
    ));
    
    // API Key (fallback if Central Config not installed)
    $settings->add(new admin_setting_configpasswordunmask(
        'local_activitynav/apikey',
        get_string('apikey', 'local_activitynav'),
        get_string('apikey_desc', 'local_activitynav') . ($centralconfiginstalled ? ' ' . get_string('centralconfig_fallback', 'local_activitynav') : ''),
        ''
    ));
    
    // General Settings heading
    $settings->add(new admin_setting_heading(
        'local_activitynav/generalsettings',
        get_string('generalsettings', 'local_activitynav'),
        ''
    ));

    // Enable/disable.
    $settings->add(new admin_setting_configcheckbox(
        'local_activitynav/enabled',
        get_string('enabled', 'local_activitynav'),
        get_string('enabled_desc', 'local_activitynav'),
        1
    ));

    // Require completion for next.
    $settings->add(new admin_setting_configcheckbox(
        'local_activitynav/requirecompletion',
        get_string('requirecompletion', 'local_activitynav'),
        get_string('requirecompletion_desc', 'local_activitynav'),
        1
    ));

    // Show previous arrow.
    $settings->add(new admin_setting_configcheckbox(
        'local_activitynav/showprevious',
        get_string('showprevious', 'local_activitynav'),
        get_string('showprevious_desc', 'local_activitynav'),
        1
    ));

    // Show next arrow.
    $settings->add(new admin_setting_configcheckbox(
        'local_activitynav/shownext',
        get_string('shownext', 'local_activitynav'),
        get_string('shownext_desc', 'local_activitynav'),
        1
    ));

    $ADMIN->add('localplugins', $settings);
}
