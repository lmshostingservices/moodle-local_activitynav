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
 * Version information for Activity Navigation plugin.
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_activitynav';
$plugin->version   = 2026061500;  // 2026-06-15, v1.5.0
$plugin->requires  = 2022041900;
$plugin->supported  = [400, 500];  // Moodle 4.0 to 5.x
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.5.3';
// v1.5.0: UX — reduce confusion between plugin nav and in-activity navigation (e.g. Book chapters).
// Two changes:
// 1. Button labels changed from "Previous"/"Next" to "Previous activity"/"Next activity" — unambiguous
//    even when a Book or similar activity has its own Prev/Next chapter buttons below.
//    Uses the existing (but previously unused) lang strings previousactivity/nextactivity.
// 2. Thin grey separator line (border-bottom: 1px solid #e5e7eb) added below the button bar,
//    with matching dark-mode colour (#334155), visually disconnecting the plugin nav bar
//    from the activity content that follows.
