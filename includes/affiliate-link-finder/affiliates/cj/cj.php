<?php

if (!class_exists('ExoCJ')) {
    
class ExoCJ {
    
    public $json;
    public $keys;
    public $config;
    public $client;
    
    public function __construct() {
        
        require_once plugin_dir_path( __FILE__ )  . '../../vendor/autoload.php';

        //get keys from json file
        $this->json = file_get_contents(plugin_dir_path( __FILE__ )  . '../../keys.json');
        $this->keys = json_decode($this->json, true);

        $this->config = [
            'key'   => $this->keys['cj']['key'],
            'id'    => $this->keys['cj']['id']
        ];

        $this->client = new \CROSCON\CommissionJunction\Client($this->config['key']);

    }
    
    public function search_products_by_sku($sku) {
        $matches = array();
        
        $matches = $this->search_products('advertiser-sku', $sku);
        
        if(empty($matches)){
            $matches = $this->search_products('manufacturer-sku', $sku);
        }
        
        if(empty($matches)){
            return 0;
        }
        return $matches;
    }
    
    public function search_products($search_field, $search_value) {
        
        $matches = $this->client->productSearch([
            'website-id'        =>  $this->config['id'],
            'advertiser-ids'    =>  'joined',
            'currency'          =>  'USD',
            $search_field       =>  $search_value
        ]);
        
        if($matches['products']['@attributes']['total-matched'] !== '0') {
            return $matches;
        }
        
        return 0;
    }

}

}

?>  