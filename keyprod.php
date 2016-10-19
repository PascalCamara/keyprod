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

        private $keyprod_tables;
        /**
         * Keyprod constructor.
         */
        public function __construct()
        {
            add_action( 'admin_menu', array($this,'add_menu' ));
            add_action( 'current_screen', array($this, 'init_page_scripts' ));

            add_action( 'wp_ajax_nopriv_launch_test', array($this, 'launch_test' ));
            add_action( 'wp_ajax_launch_test', array($this, 'launch_test' ));

            add_action( 'wp_ajax_nopriv_launched_test', array($this, 'launched_test' ));
            add_action( 'wp_ajax_launched_test', array($this, 'launched_test' ));

            require 'Rapports.php';
            $this->keyprod_tables = new Rapports(__FILE__);
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
            echo '<div class="container">';
                echo '<div class="wrap">';
                    echo '<h1>Welcom to Keyprod options</h1>';
                    echo '<div id="keyprod-app">';
                        echo "<div v-if='hasHistoric' class='list-group'><a href='#' class='list-group-item list-group-item-action' style='margin-top:20px;' v-for='historic in hasHistoric' @click='displayHistoric(historic.id)'>Rapport du <b>{{ historic.date }}</b> <span class=\"tag tag-default tag-pill pull-xs-right\">{{historic.rapports.length}}</span></a></div>";
                        echo "<fake-loader v-if='loading'></fake-loader>";
                        echo '<checking-array v-if="displayChecking" :rapports="rapports" :trello="trello" style="margin-top: 20px;"></checking-array>';
                        echo '<p v-if="!launch">Here you can start your monitoring</p>';
                        echo '<button v-on:click="start" v-if="!launch" type="button" class="btn btn-outline-primary">Start</button>';
                        echo '<button v-on:click="historic" v-if="!launch" type="button" class="btn btn-outline-secondary" style="margin-left: 20px">Historic</button>';
                        echo '<button  v-if="!launch" type="button" class="btn btn-outline-secondary" style="margin-left: 20px">Configure Trello</button>';
                        echo "<div class='wrap'>";
                            echo "<button  @click='backTo' v-if='action' type=\"button\" class=\"btn btn-outline-secondary\" style=\"margin-top: 20px\">Back</button>";
                        echo "</div>";
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }

        /**
         * load all scripts
         */
        function init_page_scripts() {
            if (get_current_screen()->base === "settings_page_keyprod_page_options") {
                wp_enqueue_style('keyprod_admin_css_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css', false, '1.0.0', 'all');
                wp_enqueue_style('keyprod_admin_css_fakeloader', plugins_url('keyprod/modules/fakeLoader/fakeLoader.css'), false, '1.0.0', 'all');
                wp_enqueue_script('keyprod_admin_js_tether', 'https://www.atlasestateagents.co.uk/javascript/tether.min.js', false, '1.0.0', false);
                wp_enqueue_script('keyprod_admin_js_bootstrap_hack', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js', false, '1.0.0', false);
                wp_enqueue_script('keyprod_admin_js_fakeloader', plugins_url('keyprod/modules/fakeLoader/fakeLoader.js'), false, '1.0.0', false);
                wp_enqueue_script('keyprod_admin_vuejs', plugins_url('keyprod/modules/vue/dist/vue.js'));
                wp_register_script('keyprod_index_vuejs', plugins_url('keyprod/app/index.js'), ['keyprod_admin_vuejs']);
                wp_localize_script( 'keyprod_index_vuejs', 'keyprod_ajax_url', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' )
                ));
                wp_enqueue_script( 'keyprod_index_vuejs' );
            }
        }

        /**
         *  AJAX start a rapport test
         */
        function launch_test() {
            require_once 'Check.php';
            $checking = new Check();
            $data = $checking->getJsonRapport();
            echo $data;
            $this->keyprod_tables->setRapport($data);
            wp_die();
        }

        /**
         * AJAX get a rapport tests
         */
        function launched_test() {
            $rapports = $this->keyprod_tables->getRapport();
            $datas = array();
            if (!empty($rapports)){
                for ($i = 0; $i < count($rapports); $i++){
                    array_push(  $datas, [
                        "id" => $rapports[$i]->id,
                        "date" => date("j / n / Y", strtotime($rapports[$i]->time)),
                        "rapports" => json_decode($rapports[$i]->rapport)
                    ]);
                }
            }
            echo json_encode($datas);
            wp_die();
        }


    }

    $keyprod = new Keyprod();

}