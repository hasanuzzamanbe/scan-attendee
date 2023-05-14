<?php

namespace scanAttendee\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class AttendeeModel
{

    public function get($attendeeId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scan_attendee_list';
        $sql = "SELECT * FROM $table_name WHERE attendee_id = " . $attendeeId;

        $result = $wpdb->get_row($sql);

        if ($result->email) {
            $result->gravatar = get_avatar_url(sanitize_email($result->email));
        }

        if ($result->update_by) {
            $result->update_by_agent = get_user_by('id', $result->update_by)->display_name;
        }

        return $result;
    }

    public function getInfo()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scan_attendee_list';
        
        // count checkin = yes as checked_in and breakfast = yes as has_breakfast
        $sql = "SELECT COUNT(*) as total,
            SUM(CASE WHEN checkin = 'yes' THEN 1 ELSE 0 END) as checked_in, 
            SUM(CASE WHEN breakfast = 'yes' THEN 1 ELSE 0 END) as has_breakfast,
            SUM(CASE WHEN lunch = 'yes' THEN 1 ELSE 0 END) as has_lunch
            FROM $table_name";

        $result = $wpdb->get_row($sql);

        return $result;
    }

    public function update($attendeeId, $type, $value)
    {
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_list';

        $data = array(
            $type => $value,
            'update_by' => $current_user->ID,
        );

        $where = array(
            'attendee_id' => $attendeeId,
        );

        return $wpdb->update($table_name, $data, $where);
    }

    public function addNoteToAttendee($attendeeId, $note)
    {
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_list';

        $data = array(
            'comment' => $note,
            'update_by' => $current_user->ID,
        );

        $where = array(
            'attendee_id' => $attendeeId,
        );

        return $wpdb->update($table_name, $data, $where);
    }
}
