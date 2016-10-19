<?php

class Check {

    private $rapport;

    private $currentWordpressVersion;
    private $lastWordpressVersion;

    /**
     * Check constructor.
     */
    public function __construct()
    {
        $this->rapport = array();
        $this->init();
        $this->doTest();
    }

    /**
     *  Define system variables
     */
    public function init()
    {
        // WP current version
        $this->currentWordpressVersion = get_bloginfo('version');
        // WP last version
        $this->lastWordpressVersion = get_site_transient("update_core");
    }

    /**
     *  addRapport push an array
     * @param $state number state
     * @param $message string message
     */
    private function addRapport($state, $message) {
        $id = empty($this->rapport) ? 1 :  count($this->rapport) + 1;
        array_push($this->rapport, array(
            'id' => $id,
            'state' => $state,
            'description' => $message
        ));
    }

    private function doTest()
    {
        // wordpress update
        if($this->haskWordpressUpdate()) {
            $message = ["Une mise a jour (" .$this->lastWordpressVersion->updates[0]->current.") Wordpress"];
            $this->addRapport(2, $message);
        } else {
            $this->addRapport(1, ["Votre Wordpress est à jour"]);
        };

        // checking perms directory
//        for ($i = 1; $i < count($this->checkDirectoryPermissions()); $i++) {}
//            $this->addRapport(3,$this->checkDirectoryPermissions()[$i] );

        // checking if const wp FS_CHMOD_DIR exist
        if (defined("FS_CHMOD_DIR")) {
            if (FS_CHMOD_DIR === 493)
                $this->addRapport(1, ["Votre constante FS_CHMOD_DIR est bien définie"]);
            elseif (FS_CHMOD_DIR ===755)
                $this->addRapport(2, [
                    "Votre constante est bien définie",
                    "Cependant la valeur n'est pas bonne" ,
                    "Essayer plutôt d'écrire '0755' au lieu de '755'"
                ]);
            else
                $this->addRapport(2, [
                    "Votre constante FS_CHMOD_DIR est bien définie cependant la valeur n'est pas bonne",
                    "il est conseiller de mettre '0755'"
                ]);
        } else {
            $this->addRapport(3, [
                "Vous n'avez pas défini la constante FS_CHMOD_DIR",
                "Vous pouvez le définir dans le fichier wp-config.php en ajoutant :",
                "define('FS_CHMOD_DIR', 0755);"
            ]);
        };

        // checking if const wp FS_CHMOD_DIR exist
        if (defined("FS_CHMOD_FILE")) {
            if (FS_CHMOD_FILE ===  420)
                $this->addRapport(1, ["Votre constante FS_CHMOD_FILE est bien définie"]);
            elseif (FS_CHMOD_FILE ===644)
                $this->addRapport(2, [
                    "Votre constante est bien définie",
                    "Cependant la valeur n'est pas bonne" ,
                    "Essayer plutôt d'écrire '0644' au lieu de '644'"
                ]);
            else
                $this->addRapport(2, [
                    "Votre constante FS_CHMOD_FILE est bien définie cependant la valeur n'est pas bonne",
                    "il est conseiller de mettre '0644'"
                ]);
        } else {
            $this->addRapport(3, [
                "Vous n'avez pas défini la constante FS_CHMOD_FILE",
                "Vous pouvez le définir dans le fichier wp-config.php en ajoutant :",
                "define('FS_CHMOD_FILE', 0644);"
            ]);
        };
    }

    /**
     * @return bool check if has wordpress update
     */
    public function haskWordpressUpdate()
    {
        return intval(str_replace('.', '', $this->currentWordpressVersion)) < intval(str_replace('.', '', $this->lastWordpressVersion->updates[0]->current));
    }

//    public function checkDirectoryPermissions()
//    {
//        return explode('@',shell_exec("cd ../ ;ls -R -la | awk '{print $1, $9}'"));
//    }


    /**
     * @return string
     */
    public function getRapport()
    {
        return $this->rapport;
    }

    /**
     * @return string encoded json
     */
    public function getJsonRapport()
    {
        return json_encode($this->rapport);
    }
}