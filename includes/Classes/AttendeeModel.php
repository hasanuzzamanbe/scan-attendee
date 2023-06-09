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

    public function getAttendees()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scan_attendee_list';

        $pagination =  [
            'current_page' =>  1,
            'per_page' => 20,
            'page_number' =>  1,
            'total' => 0
        ];

        $search = $_REQUEST['search'] ? $_REQUEST['search'] : '';

        $pagination = $_REQUEST['pagination'] ? $_REQUEST['pagination'] : $pagination;

        $offset = ($pagination['current_page'] - 1) * $pagination['per_page'];

        $sql = "SELECT * FROM $table_name WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR ticket_type LIKE '%$search%' LIMIT $offset, " . $pagination['per_page'];

        $results = $wpdb->get_results($sql);

        foreach ($results as $result) {
            if ($result->email) {
                $result->gravatar = get_avatar_url(sanitize_email($result->email));
            }

            if ($result->update_by) {
                $result->update_by_agent = get_user_by('id', $result->update_by)->display_name;
            }
        }

        //get total
        $sql = "SELECT COUNT(*) as total FROM $table_name";
        $total = $wpdb->get_row($sql);

        return array(
            'attendees' => $results,
            'total' => $total->total,
        );
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
