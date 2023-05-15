<?php

namespace scanAttendee\Classes;

class Menu
{
    public function register()
    {
        add_action('admin_menu', array($this, 'addMenus'));
    }

    public function addMenus()
    {
        $menuPermission = AccessControl::hasTopLevelMenuPermission();
        if (!$menuPermission) {
            return;
        }

        $title = __('scan attendee', 'textdomain');
        global $submenu;
        add_menu_page(
            $title,
            $title,
            $menuPermission,
            'scan-attendee.php',
            array($this, 'enqueueAssets'),
            'dashicons-admin-site',
            25
        );

        $submenu['scan-attendee.php']['scan'] = array(
            __('Scan Attendee', 'textdomain'),
            $menuPermission,
            'admin.php?page=scan-attendee.php#/',
        );
        // $submenu['scan-attendee.php']['settings'] = array(
        //     __('Settings', 'textdomain'),
        //     $menuPermission,
        //     'admin.php?page=scan-attendee.php#/settings',
        // );
        $submenu['scan-attendee.php']['attendees'] = array(
            __('Attendees', 'textdomain'),
            $menuPermission,
            'admin.php?page=scan-attendee.php#/attendees',
        );
    }

    public function enqueueAssets()
    {
        do_action('scan-attendee/render_admin_app');
        wp_enqueue_script(
            'scan-attendee_boot',
            SCANATTENDEE_URL . 'assets/js/boot.js',
            array('jquery'),
            SCANATTENDEE_VERSION,
            true
        );

        // 3rd party developers can now add their scripts here
        do_action('scan-attendee/booting_admin_app');
        wp_enqueue_script(
            'scan-attendee_js',
            SCANATTENDEE_URL . 'assets/js/plugin-main-js-file.js',
            array('scan-attendee_boot'),
            SCANATTENDEE_VERSION,
            true
        );

        //enque css file
        wp_enqueue_style('scan-attendee_admin_css', SCANATTENDEE_URL . 'assets/css/element.css');

        $scanAttendeeAdminVars = apply_filters('scan-attendee/admin_app_vars', array(
            //'image_upload_url' => admin_url('admin-ajax.php?action=wpf_global_settings_handler&route=wpf_upload_image'),
            'assets_url' => SCANATTENDEE_URL . 'assets/',
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        wp_localize_script('scan-attendee_boot', 'scanAttendeeAdmin', $scanAttendeeAdminVars);
    }
}
