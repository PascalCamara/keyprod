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
     * @param $id number id incremental test
     * @param $state number state
     * @param $message string message
     */
    private function addRapport($id, $state, $message) {
        array_push($this->rapport, array(
            'id' => ++$id,
            'state' => $state,
            'description' => $message
        ));
    }

    private function doTest()
    {
        $nbTest = 0;

        // wordpress update
        if($this->haskWordpressUpdate()) {
            $message = "Une mise a jour (" .$this->lastWordpressVersion->updates[0]->current.") Wordpress";
            $this->addRapport($nbTest, 2, $message);
        } else {
            $this->addRapport($nbTest, 1, "Votre Wordpress est à jour");
        };

        //
    }

    /**
     * @return bool check if has wordpress update
     */
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