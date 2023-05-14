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

    public function searchBy($eventId, $searchQuery)
    {
        //search from data using $searchQuery
        global $wpdb;
        $table_name = $wpdb->prefix . 'speakers';
        $sql = "SELECT * FROM $table_name WHERE event_id = " . $eventId . " WHERE name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%' OR phone LIKE '%$searchQuery%' OR username LIKE '%$searchQuery%' OR social LIKE '%$searchQuery%' OR type LIKE '%$searchQuery%' OR topic LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR cospeakers LIKE '%$searchQuery%' OR audience LIKE '%$searchQuery%' OR experience LIKE '%$searchQuery%' OR question LIKE '%$searchQuery%' OR consent LIKE '%$searchQuery%' OR ip LIKE '%$searchQuery%' Limit 10";

        $results = $wpdb->get_results($sql);

        $suggestion = array();
        foreach ($results as $key => $value) {
            $suggestion[] = array(
                'label' => $value->topic,
                'value' => $value->topic
            );
        }

        return $suggestion;
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
