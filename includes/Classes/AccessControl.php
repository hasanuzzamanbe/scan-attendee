<?php

namespace scanAttendee\Classes;

class AccessControl
{
    public static function hasTopLevelMenuPermission()
    {
        $menuPermissions = array(
            'manage_options',
            'scan-attendee_full_access',
            'scan-attendee_can_view_menus'
        );
        foreach ($menuPermissions as $menuPermission) {
            if (current_user_can($menuPermission)) {
                return $menuPermission;
            }
        }
        return false;
    }

    public static function giveCustomAccess()
    {
        $customRoles = get_option('_scan_attendee_permission');

        if (is_string($customRoles)) {
            $customRoles = [];
        }

        if (!$customRoles) {
            return array(
                'has_access' => false,
                'role'  => ''
            );
        }

        $hasAccess = false;
        $menuPermission = "";

        foreach ($customRoles as $roleName) {
            if (current_user_can($roleName)) {
                return array(
                    'has_access' => true,
                    'role'  => $roleName
                );
            }
        }

        return array(
            'has_access' => $hasAccess,
            'role'  => $menuPermission
        );
    }

    public function setAccessRoles($request)
    {
        if (current_user_can('manage_options')) {
            $capability = isset($request['capability']) ? $request['capability'] : [];
            update_option('_scan_attendee_permission', $capability, 'no');
            return array(
                'message' => __('Successfully updated the role(s).', 'textdomain')
            );
        } else {
            throw new \Exception(__('Sorry, You can not update permissions. Only administrators can update permissions', 'textdomain'));
        }
    }

    public function getAccessRoles()
    {
        if (!current_user_can('manage_options')) {
            return array(
                'capability' => array(),
                'roles' => array()
            );
        }

        if (!function_exists('get_editable_roles')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        $roles = \get_editable_roles();

        $formatted = array();
        foreach ($roles as $key => $role) {
            if ($key == 'administrator') {
                continue;
            }
            if ($key != 'subscriber') {
                $formatted[] = array(
                    'name' => $role['name'],
                    'key' => $key
                );
            }
        }

        $capability = get_option('_scan_attendee_permission');

        if (is_string($capability)) {
            $capability = [];
        }
        return array(
            'capability' => $capability,
            'roles' => $formatted
        );
    }
}
