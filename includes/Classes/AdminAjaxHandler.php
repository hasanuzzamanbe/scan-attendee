<?php

namespace scanAttendee\Classes;

class AdminAjaxHandler
{
    public function registerEndpoints()
    {
        add_action('wp_ajax_scan_attendee_admin_ajax', array($this, 'handleEndPoint'));
    }
    public function handleEndPoint()
    {
        $route = sanitize_text_field($_REQUEST['route']);

        $validRoutes = array(
            'get_attendee' => 'getAttendee',
            'update_attendee' => 'updateAttendee',
            'add_note' => 'addNote',
        );

        if (isset($validRoutes[$route])) {
            do_action('scan-attendee/doing_ajax_forms_' . $route);
            return $this->{$validRoutes[$route]}();
        }
        do_action('scan-attendee/admin_ajax_handler_catch', $route);
    }

    public function addNote()
    {
        if (!isset($_REQUEST['attendee_id']) || !isset($_REQUEST['note'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);
        $note = sanitize_text_field($_REQUEST['note']);

        $res = (new AttendeeModel())->addNoteToAttendee($attendeeId, $note);
        
        if (!$res) {
            wp_send_json_error('No updated!', 400);
        }

        wp_send_json_success(
            array(
                'message' => 'Note added!',
            )
        );
    }

    public function updateAttendee()
    {
        if (!isset($_REQUEST['type']) || !isset($_REQUEST['attendee_id']) || !isset($_REQUEST['value'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $type = sanitize_text_field($_REQUEST['type']);
        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);
        $value = sanitize_text_field($_REQUEST['value']);

        $res = (new AttendeeModel())->update($attendeeId, $type, $value);
        
        if (!$res) {
            wp_send_json_error('No updated!', 400);
        }

        $stat = $value == 'yes' ? 'Done' : 'Not Done';

        wp_send_json_success(
            array(
                'message' => 'Updated! ' . $type . ' as ' . $stat,
            )
        );
    }

    protected function getAttendee()
    {
        if (!isset($_REQUEST['attendee_id'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);
        $attendee = (new AttendeeModel())->get($attendeeId);

        if (!$attendee->id) {
            wp_send_json_error('Attendee not found!', 400);
        }

        wp_send_json_success(
            array(
                'attendee' => $attendee
            )
        );
    }
}
