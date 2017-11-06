<?php

if (!class_exists('ExoWebgainsDE')) {

class ExoWebgainsDE {

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
        $this->feed_url = $this->keys['webgainsDE']['feedURL'];
        $this->feed_dir = AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains-de/webgains-feed/';
    }

    public function search_bstn($webgains_csv, $style_code) {

        if(!is_object($webgains_csv)){
            echo 'Couldnt load csv...<br>';
            return;
        }

        $match = array();
        $final_match = array();

        $match = $webgains_csv->filter_rows_by_col_value('manufacturers_product_number',$style_code);

        if($match){
            foreach($match as $matched_row) {

                if( $matched_row[11] == 'Bstnstore.com' ) {

                  $stock = false;
                  if($matched_row[14] == 'ja'){
                      $stock = true;
                  }

                  array_push($final_match, array(
                      'retailer'      => $matched_row[11],
                      'deeplink'      => $matched_row[2],
                      'in_stock'      => $stock,
                      'price'         => $matched_row[7],
                      'sale-price'    => ''
                  ));

                }
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: BSTN<br><br>';

        return $final_match;
    }

    public function search_slam_jam($webgains_csv, $style_code) {

        if(!is_object($webgains_csv)){
            echo 'Couldnt load csv...<br>';
            return;
        }

        $match = array();
        $final_match = array();

        $style_code_split = $this->split_style_code($style_code);

        $style_code_1 = $style_code_split[0];
        $style_code_2 = $style_code_split[1];

        $match = $webgains_csv->filter_rows_by_col_value_contains('description',$style_code_1);
        if($match){
            foreach($match as $matched_row) {

                if(strpos($matched_row[4],$style_code_2) !== false) {

                    $stock = true;

                    array_push($final_match, array(
                        'retailer'      => $matched_row[12],
                        'deeplink'      => $matched_row[3],
                        'in_stock'      => $stock,
                        'price'         => $matched_row[8],
                        'sale-price'    => ''
                    ));
                }
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Slamjam<br><br>';

        return $final_match;

    }

    public function search_sneaker_bass($webgains_csv,$style_code) {

        if(!is_object($webgains_csv)){
            echo 'Couldnt load csv...<br>';
            return;
        }

        $match = array();
        $final_match = array();

        $match = $webgains_csv->filter_rows_by_col_value('product_id','SB-'.$style_code);
        if($match){
            foreach($match as $matched_row) {

                $stock = true;

                array_push($final_match, array(
                    'retailer'      => $matched_row[12],
                    'deeplink'      => $matched_row[3],
                    'in_stock'      => $stock,
                    'price'         => $matched_row[8],
                    'sale-price'    => ''
                ));
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Sneakerbass<br><br>';

        return $final_match;
    }

    public function split_style_code($style_code) {

        $style_code_split = explode("-",$style_code);

        $style_code_1 = $style_code_split[0];

        if(!array_key_exists(1,$style_code_split)){
            $style_code_2 = $style_code_1;
        } else {
            $style_code_2 = $style_code_split[1];
        }

        return array($style_code_1,$style_code_2);
    }

    public function get_csv_object() {

        if($this->feed_path) {
            //CSV logic
            return new CSV($this->feed_path);
        }
        echo 'First get new feed<br>';
        return 0;

    }

    public function delete_old_feed() {

        exo_delete_files_from_dir($this->feed_dir);
    }

    public function get_new_feed() {

        exo_extract_remote_zip($this->feed_dir,AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/tmp/webgains-temp.zip',$this->feed_url);

        $this->set_feed_path();

    }

    public function set_feed_path(){

        if(!(is_dir($this->feed_dir))){
            echo "Couldn't set feed path, ".$this->feed_dir." does not exist<br>";
            return;
        }
        if(empty(exo_get_files_from_dir($this->feed_dir))){
            echo "Couldn't set feed path, ".$this->feed_dir." is empty<br>";
            return;
        }
        $this->feed_filename = exo_get_files_from_dir($this->feed_dir)[0];
        $this->feed_path = $this->feed_dir.$this->feed_filename;
    }

}

}


?>