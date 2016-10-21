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
        private $trello_keyprod_options = "trello_keyprod_options";
        private $trello_token;

        /**
         * Keyprod constructor.
         */
        public function __construct()
        {
            require 'Rapports.php';
            $this->keyprod_tables = new Rapports(__FILE__);

            //config trello
            $this->trello_token = get_option($this->trello_keyprod_options);

            add_action( 'admin_menu', array($this,'add_menu' ));
            add_action( 'current_screen', array($this, 'init_page_scripts' ));

            add_action( 'wp_ajax_nopriv_launch_test', array($this, 'launch_test' ));
            add_action( 'wp_ajax_launch_test', array($this, 'launch_test' ));

            add_action( 'wp_ajax_nopriv_launched_test', array($this, 'launched_test' ));
            add_action( 'wp_ajax_launched_test', array($this, 'launched_test' ));

            add_action( 'wp_ajax_nopriv_ajax_set_trello_token', array($this, 'ajax_set_trello_token' ));
            add_action( 'wp_ajax_ajax_set_trello_token', array($this, 'ajax_set_trello_token' ));

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
                        echo "<div v-if='hasHistoric' class='list-group'><a href='#' class='list-group-item list-group-item-action' style='margin-top:20px;' v-for='historic in hasHistoric' @click='displayHistoric(historic)'>Rapport du <b>{{ historic.date }}</b> <span class=\"tag tag-default tag-pill pull-xs-right\" style='background: red;margin-left: 10px;'>{{historic.errors}}</span><span class=\"tag tag-default tag-pill pull-xs-right\" style='background: orange;margin-left: 10px;'>{{historic.warnings}}</span><span class=\"tag tag-default tag-pill pull-xs-right\" style='background: #5cb85c;margin-left: 10px;'>{{historic.success}}</span></a></div>";
                        echo "<fake-loader v-if='loading'></fake-loader>";
                        echo '<checking-array v-if="displayChecking" :rapports="rapports" :trello="trello" style="margin-top: 20px;"></checking-array>';
                        echo '<p v-if="!launch">Here you can start your monitoring</p>';
                        echo '<button v-on:click="start" v-if="!launch" type="button" class="btn btn-outline-primary">Start</button>';
                        echo '<button v-on:click="historic" v-if="!launch" type="button" class="btn btn-outline-secondary" style="margin-left: 20px">Historic</button>';
                        echo '<button v-on:click="configTrello" v-if="!launch && !trello" type="button" class="btn btn-outline-secondary" style="margin-left: 20px">Configure Trello</button>';

                        //trello
                        echo '<div v-if="displayConfigTrello" class="form-group row" style="margin-top: 20px;">';
                            echo "<p>You can get you trello token at this <a href='https://trello.com/app-key' target='_blank'>adresse</a></p>";
                            echo '<label for="example-text-input" class="col-xs-2 col-form-label">Trello Api Key</label>';
                            echo '<div class="col-xs-6">';
                                echo '<input v-model="model_token" class="form-control" type="text" placeholder="ee5f72dea296d4nnnb65aa580922eecac" id="example-text-input">';
                            echo '</div>';
                            echo '<button v-if="model_token" v-on:click="validationToken" type="button" class="btn btn-outline-primary" style="margin-left: 20px">Save</button>';
                        echo '</div>';

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
                if ($this->trello_token);
                    wp_enqueue_script('keyprod_admin_js_trello', "https://api.trello.com/1/client.js?key=$this->trello_token", 'jquery', '1.0.0', false);
                wp_enqueue_script('keyprod_admin_vuejs', plugins_url('keyprod/modules/vue/dist/vue.js'));
                wp_register_script('keyprod_index_vuejs', plugins_url('keyprod/app/index.js'), ['keyprod_admin_vuejs']);
                wp_localize_script( 'keyprod_index_vuejs', 'keyprod_ajax_url', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                ));
                wp_enqueue_script( 'keyprod_index_vuejs' );
                wp_localize_script( 'keyprod_index_vuejs', "trello_token", $this->trello_token );
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
                        "date" => date("j / n / Y à H:i:s", strtotime($rapports[$i]->time)),
                        "rapports" => json_decode($rapports[$i]->rapport)
                    ]);
                }
            }
            echo json_encode($datas);
            wp_die();
        }

        /**
         * Ajax set trello token
         */
        function ajax_set_trello_token() {
            if (isset($_POST['token']) && $this->trello_token = $this->set_trello_token($_POST['token'])) {
                echo json_encode("");
            } else {
                echo json_encode("error");
            }

            wp_die();
        }

        function set_trello_token($token){
            return update_option($this->trello_keyprod_options, $token);
        }
    }

    $keyprod = new Keyprod();

}