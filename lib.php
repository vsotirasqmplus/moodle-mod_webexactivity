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
 * An activity to interface with WebEx.
 *
 * @package   mod_webexactvity
 * @copyright Eric Merrill (merrill@oakland.edu)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function webexactivity_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_ASSIGNMENT;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_GROUPMEMBERSONLY:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return false;

        default:
            return null;
    }
}

function webexactivity_add_instance($data, $mform) {

    $meeting = \mod_webexactivity\webex::new_meeting(\mod_webexactivity\webex::WEBEXACTIVITY_TYPE_TRAINING);
    $meeting->starttime = $data->starttime;
    $meeting->duration = $data->duration;
    $meeting->intro = $data->intro;
    $meeting->introformat = $data->introformat;
    $meeting->name = $data->name;
    $meeting->course = $data->course;
    $meeting->status = \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_NEVER_STARTED;
    if (isset($data->studentdownload) && $data->studentdownload) {
        $meeting->studentdownload = 1;
    } else {
        $meeting->studentdownload = 0;
    }

    if ($meeting->save()) {
        return $meeting->id;
    }

    return false;
}

function webexactivity_update_instance($data, $mform) {
    $cmid = $data->coursemodule;
    $cm = get_coursemodule_from_id('webexactivity', $cmid, 0, false, MUST_EXIST);
    $meeting = \mod_webexactivity\webex::load_meeting($cm->instance);

    $meeting->starttime = $data->starttime;
    $meeting->duration = $data->duration;
    $meeting->intro = $data->intro;
    $meeting->introformat = $data->introformat;
    $meeting->name = $data->name;
    $meeting->course = $data->course;

    if (isset($data->studentdownload) && $data->studentdownload) {
        $meeting->studentdownload = 1;
    } else {
        $meeting->studentdownload = 0;
    }

    return $meeting->save();
}

function webexactivity_delete_instance($id) {
    $meeting = \mod_webexactivity\webex::load_meeting($id);
    return $meeting->delete();
}

function webexactivity_cron() {
    $webex = new \mod_webexactivity\webex();
    $webex->get_recordings();
    $webex->get_open_sessions();
    $webex->remove_deleted_recordings();

    return true;
}

function webexactivity_get_recordings() {

}
