<?php

namespace scanAttendee\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class GameScoreModel
{
    public function addScore($email,$attendeeId, $score)
    {
        // find attendee if not exist then add
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_game_score';

        $attendee = $wpdb->get_row("SELECT * FROM $table_name WHERE email = '$email'");

        if ($attendee) {
            $oldScore = $attendee->score;
            if($oldScore > $score){
                return array(
                    'score' => $oldScore,
                    'message' => 'Your Previous Score Is greater than your current score',
                );
            }else{
                $data = array(
                    'score' => $score,
                    'updated_at' => current_time('mysql'),
                );
                $where = array(
                    'email' => $email,
                );
                $wpdb->update($table_name, $data, $where);
                return array(
                    'score' => $score,
                    'message' => 'Your new score has been published',
                );
            }

        } else {
            // insert attendee

            $wpdb->insert(
                $table_name,
                array(
                    'attendee_id' => $attendeeId,
                    'email' => $email,
                    'score' => $score,
                    'updated_at' => current_time('mysql'),
                )
            );
            
            return array(
                'score' => $score,
                'message' => 'Score Has Been Submitted',
            );
        };
    }

    public function get($attendeeId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scan_attendee_list';
        $sql = "SELECT * FROM $table_name WHERE attendee_id = " . $attendeeId;

        $result = $wpdb->get_row($sql);

        if ($result->email) {
            $result->gravatar = get_avatar_url(sanitize_email($result->email));
        } else {
            $result->gravatar = 'http://www.gravatar.com/avatar';
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

        $sql = "SELECT * FROM $table_name WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR attendee_id LIKE '%$search%' OR email LIKE '%$search%' OR ticket_type LIKE '%$search%' LIMIT $offset, " . $pagination['per_page'];

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
        
        // count has_giftbox = yes as checked_in and has_tshirt = yes as has_tshirt
        $sql = "SELECT COUNT(*) as total,
            SUM(CASE WHEN has_giftbox = 'yes' THEN 1 ELSE 0 END) as has_giftbox, 
            SUM(CASE WHEN has_tshirt = 'yes' THEN 1 ELSE 0 END) as has_tshirt,
            SUM(CASE WHEN has_swag = 'yes' THEN 1 ELSE 0 END) as has_swag
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
            'updated_at' => current_time('mysql'),
        );

        $where = array(
            'attendee_id' => $attendeeId,
        );

        return $wpdb->update($table_name, $data, $where);
    }

    public function addEmailToAttendee($attendeeId, $email)
    {
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_list';

        $data = array(
            'email' => $email,
            'update_by' => $current_user->ID,
            'updated_at'     => current_time('mysql')
        );

        $where = array(
            'attendee_id' => $attendeeId,
        );

        return $wpdb->update($table_name, $data, $where);
    }

    public function addNameAttendee($attendeeId, $firstName)
    {
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_list';

        $data = array(
            'first_name' => $firstName,
            'update_by' => $current_user->ID,
            'updated_at'     => current_time('mysql')
        );

        $where = array(
            'attendee_id' => $attendeeId,
        );

        return $wpdb->update($table_name, $data, $where);
    }
}
