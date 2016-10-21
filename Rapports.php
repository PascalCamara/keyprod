<?php
if(!class_exists('Rapports')) {
    class Rapports
    {

        /**
         * Rapports constructor.
         */
        public function __construct($file)
        {
            register_activation_hook($file, array($this, 'tables_install'));
            register_deactivation_hook($file, array($this, 'tables_uninstall'));
        }

        function tables_uninstall()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'keyprod';
            $sql = "DROP TABLE IF EXISTS $table_name;";
            $wpdb->query($sql);
            delete_option("keyprod_db_version");
        }

        function tables_install()
        {
            global $wpdb;
            global $keyprod_db_version;

            $table_name = $wpdb->prefix . 'keyprod';
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		rapport longtext NOT NULL,
        PRIMARY KEY  (id)
	    ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            add_option('keyprod_db_version', $keyprod_db_version);
        }

        /**
         * @param $rapports string
         * @return false|int
         */
        function setRapport($rapport)
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'keyprod';
            return $wpdb->insert(
                $table_name,
                array(
                    'time' => current_time('mysql'),
                    'rapport' => $rapport
                )
            );
        }

        function getRapport()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'keyprod';
            return $wpdb->get_results("SELECT * FROM $table_name ORDER BY `$table_name`.`time` DESC ");
        }

    }
};