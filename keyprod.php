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
            add_action( 'wp_ajax_nopriv_launch_test', array($this, 'launch_test' ));
            add_action( 'wp_ajax_launch_test', array($this, 'launch_test' ));
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
                echo '<div id="keyprod-app">';
                    echo '<p v-if="!launch">Here you can start your monitoring</p>';
                    echo '<button v-on:click="start" v-if="!launch" type="button" class="btn btn-outline-primary">Start</button>';
                echo '</div>';
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
                wp_register_script('keyprod_index_vuejs', plugins_url('keyprod/app/index.js'), ['keyprod_admin_vuejs']);
                wp_localize_script( 'keyprod_index_vuejs', 'keyprod_ajax_url', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' )
                ));
                wp_enqueue_script( 'keyprod_index_vuejs' );
            }
        }

        function launch_test() {
            $a = file_get_contents('http://api.wordpress.org/core/version-check/1.7/');
            $a = json_decode($a);
            echo $a->offers[0]->current;
            //echo $a;
            //echo json_encode(get_bloginfo('version'));
            wp_die();

        }


    }

    $keyprod = new Keyprod();

}