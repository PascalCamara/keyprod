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



add_action( 'admin_menu', 'keyprod_menu' );
function keyprod_menu() {
    add_options_page( 'Keyprod options', 'Keyprod', 'manage_options', 'keyprod_page_options', 'keyprod_options' );
}

function keyprod_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<h1>Welcom to Keyprod options</h1>';
    echo '<p>Here you can start your monitoring</p>';
    echo '<button type="button" class="btn btn-outline-primary">Start</button>';
    echo '</div>';
}


add_action( 'current_screen', 'init_keyprod_page_options' );
function init_keyprod_page_options( ){
    if (get_current_screen()->base === "settings_page_keyprod_page_options") {
        wp_enqueue_style('keyprod_admin_css_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css', false, '1.0.0', 'all');
        wp_enqueue_script('keyprod_admin_js_tether', 'https://www.atlasestateagents.co.uk/javascript/tether.min.js', false, '1.0.0', false);
        wp_enqueue_script('keyprod_admin_js_bootstrap_hack', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js', false, '1.0.0', false);
        wp_enqueue_script('keyprod_admin_vuejs', plugins_url('keyprod/modules/vue/dist/vue.js'));
    }

}


