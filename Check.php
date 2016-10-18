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

    public function init()
    {
        // WP current version
        $this->currentWordpressVersion = get_bloginfo('version');
        // WP last version
        $this->lastWordpressVersion = get_site_transient("update_core");
    }


    private function doTest()
    {
        $nbTest = 0;
        // wordpress update
        if($this->haskWordpressUpdate()) {
            array_push($this->rapport, array(
                "id" => $nbTest + 1,
                "state" => 2,
                "description" => "Une mise a jour Wordpress (". $this->lastWordpressVersion->updates[0]->current.") est disponible"
            ));
        } else {
            array_push($this->rapport, array(
                "id" => $nbTest + 1,
                "state" => 1,
                "description" => "Votre Wordpress est à jour"
            ));
        };
    }

    public function haskWordpressUpdate()
    {
        return intval(str_replace('.', '', $this->currentWordpressVersion)) < intval(str_replace('.', '', $this->lastWordpressVersion->updates[0]->current));
    }


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