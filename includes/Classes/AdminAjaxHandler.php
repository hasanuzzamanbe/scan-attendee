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
            'add_attendee' => 'addAttendee',
            'update_attendee' => 'updateAttendee',
            'add_email' => 'addEmail',
            'add_name' => 'addName',
            'get_info' => 'getInfo',
            'get_attendees' => 'getAttendees',
            'upload_csv' => 'uploadCsv',
            'get_permissions' => 'getPermissions',
            'update_permissions' => 'updatePermissions',
        );

        if (isset($validRoutes[$route])) {
            do_action('scan-attendee/doing_ajax_forms_' . $route);
            return $this->{$validRoutes[$route]}();
        }
        do_action('scan-attendee/admin_ajax_handler_catch', $route);
    }

    public function updatePermissions()
    {
        $res = (new AccessControl())->setAccessRoles($_REQUEST);

        wp_send_json_success(
            $res
        );
    }


    public function getPermissions()
    {
        $roles = (new AccessControl())->getAccessRoles();

        wp_send_json_success(
            array('roles' => $roles)
        );
    }

    public function addAttendee()
    {
        if (!isset($_REQUEST['attendee_id'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);

        $res = (new AttendeeModel())->addAttendee($attendeeId);

        if ($res['found']) {
            wp_send_json_error(
                array(
                    'message' => 'Attendee already collected swags!',
                    'attendee' => $res['attendee']
                ),
                400
            );
        }

        wp_send_json_success(
            $res
        );
    }

    public function getAttendees()
    {
        $attendees = (new AttendeeModel())->getAttendees();

        wp_send_json_success(
            $attendees
        );
    }

    public function uploadCsv()
    {
        dd('hh');
        return (new ImportData())->import();
    }


    public function getInfo()
    {
        $info = (new AttendeeModel())->getInfo();

        wp_send_json_success(
            array(
                'info' => $info
            )
        );
    }

    public function addEmail()
    {
        if (!isset($_REQUEST['attendee_id']) || !isset($_REQUEST['email'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);
        $email = sanitize_email($_REQUEST['email']);

        $res = (new AttendeeModel())->addEmailToAttendee($attendeeId, $email);

        if (!$res) {
            wp_send_json_error('No updated!', 400);
        }

        wp_send_json_success(
            array(
                'message' => 'Email added!',
            )
        );
    }

    public function addName()
    {
        if (!isset($_REQUEST['attendee_id']) || !isset($_REQUEST['first_name'])) {
            wp_send_json_error(array(
                'message' => __('Please input attendee id!', 'textdomain'),
            ));
        };

        $attendeeId = sanitize_text_field($_REQUEST['attendee_id']);
        $firstName = sanitize_text_field($_REQUEST['first_name']);

        $res = (new AttendeeModel())->addNameAttendee($attendeeId, $firstName);

        if (!$res) {
            wp_send_json_error('No updated!', 400);
        }

        wp_send_json_success(
            array(
                'message' => 'Name added!',
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
