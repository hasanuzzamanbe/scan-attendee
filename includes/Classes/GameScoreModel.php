<?php

namespace scanAttendee\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class GameScoreModel
{
    public function addScore($email,$attendeeId,$name, $score)
    {
        // find attendee if not exist then add
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_game_score';

        $attendee = $wpdb->get_row("SELECT * FROM $table_name WHERE attendee_id = '$attendeeId'");

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
                    'attendee_id' => $attendeeId,
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
                    'name' => $name,
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

    public function validateAttendeeId($attendeeId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scan_attendee_game_score';
        $sql = "SELECT * FROM $table_name WHERE attendee_id = " . $attendeeId;

        $result = $wpdb->get_row($sql);
        return !$result;
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
