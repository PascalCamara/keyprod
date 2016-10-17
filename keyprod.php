<?php
/*
Plugin Name: Keyprod
Description: What's necessary to deploying my wordpress in production ? Keyprod tchecking all necessary for you.
Version: 0.1
Author: Pascal CAMARA
Author URI: https://automattic.com/wordpress-plugins/
License: GPLv2
Text Domain: keyprod
*/
if(!class_exists('Keyprod')) {
    class Keyprod {

        /**
         * Keyprod constructor.
         */
        public function __construct()
        {
            add_action( 'admin_menu', array($this,'add_menu' ));
            add_action( 'current_screen', array($this, 'init_page_scripts' ));
        }

        /*
         *  initalize menu BO
         */
        function add_menu()
        {
            add_options_page( 'Keyprod options', 'Keyprod', 'manage_options', 'keyprod_page_options', array($this, 'show_content') );
        }

        /**
         * Add view in page options
         */
        function show_content() {
            if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            echo '<div class="wrap">';
            echo '<h1>Welcom to Keyprod options</h1>';
            echo '<div id="keyprod-app">{{ message }}</div>';
            echo '<p>Here you can start your monitoring</p>';
            echo '<button type="button" class="btn btn-outline-primary">Start</button>';
            echo '</div>';
        }

        /**
         * load all scripts
         */
        function init_page_scripts() {
            if (get_current_screen()->base === "settings_page_keyprod_page_options") {
                wp_enqueue_style('keyprod_admin_css_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css', false, '1.0.0', 'all');
                wp_enqueue_script('keyprod_admin_js_tether', 'https://www.atlasestateagents.co.uk/javascript/tether.min.js', false, '1.0.0', false);
                wp_enqueue_script('keyprod_admin_js_bootstrap_hack', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js', false, '1.0.0', false);
                wp_enqueue_script('keyprod_admin_vuejs', plugins_url('keyprod/modules/vue/dist/vue.js'));
            }
        }




    }

    $keyprod = new Keyprod();



}
