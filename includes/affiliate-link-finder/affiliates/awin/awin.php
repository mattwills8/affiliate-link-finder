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


    public function search_awin($awin_csv, $name) {

        if(!is_object($awin_csv)){
            echo 'Couldnt load csv...<br>';
            return;
        }

        $match = array();
        $final_match = array();

        $keywords = $this->split_name_into_keywords( $name );

        $stock_row = $awin_csv->get_column_id('in_stock');
        $retailer_row = $awin_csv->get_column_id('merchant_name');
        $deeplink_row = $awin_csv->get_column_id('aw_deep_link');
        $price_row = $awin_csv->get_column_id('search_price');


        $match = $awin_csv->filter_rows_by_keywords('product_name',$keywords);
        if($match){
            foreach($match as $matched_row) {

                $stock = false;
                if($matched_row[$stock_row] == 1){
                    $stock = true;
                }

                // stop it from getting two matches from one retailer
                if( ! empty($final_match)) {
                  foreach ($final_match as $already_matched) {
                    if( $already_matched['retailer'] == $matched_row[$retailer_row] ) {
                      continue 2;
                    }
                  }
                }

                array_push($final_match, array(
                    'retailer'      => $matched_row[$retailer_row],
                    'deeplink'      => $matched_row[$deeplink_row],
                    'in_stock'      => $stock,
                    'price'         => $matched_row[$price_row],
                    'sale-price'    => ''
                ));
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Awin<br><br>';

        return $final_match;
    }

    public function split_name_into_keywords( $name ) {

      return explode( ' ', $name );
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

?>
