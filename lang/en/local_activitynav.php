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
 * Language strings for Activity Navigation plugin.
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Activity Navigation';
$string['privacy:metadata'] = 'The Activity Navigation plugin does not store any personal data.';

// Navigation labels.
$string['previous'] = 'Previous';
$string['next'] = 'Next';
$string['previousactivity'] = 'Previous Activity';
$string['nextactivity'] = 'Next Activity';
$string['backtocourse'] = 'Back to Course';

// Completion messages.
$string['completionrequired'] = 'Complete this activity to unlock the next one';
$string['completioncompleted'] = 'Activity completed';

// Settings.
$string['enabled'] = 'Enable Activity Navigation';
$string['enabled_desc'] = 'Show next/previous navigation arrows on activity pages';
$string['requirecompletion'] = 'Require completion for next';
$string['requirecompletion_desc'] = 'Only show the Next arrow when the current activity completion is met';
$string['showprevious'] = 'Show Previous arrow';
$string['showprevious_desc'] = 'Show the Previous arrow to navigate to the previous activity';
$string['shownext'] = 'Show Next arrow';
$string['shownext_desc'] = 'Show the Next arrow to navigate to the next activity (respects completion if enabled)';
$string['showbacktocourse'] = 'Show Back to Course link';
$string['showbacktocourse_desc'] = 'Show a link to return to the course page';
$string['position'] = 'Navigation position';
$string['position_desc'] = 'Where to display the navigation on the activity page';
$string['position_bottom'] = 'Bottom of activity';
$string['position_top'] = 'Top of activity';
$string['position_both'] = 'Both top and bottom';

// API Credentials
$string['apicredentials'] = 'API Credentials';
$string['apicredentials_desc'] = 'Enter your AI Grader credentials to enable plugin unlock verification. These credentials are available from your AI Grader dashboard at lms-labs.com.';
$string['siteid'] = 'Site ID';
$string['siteid_desc'] = 'Your unique Site ID from the AI Grader dashboard.';
$string['apikey'] = 'API Key';
$string['apikey_desc'] = 'Your API Key from the AI Grader dashboard.';
$string['centralconfig_fallback'] = '(Fallback - Central Config takes priority if installed)';
$string['generalsettings'] = 'General Settings';
$string['activitynav:view'] = 'View activity navigation';
