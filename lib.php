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
 * Library functions for Activity Navigation plugin.
 * 
 * Uses client-side JavaScript to read the course index DOM for reliable
 * navigation in courses with subsections, Tiles, or phased layouts.
 * 
 * Supports both Moodle 4.x (legacy callback) and Moodle 5.x (new hook system).
 *
 * @package    local_activitynav
 * @copyright  2025 Essay Grader AI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Legacy callback for Moodle 4.x.
 * In Moodle 5.x, the new hook system in db/hooks.php is used instead.
 * This callback is kept for backward compatibility with Moodle 4.x.
 */
function local_activitynav_before_footer() {
    // Check if we're on Moodle 5.0+ with the new hook system.
    // If so, the hook callback will handle this, so skip the legacy callback.
    if (class_exists('\core\hook\output\before_footer_html_generation')) {
        return;
    }
    
    // Moodle 4.x - use legacy callback.
    local_activitynav_inject_navigation();
}

/**
 * Shared implementation for injecting activity navigation.
 * Called by both the legacy callback (Moodle 4.x) and the new hook (Moodle 5.x).
 */
function local_activitynav_inject_navigation() {
    static $already_injected = false;
    
    // Prevent double injection.
    if ($already_injected) {
        return;
    }
    global $PAGE, $COURSE, $USER;

    // Check if plugin is enabled.
    if (!get_config('local_activitynav', 'enabled')) {
        return;
    }

    // Only show on activity pages (mod context).
    if ($PAGE->context->contextlevel !== CONTEXT_MODULE) {
        return;
    }

    // Get the course module.
    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    // Check user has capability.
    if (!has_capability('local/activitynav:view', $PAGE->context)) {
        return;
    }
    
    // Mark as injected to prevent double injection.
    $already_injected = true;

    // Get settings.
    $showPrevious = get_config('local_activitynav', 'showprevious') ? true : false;
    $showNext = get_config('local_activitynav', 'shownext') ? true : false;
    $requireCompletion = get_config('local_activitynav', 'requirecompletion') ? true : false;

    // Check current activity completion status for "next" visibility.
    $isCompleted = true; // Default to allowing next.
    
    if ($requireCompletion) {
        $completionInfo = new \completion_info($COURSE);
        
        if ($completionInfo->is_enabled($cm)) {
            $completionData = $completionInfo->get_data($cm, true, $USER->id);
            
            // Check if completion is complete (COMPLETION_COMPLETE or COMPLETION_COMPLETE_PASS).
            if ($completionData->completionstate == COMPLETION_INCOMPLETE || 
                $completionData->completionstate == COMPLETION_COMPLETE_FAIL) {
                $isCompleted = false;
            }
        }
    }

    // Get strings for JavaScript.
    $strings = array(
        'previous' => get_string('previousactivity', 'local_activitynav'),
        'next' => get_string('nextactivity', 'local_activitynav'),
        'completionrequired' => get_string('completionrequired', 'local_activitynav'),
    );

    // Include CSS - use different methods depending on when we're called.
    // In Moodle 5 with hook system, <head> is already printed, so we inject inline styles.
    // In Moodle 4 with legacy callback, we can use the normal CSS include.
    $useinlinecss = class_exists('\core\hook\output\before_footer_html_generation');
    
    if (!$useinlinecss) {
        // Moodle 4.x - can use normal CSS include.
        $PAGE->requires->css('/local/activitynav/styles.css');
    }

    // Inject JavaScript that reads the course index DOM for navigation.
    // This approach works reliably with subsections, Tiles, and phased courses.
    $PAGE->requires->js_amd_inline("
        require(['jquery'], function ($) {
            'use strict';
            
            /**
             * Detect Moodle theme primary color from DOM.
             * Works with Moodle 4.x and 5.x themes.
             */
            function detectThemeColor() {
                // Try common Moodle theme elements to detect primary color.
                var selectors = [
                    '.navbar',
                    '.btn-primary',
                    '.bg-primary',
                    '#page-header',
                    '.primary-navigation'
                ];
                
                for (var i = 0; i < selectors.length; i++) {
                    var el = document.querySelector(selectors[i]);
                    if (el) {
                        var style = window.getComputedStyle(el);
                        var bg = style.backgroundColor;
                        // Check if it's a valid color (not transparent or white).
                        if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent' && bg !== 'rgb(255, 255, 255)') {
                            return bg;
                        }
                    }
                }
                
                // Fallback to blue.
                return '#3b82f6';
            }
            
            // Inject CSS inline (works for both Moodle 4 and 5 hook timing).
            if (!document.getElementById('activitynav-inline-styles')) {
                var themeColor = detectThemeColor();
                var css = '.activitynav-topright{position:relative;display:flex;justify-content:flex-end;border-bottom:1px solid #e5e7eb;margin-bottom:1.5rem;padding:0 0 .875rem}.activitynav-container{display:inline-flex;align-items:center;gap:.5rem}.activitynav-inner{display:flex;align-items:center;gap:.5rem}.activitynav-btn{display:inline-flex;align-items:center;gap:.375rem;padding:.5rem .875rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none!important;color:#475569;font-size:.8125rem;font-weight:500;transition:all .15s ease;white-space:nowrap}.activitynav-btn:hover{background:#fff;border-color:' + themeColor + ';color:' + themeColor + ';box-shadow:0 2px 8px rgba(0,0,0,.1);text-decoration:none!important}.activitynav-prev{padding-left:.625rem}.activitynav-next{padding-right:.625rem}.activitynav-course,.activitynav-spacer{display:none}.activitynav-icon{width:16px;height:16px;flex-shrink:0}.activitynav-label{display:flex;align-items:center}.activitynav-direction{font-size:.8125rem;font-weight:500}.activitynav-name{display:none}.activitynav-locked{background:#f8fafc;border-color:#e2e8f0;color:#94a3b8;cursor:not-allowed;opacity:.7}.activitynav-locked:hover{background:#f8fafc;border-color:#e2e8f0;box-shadow:none;color:#94a3b8}.activitynav-locked .activitynav-icon,.activitynav-lock{color:#94a3b8}@media(prefers-color-scheme:dark){.activitynav-topright{border-bottom-color:#334155}.activitynav-btn{background:#1e293b;border-color:#334155;color:#cbd5e1}.activitynav-btn:hover{background:#334155;border-color:' + themeColor + ';color:' + themeColor + '}.activitynav-locked{background:#1e293b;border-color:#334155;color:#64748b}.activitynav-locked:hover{background:#1e293b;border-color:#334155}.activitynav-locked .activitynav-icon,.activitynav-lock{color:#64748b}}@media(max-width:480px){.activitynav-btn{padding:.375rem .5rem}.activitynav-direction{display:none}.activitynav-icon{width:18px;height:18px}}';
                var style = document.createElement('style');
                style.id = 'activitynav-inline-styles';
                style.textContent = css;
                document.head.appendChild(style);
            }
            
            var config = {
                cmid: " . (int)$cm->id . ",
                showPrevious: " . ($showPrevious ? 'true' : 'false') . ",
                showNext: " . ($showNext ? 'true' : 'false') . ",
                requireCompletion: " . ($requireCompletion ? 'true' : 'false') . ",
                isCompleted: " . ($isCompleted ? 'true' : 'false') . ",
                strings: " . json_encode($strings) . "
            };
            
            /**
             * Find navigation targets from the course index DOM.
             * Works with subsections, Tiles, and standard course formats.
             */
            function findNavTargetsFromIndex(cmid) {
                var result = { prevUrl: null, nextUrl: null, prevName: null, nextName: null };
                
                // The course index contains all activities in order, even with subsections.
                var container = document.getElementById('courseindex-content');
                if (!container) {
                    // Try alternative selector for older Moodle versions.
                    container = document.querySelector('.courseindex, #nav-drawer .list-group');
                }
                
                if (!container) {
                    console.log('ActivityNav: Course index not found, falling back to section-based navigation');
                    return findNavTargetsFromSections(cmid);
                }
                
                // Find all course module items in the index.
                // These have data-for=\"cm\" and data-id=\"<cmid>\".
                var allItems = container.querySelectorAll('.courseindex-item[data-for=\"cm\"][data-id]');
                
                if (allItems.length === 0) {
                    // Try alternative selectors.
                    allItems = container.querySelectorAll('[data-for=\"cm\"][data-id]');
                }
                
                if (allItems.length === 0) {
                    console.log('ActivityNav: No activity items found in course index');
                    return findNavTargetsFromSections(cmid);
                }
                
                // Convert to array and filter out hidden activities for students.
                // Hidden activities have .dimmed, .courseindex-hidden, .hiddenactivity, 
                // or data-visible=\"0\" attribute, or are inside dimmed sections.
                var allItemsArray = Array.prototype.slice.call(allItems);
                var items = [];
                var currentIndex = -1;
                
                for (var i = 0; i < allItemsArray.length; i++) {
                    var item = allItemsArray[i];
                    
                    // Check if this item or its parent is hidden/dimmed.
                    var isHidden = false;
                    
                    // Check item itself for hidden indicators.
                    if (item.classList.contains('dimmed') || 
                        item.classList.contains('dimmed_text') ||
                        item.classList.contains('courseindex-hidden') ||
                        item.classList.contains('hiddenactivity') ||
                        item.classList.contains('activity-item-hidden') ||
                        item.getAttribute('data-visible') === '0') {
                        isHidden = true;
                    }
                    
                    // Check if parent section is hidden.
                    if (!isHidden) {
                        var parentSection = item.closest('.courseindex-section, .section');
                        if (parentSection && (parentSection.classList.contains('dimmed') || 
                            parentSection.classList.contains('hidden') ||
                            parentSection.getAttribute('data-visible') === '0')) {
                            isHidden = true;
                        }
                    }
                    
                    // Check if link inside is dimmed (indicates hidden from student).
                    if (!isHidden) {
                        var link = item.querySelector('a.courseindex-link, a[href*=\"/mod/\"]');
                        if (link && (link.classList.contains('dimmed') || link.classList.contains('dimmed_text'))) {
                            isHidden = true;
                        }
                    }
                    
                    // Skip hidden items but still track current activity position.
                    if (item.getAttribute('data-id') === String(cmid)) {
                        currentIndex = items.length; // Position in filtered array.
                    }
                    
                    // Only add visible items to navigation list.
                    if (!isHidden) {
                        items.push(item);
                    }
                }
                
                if (currentIndex === -1) {
                    console.log('ActivityNav: Current activity not found in index, cmid=' + cmid);
                    return findNavTargetsFromSections(cmid);
                }
                
                // Get previous visible activity.
                if (currentIndex > 0) {
                    var prevItem = items[currentIndex - 1];
                    var prevLink = prevItem.querySelector('a.courseindex-link, a[href*=\"/mod/\"]');
                    if (prevLink) {
                        result.prevUrl = prevLink.href;
                        result.prevName = prevLink.textContent.trim();
                    }
                }
                
                // Get next visible activity.
                if (currentIndex < items.length - 1) {
                    var nextItem = items[currentIndex + 1];
                    var nextLink = nextItem.querySelector('a.courseindex-link, a[href*=\"/mod/\"]');
                    if (nextLink) {
                        result.nextUrl = nextLink.href;
                        result.nextName = nextLink.textContent.trim();
                    }
                }
                
                console.log('ActivityNav: Found nav targets from index', result);
                return result;
            }
            
            /**
             * Fallback: Find navigation targets from section-based DOM.
             * Used when course index is not available.
             * Also filters out hidden activities for students.
             */
            function findNavTargetsFromSections(cmid) {
                var result = { prevUrl: null, nextUrl: null, prevName: null, nextName: null };
                
                // Find all activity links in the page.
                var allActivities = document.querySelectorAll('li.activity[data-id], .activity[id^=\"module-\"]');
                
                if (allActivities.length === 0) {
                    return result;
                }
                
                // Filter out hidden activities and track current position.
                var allItemsArray = Array.prototype.slice.call(allActivities);
                var items = [];
                var currentIndex = -1;
                
                for (var i = 0; i < allItemsArray.length; i++) {
                    var item = allItemsArray[i];
                    
                    // Check if activity is hidden from students.
                    var isHidden = false;
                    
                    // Check for hidden/dimmed classes on the activity.
                    if (item.classList.contains('dimmed') || 
                        item.classList.contains('dimmed_text') ||
                        item.classList.contains('hidden') ||
                        item.classList.contains('hiddenactivity') ||
                        item.getAttribute('data-visible') === '0') {
                        isHidden = true;
                    }
                    
                    // Check if activity link is dimmed.
                    if (!isHidden) {
                        var link = item.querySelector('a.aalink, a.activityname, a[href*=\"/mod/\"]');
                        if (link && (link.classList.contains('dimmed') || link.classList.contains('dimmed_text'))) {
                            isHidden = true;
                        }
                    }
                    
                    // Track current activity position.
                    var itemId = item.getAttribute('data-id') || item.id.replace('module-', '');
                    if (itemId === String(cmid)) {
                        currentIndex = items.length;
                    }
                    
                    // Only add visible activities.
                    if (!isHidden) {
                        items.push(item);
                    }
                }
                
                if (currentIndex === -1) {
                    return result;
                }
                
                // Get previous visible activity.
                if (currentIndex > 0) {
                    var prevItem = items[currentIndex - 1];
                    var prevLink = prevItem.querySelector('a.aalink, a.activityname, a[href*=\"/mod/\"]');
                    if (prevLink) {
                        result.prevUrl = prevLink.href;
                        result.prevName = prevLink.textContent.trim();
                    }
                }
                
                // Get next visible activity.
                if (currentIndex < items.length - 1) {
                    var nextItem = items[currentIndex + 1];
                    var nextLink = nextItem.querySelector('a.aalink, a.activityname, a[href*=\"/mod/\"]');
                    if (nextLink) {
                        result.nextUrl = nextLink.href;
                        result.nextName = nextLink.textContent.trim();
                    }
                }
                
                return result;
            }
            
            /**
             * Build and inject navigation HTML.
             */
            function buildNavigation(navTargets) {
                var html = '<div class=\"activitynav-container\">';
                html += '<div class=\"activitynav-inner\">';
                
                // Previous button.
                if (config.showPrevious && navTargets.prevUrl) {
                    var prevName = navTargets.prevName || config.strings.previous;
                    html += '<a href=\"' + navTargets.prevUrl + '\" class=\"activitynav-btn activitynav-prev\" title=\"' + escapeHtml(prevName) + '\">';
                    html += '<svg class=\"activitynav-icon\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">';
                    html += '<polyline points=\"15 18 9 12 15 6\"></polyline>';
                    html += '</svg>';
                    html += '<span class=\"activitynav-label\">';
                    html += '<span class=\"activitynav-direction\">' + config.strings.previous + '</span>';
                    html += '<span class=\"activitynav-name\">' + escapeHtml(prevName) + '</span>';
                    html += '</span>';
                    html += '</a>';
                } else if (config.showPrevious) {
                    html += '<div class=\"activitynav-spacer\"></div>';
                }
                
                // Next button.
                if (config.showNext && navTargets.nextUrl) {
                    var nextName = navTargets.nextName || config.strings.next;
                    
                    if (config.requireCompletion && !config.isCompleted) {
                        // Show locked state - just Next text with lock icon (clean, no messy text).
                        html += '<div class=\"activitynav-btn activitynav-next activitynav-locked\" title=\"' + escapeHtml(config.strings.completionrequired) + '\">';
                        html += '<span class=\"activitynav-label\">';
                        html += '<span class=\"activitynav-direction\">' + config.strings.next + '</span>';
                        html += '</span>';
                        html += '<svg class=\"activitynav-icon activitynav-lock\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">';
                        html += '<rect x=\"3\" y=\"11\" width=\"18\" height=\"11\" rx=\"2\" ry=\"2\"></rect>';
                        html += '<path d=\"M7 11V7a5 5 0 0 1 10 0v4\"></path>';
                        html += '</svg>';
                        html += '</div>';
                    } else {
                        // Show clickable next.
                        html += '<a href=\"' + navTargets.nextUrl + '\" class=\"activitynav-btn activitynav-next\" title=\"' + escapeHtml(nextName) + '\">';
                        html += '<span class=\"activitynav-label\">';
                        html += '<span class=\"activitynav-direction\">' + config.strings.next + '</span>';
                        html += '<span class=\"activitynav-name\">' + escapeHtml(nextName) + '</span>';
                        html += '</span>';
                        html += '<svg class=\"activitynav-icon\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">';
                        html += '<polyline points=\"9 18 15 12 9 6\"></polyline>';
                        html += '</svg>';
                        html += '</a>';
                    }
                } else if (config.showNext) {
                    html += '<div class=\"activitynav-spacer\"></div>';
                }
                
                html += '</div>';
                html += '</div>';
                
                return html;
            }
            
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            /**
             * Inject navigation into page.
             */
            function injectNavigation() {
                var navTargets = findNavTargetsFromIndex(config.cmid);
                
                // Only show if we have at least one navigation target.
                if (!navTargets.prevUrl && !navTargets.nextUrl) {
                    console.log('ActivityNav: No navigation targets found');
                    return;
                }
                
                var html = buildNavigation(navTargets);
                
                // Find insertion point.
                var \$target = $('#region-main .activity-header, #region-main-box > .card-header, #page-content .page-header-headings').first();
                
                if (\$target.length) {
                    \$target.after('<div class=\"activitynav-topright\">' + html + '</div>');
                } else {
                    $('#region-main').prepend('<div class=\"activitynav-topright\">' + html + '</div>');
                }
            }
            
            /**
             * Unlock the Next button (convert from locked to clickable).
             */
            function unlockNextButton() {
                var lockedBtn = document.querySelector('.activitynav-locked');
                if (!lockedBtn) return;
                
                // Get next URL from stored nav targets.
                var navTargets = findNavTargetsFromIndex(config.cmid);
                if (!navTargets.nextUrl) return;
                
                var nextName = navTargets.nextName || config.strings.next;
                
                // Create new clickable button.
                var newBtn = document.createElement('a');
                newBtn.href = navTargets.nextUrl;
                newBtn.className = 'activitynav-btn activitynav-next';
                newBtn.title = nextName;
                newBtn.innerHTML = '<span class=\"activitynav-label\"><span class=\"activitynav-direction\">' + config.strings.next + '</span></span>' +
                    '<svg class=\"activitynav-icon\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><polyline points=\"9 18 15 12 9 6\"></polyline></svg>';
                
                // Replace locked button with clickable one.
                lockedBtn.parentNode.replaceChild(newBtn, lockedBtn);
                
                console.log('ActivityNav: Next button unlocked');
            }
            
            /**
             * Check if activity is now complete by examining DOM.
             */
            function checkIfCompleted() {
                // Check course index for completion indicator on current activity.
                var courseIndex = document.getElementById('courseindex-content');
                if (courseIndex) {
                    var currentItem = courseIndex.querySelector('[data-for=\"cm\"][data-id=\"' + config.cmid + '\"]');
                    if (currentItem) {
                        // Look for completion checkmark/icon.
                        var completionIcon = currentItem.querySelector('.completioninfo .complete, .text-success, .icon[title*=\"Completed\"], .icon[title*=\"Done\"], svg.text-success');
                        if (completionIcon) return true;
                        
                        // Check for completed class.
                        if (currentItem.classList.contains('activity-complete') || currentItem.classList.contains('completed')) return true;
                    }
                }
                
                // Check page completion indicator.
                var pageCompletion = document.querySelector('.activity-header .badge-success, .activity-header .text-success, .completion-info .complete, [data-region=\"completionrequirements\"] .text-success, .automatic-completion-conditions .text-success');
                if (pageCompletion) return true;
                
                // Check if \"Mark as done\" button is now showing as done.
                var doneBtn = document.querySelector('[data-action=\"toggle-manual-completion\"]');
                if (doneBtn) {
                    var btnText = doneBtn.textContent.toLowerCase();
                    if (btnText.includes('done') && !btnText.includes('mark as done')) return true;
                    // Check data attribute.
                    if (doneBtn.getAttribute('data-completionstate') === '1') return true;
                }
                
                return false;
            }
            
            /**
             * Watch for completion changes and unlock Next button dynamically.
             */
            function watchForCompletion() {
                if (!config.requireCompletion || config.isCompleted) return;
                
                var unlocked = false;
                
                function tryUnlock() {
                    if (unlocked) return;
                    if (checkIfCompleted()) {
                        unlockNextButton();
                        unlocked = true;
                    }
                }
                
                // Watch for any click on completion-related elements.
                $(document).on('click', '[data-action=\"toggle-manual-completion\"], .togglecompletion, .activity-manual-completion, .btn-completion, button[type=\"submit\"]', function () {
                    // Check multiple times after click to catch AJAX updates.
                    setTimeout(tryUnlock, 300);
                    setTimeout(tryUnlock, 700);
                    setTimeout(tryUnlock, 1200);
                    setTimeout(tryUnlock, 2000);
                });
                
                // Watch entire document body for mutations (catches all AJAX updates).
                var bodyObserver = new MutationObserver(function () {
                    tryUnlock();
                });
                
                bodyObserver.observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['class', 'data-completionstate', 'data-value']
                });
                
                // Also poll periodically as fallback.
                var pollCount = 0;
                var pollInterval = setInterval(function () {
                    tryUnlock();
                    pollCount++;
                    if (unlocked || pollCount > 60) { // Stop after 60 seconds.
                        clearInterval(pollInterval);
                        bodyObserver.disconnect();
                    }
                }, 1000);
            }
            
            /**
             * Initialize with retry for dynamically loaded course index.
             */
            function initWithRetry(attempts) {
                if (attempts <= 0) {
                    console.log('ActivityNav: Max retries reached, injecting with available data');
                    injectNavigation();
                    watchForCompletion();
                    return;
                }
                
                var container = document.getElementById('courseindex-content');
                var hasItems = container && container.querySelectorAll('.courseindex-item[data-for=\"cm\"]').length > 0;
                
                if (hasItems) {
                    injectNavigation();
                    watchForCompletion();
                } else {
                    // Wait for course index to load (handles dynamic loading in Tiles/subsections).
                    setTimeout(function () {
                        initWithRetry(attempts - 1);
                    }, 200);
                }
            }
            
            // Initialize when DOM is ready.
            $(document).ready(function () {
                // Give the course index time to load (especially for Tiles/subsections).
                setTimeout(function () {
                    initWithRetry(10); // Try up to 10 times (2 seconds total).
                }, 100);
            });
        });
    ");
}
