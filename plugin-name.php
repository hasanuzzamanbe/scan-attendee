<?php

/*
Plugin Name: scan-attendee
Plugin URI: #
Description: A WordPress boilerplate plugin with Vue js.
Version: 1.0.0
Author: #
Author URI: #
License: A "Slug" license name e.g. GPL2
Text Domain: textdomain
*/


/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 *
 * Copyright 2019 Plugin Name LLC. All rights reserved.
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!defined('SCANATTENDEE_VERSION')) {
    define('SCANATTENDEE_VERSION_LITE', true);
    define('SCANATTENDEE_VERSION', '1.0.0');
    define('SCANATTENDEE_MAIN_FILE', __FILE__);
    define('SCANATTENDEE_URL', plugin_dir_url(__FILE__));
    define('SCANATTENDEE_DIR', plugin_dir_path(__FILE__));
    define('SCANATTENDEE_UPLOAD_DIR', '/scan-attendee');

    class scanAttendee
    {
        public function boot()
        {
            if (is_admin()) {
                $this->adminHooks();
            }
        }

        public function adminHooks()
        {
            require SCANATTENDEE_DIR . 'includes/autoload.php';

            //Register Admin menu
            $menu = new \scanAttendee\Classes\Menu();
            $menu->register();

            // Top Level Ajax Handlers
            $ajaxHandler = new \scanAttendee\Classes\AdminAjaxHandler();
            $ajaxHandler->registerEndpoints();

            add_action('scan-attendee/render_admin_app', function () {
                $adminApp = new \scanAttendee\Classes\AdminApp();
                $adminApp->bootView();
            });
        }

        public function textDomain()
        {
            load_plugin_textdomain('scan-attendee', false, basename(dirname(__FILE__)) . '/languages');
        }
    }

    add_action('plugins_loaded', function () {
        (new scanAttendee())->boot();
    });

    register_activation_hook(__FILE__, function ($newWorkWide) {
        require_once(SCANATTENDEE_DIR . 'includes/Classes/Activator.php');
        $activator = new \scanAttendee\Classes\Activator();
        $activator->migrateDatabases($newWorkWide);
    });

    // disabled admin-notice on dashboard
    add_action('admin_init', function () {
        $disablePages = [
            'scan-attendee.php',
        ];
        if (isset($_GET['page']) && in_array($_GET['page'], $disablePages)) {
            remove_all_actions('admin_notices');
        }
    });
} else {
    add_action('admin_init', function () {
        deactivate_plugins(plugin_basename(__FILE__));
    });
}
