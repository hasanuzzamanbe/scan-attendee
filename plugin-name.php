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

use scanAttendee\Classes\GameScoreModel;

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

            if (!is_admin()) {
                $this->initGame();
            }


            add_action('wp_ajax_nopriv_add_game_score', [$this, 'submitForm']);
            add_action('wp_ajax_add_game_score', [$this, 'submitForm']);

        }

        public function submitForm()
        {

            require 'includes/Classes/GameScoreModel.php';
            try {
                $gameScoreModel = new GameScoreModel();
                $score = $this->decryptValue($_POST['score'],'AuthLabAuthLab12');
                $response = $gameScoreModel->addScore($_POST['email'],$_POST['attendee_id'],$score);
                return wp_send_json_success($response);
            }catch (Exception $e){
                return wp_send_json_error([
                    'message' => 'Something Went Wrong'
                ]);
            }
        }


        public function decryptValue($encryptedValue, $secretKey){
            $encryptedData = hex2bin($encryptedValue);

            // Extract the IV from the encrypted value
            $ivSize = openssl_cipher_iv_length('AES-128-CBC');
            $iv = substr($encryptedData, 0, $ivSize);
            $ciphertext = substr($encryptedData, $ivSize);

            // Decrypt the value using AES-CBC algorithm with the secret key and IV
            $decrypted = openssl_decrypt($ciphertext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);

            // Return the decrypted value as a UTF-8 encoded string
            return utf8_encode($decrypted);
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

        public function initGame(){
            add_shortcode(
                'scan-attendee-game-shortcode',
                function(){
                    echo '
                    <div class="parent-1">
                    <div class="parent-2">
                    <div class="game-container" id="game-container">
                <div class="preventer">
                    <h2 class="game_title">
                        Welcome To AuthLab Game!
                    </h2>
                    
                    <div>
                        <input id="attendee-id-input" class="attendee-id-input" placeholder="Attendee Id">
                        <span class="attendee-id-validation" id="attendee-id-validation"></span>
                    </div>
                    
                    <div>
                        <input id="mail-input" class="mail-input" placeholder="Email">
                        <span class="mail-validation" id="mail-validation"></span>
                    </div>
                    
                    <button id="mail-submit-button">
                        Submit
                    </button>
                </div>
                <div class="game-end-button-container" id="game-end-button-container">

                </div>
            </div>
</div>
</div>';
                }
            );

            wp_enqueue_style('scan-attendee_game_css', SCANATTENDEE_URL . 'assets/css/style.css');
            wp_enqueue_style('scan-attendee_game_css', SCANATTENDEE_URL . 'assets/css/style.css');
            wp_enqueue_script(
                'scan-attendee-game-sweet-alert',
                'https://cdn.jsdelivr.net/npm/sweetalert2@11',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_enqueue_script(
                'scan-attendee-game-p5',
                SCANATTENDEE_URL . 'assets/js/game/js/p5.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );





            wp_enqueue_script(
                'scan-attendee-game-constant',
                SCANATTENDEE_URL . 'assets/js/game/js/constant.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_localize_script('scan-attendee-game-constant', 'scanAttendeeGameUrl', SCANATTENDEE_URL);
            wp_localize_script('scan-attendee-game-constant', 'scanAttendeeGameAjaxUrl',  admin_url('admin-ajax.php'));


            wp_enqueue_script(
                'scan-attendee-game-starter',
                SCANATTENDEE_URL . 'assets/js/game/js/starter.js',
                array('jquery'),
                time(),
                true
            );


            wp_enqueue_script(
                'scan-attendee-game-bomb',
                SCANATTENDEE_URL . 'assets/js/game/js/bomb.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );



            wp_enqueue_script(
                'scan-attendee-game-game',
                SCANATTENDEE_URL . 'assets/js/game/js/game.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_enqueue_script(
                'scan-attendee-game-product',
                SCANATTENDEE_URL . 'assets/js/game/js/product.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_enqueue_script(
                'scan-attendee-game-sprite',
                SCANATTENDEE_URL . 'assets/js/game/js/sprite.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );




            //screens
            wp_enqueue_script(
                'scan-attendee-game-screen-game-over-screen',
                SCANATTENDEE_URL . 'assets/js/game/js/screen/gameOverScreen.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_enqueue_script(
                'scan-attendee-game-screen-game-screen',
                SCANATTENDEE_URL . 'assets/js/game/js/screen/gameScreen.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );

            wp_enqueue_script(
                'scan-attendee-game-screen-game-start-screen',
                SCANATTENDEE_URL . 'assets/js/game/js/screen/startScreen.js',
                array('jquery'),
                SCANATTENDEE_VERSION,
                true
            );
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
