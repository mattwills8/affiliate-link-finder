<?php

if (!class_exists('ExoAffilinet')) {
    
class ExoAffilinet {
    
    public $json;
    public $keys;
    public $config;
    
    public $affilinet;
    public $productsRequest;
    

    public function __construct() {
        
        require_once plugin_dir_path( __FILE__ )  . '../../vendor/autoload.php'; 
        
        //get keys from json file
        $this->json = file_get_contents(plugin_dir_path( __FILE__ )  . '../../keys.json');
        $this->keys = json_decode($this->json, true);
        
        //set config
        $this->config = [
            'publisher_id' => $this->keys['affilinet']['id'],
            'product_webservice_password' => $this->keys['affilinet']['pwd']
        ];
        
        //create affilinet helper object
        $this->affilinet = new \Affilinet\ProductData\AffilinetClient($this->config);
        
        // create a ProductsRequest Object
        $this->productsRequest = new \Affilinet\ProductData\Requests\ProductsRequest($this->affilinet);
    }
    
    public function search_foot_locker($name, $style_code){
        $match = array();
        $final_match = array();
        $sku = $style_code;
        
        $match = $this->get_products_by_name($name);
        
        if($match){

            foreach($match as $matched_product) {
                if(strpos($matched_product['props']['CF_mpn'],$sku) !== false ){
                    array_push($final_match, $matched_product);
                    echo $matched_product['name'].'<br>';
                    print_r($matched_product);
                    echo '<br><br>';
                    break;
                    /*
                    *
                    THERE WILL BE A DIFFERENT MATCH FOR EACH SIZE, THEY HAVE THE SAME LINK, BUT WE MAY NEED TO SEE THEM ALL FOR STOCK REASONS
                    *
                    */

                }
            }
        }
        
        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Footlocker<br><br>';
        
        return $final_match;
    }


    public function get_products_by_id($products_array) {

        try {
            $this->productsRequest->find( $products_array );

            $response = $this->productsRequest->send();
        }  
        catch (\Affilinet\ProductData\Exceptions\AffilinetProductWebserviceException $e) {
            echo 'Error: ' . $e->getMessage();
        } 

        echo 'Total results : ' . $response->totalRecords();

        foreach ($response->getProducts() as $product) {
            echo $product->getProductName().'<br>';
            echo $product->getDeeplink().'<br>';
        }

    }
    
    public function get_products_by_name($name) {
        $matches = array();

        try {
            $query = new \Affilinet\ProductData\Requests\Helper\Query();
            // will include apple but not applejuice
            $query->where(
                $query
                    ->expr()
                    ->exactly($name)
            );
            
            $this->productsRequest->query($query)->pageSize(500);

            $response = $this->productsRequest->send();
        }  
        catch (\Affilinet\ProductData\Exceptions\AffilinetProductWebserviceException $e) {
            echo 'Error: ' . $e->getMessage();
        } 

        //echo 'Total results : ' . $response->totalRecords();
        
        foreach ($response->getProducts() as $product) {
            
            $product_props = array(
                'name'      => $product->getProductName(),
                'deeplink'  => $product->getDeeplink(),
                'props'     => $product->getProperties()
            );
            array_push($matches,$product_props);
        }
        
        if(!empty($matches)){
            return $matches;
        }
        return 0;

    }
    
}

}


?>