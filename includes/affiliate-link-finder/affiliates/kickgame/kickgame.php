<?php

if (!class_exists('ExoKickgame')) {
    
class ExoKickgame {
    
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
        $this->feed_url = $this->keys['kickgame']['feedURL'];
        $this->feed_dir = plugin_dir_path( __FILE__ )  . 'kickgame-feed/';
        $this->feed_filename = 'kickgame-feed.xml';
        $this->feed_path = $this->feed_dir.$this->feed_filename;

    }
    
    public function get_products_by_sku($sku) {
        
        $match = array();
        
        foreach($this->feed_xml->item as $product){
            foreach($product->children() as $product_info){
                if($product_info->getName() === 'description') {
                    
                    if(strpos($product_info->asXml(),$sku) !== false) {
                        array_push($match, $product);
                        echo $product_info->name->asXml().'<br>';
                    }
                }
            }
        }
        echo '<br>Found: '.sizeof($match).'<br>';
        echo 'From Kickgame<br><br>';
        
        return $match;
    }
    
    public function delete_old_feed() {
        
        exo_delete_files_from_dir($this->feed_dir);
    }

    public function get_new_feed() {
        
        //exo_download_in_chunks($this->feed_url, $this->feed_path);
        $this->feed_xml = simplexml_load_file($this->feed_path);
        if ($this->feed_xml === false) {
            echo "Failed loading XML: ";
            foreach(libxml_get_errors() as $error) {
                echo "<br>", $error->message;
            }
        } else {
            $this->ns = $this->feed_xml->getNamespaces(true);
        }
    }
}
    
}

?> 