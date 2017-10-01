<?php

if (!class_exists('ExoAwin')) {

class ExoAwin {

    public $json;
    public $keys;
    public $config;
    public $feed_url;
    public $feed_dir;
    public $feed_filename;
    public $feed_path;

    public function __construct() {

        set_time_limit ( 300 );

        //include helpers
        require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/classes/class-csv.php';
        include AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/functions/file-handling.php';

        //get keys from json file
        $this->json = file_get_contents(AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/keys.json');
        $this->keys = json_decode($this->json, true);

        //set remote feed url and local dir
        $this->feed_url = $this->keys['awin']['feedURL'];
        $this->feed_dir = AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin-feed/';
    }


    //delete old feed
    public function delete_old_feed() {

        exo_delete_files_from_dir($this->feed_dir);
    }


    //get new feed
    public function get_new_feed() {

        exo_extract_remote_zip($this->feed_dir,AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/tmp/awin-temp.zip',$this->feed_url);

        $this->set_feed_path();
    }

    public function get_csv_object() {

        if($this->feed_path) {
            //CSV logic
            return new CSV($this->feed_path);
        }
        echo 'First get new feed';
        return 0;

    }

    public function set_feed_path(){

        $this->feed_filename = exo_get_files_from_dir($this->feed_dir)[0];
        $this->feed_path = $this->feed_dir.$this->feed_filename;
    }

}

}

//$awin = new ExoAwin();

//$awin->delete_old_feed();

//$awin->get_new_feed();

//$awin->set_feed_path();

//$awin_csv = $awin->get_csv_object();


?>
