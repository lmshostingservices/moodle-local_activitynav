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
 * Hook callback for before_footer_html_generation.
 * This is used in Moodle 5.0+ which uses the new hook system.
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_activitynav\hook;

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks for Activity Navigation plugin.
 */
class before_footer_html_generation {
    /**
     * Callback for core\hook\output\before_footer_html_generation hook.
     * Injects activity navigation into the page.
     *
     * @param \core\hook\output\before_footer_html_generation $hook The hook object.
     */
    public static function callback(\core\hook\output\before_footer_html_generation $hook): void {
        // Use the shared implementation from lib.php
        local_activitynav_inject_navigation();
    }
}
