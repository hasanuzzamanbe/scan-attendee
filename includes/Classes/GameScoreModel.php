<?php

namespace scanAttendee\Classes;

if (!defined('ABSPATH')) {
    exit;
}

class GameScoreModel
{
    public function addScore($email, $attendeeId, $name, $score)
    {
        // find attendee if not exist then add
        global $wpdb, $current_user;

        $table_name = $wpdb->prefix . 'scan_attendee_game_score';

        $attendee = $wpdb->get_row("SELECT * FROM $table_name WHERE attendee_id = '$attendeeId'");

        if ($attendee) {
            $oldScore = $attendee->score;
            if ($oldScore > $score) {
                return array(
                    'score' => $oldScore,
                    'message' => 'Your Previous Score Is greater than your current score',
                );
            } else {
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

    public function playersCount()
    {
        global $wpdb;
        $gameTable = $wpdb->prefix . 'scan_attendee_game_score';
        $sql = "SELECT COUNT(*) as total FROM $gameTable";
        $playersCount = $wpdb->get_row($sql);
        return $playersCount;
    }
}
