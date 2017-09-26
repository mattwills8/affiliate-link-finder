<?php

if (!class_exists('ExoEnd')) {
    
class ExoEnd {
    
    public $json;
    public $keys;
    public $config;
    public $feed_url;
    public $feed_dir;
    public $feed_xml;
    public $ns;
    
    public function __construct() {
        
        libxml_use_internal_errors(true);
        set_time_limit ( 300 );
        
        include plugin_dir_path( __FILE__ )  . '../../functions/file-handling.php';

        //get keys from json file
        $this->json = file_get_contents(plugin_dir_path( __FILE__ )  . '../../keys.json');
        $this->keys = json_decode($this->json, true);

        //set remote feed url and local dir
        $this->feed_url = $this->keys['end']['feedURL'];
        $this->feed_dir = plugin_dir_path( __FILE__ )  . 'end-feed/';
        $this->feed_filename = 'end-feed.xml';
        $this->feed_path = $this->feed_dir.$this->feed_filename;

    }
    
    //delete old feed
    public function delete_old_feed() {
        
        exo_delete_files_from_dir($this->feed_dir);
    }

        //get new feed
    public function get_new_feed() {
        
        exo_download_in_chunks($this->feed_url, $this->feed_path);
        $this->feed_xml = simplexml_load_file($this->feed_path);
        if ($this->feed_xml === false) {
            echo "Failed loading XML: ";
            foreach(libxml_get_errors() as $error) {
                echo "<br>", $error->message;
            }
        } else {
            $this->ns = $this->feed_xml->getNamespaces(true);
            // rememeber children($ns['g'])
        }
    }
    
    public function get_products_by_sku($sku) {
        
        foreach($this->feed_xml->entry as $product){
            foreach($product->children($this->ns['g']) as $product_info){
                if($product_info->getName() == 'mpn') {
                    echo $product_info.'<br>';
                }
            }
        }
    }
    
}
    
}

/*
$end = new ExoEnd();
$end->delete_old_feed();
$end->get_new_feed();
$end->get_products_by_sku('123423');
*/

?> 