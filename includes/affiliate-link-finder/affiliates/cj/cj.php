<?php

if (!class_exists('ExoCJ')) {

class ExoCJ {

    public $json;
    public $keys;
    public $config;
    public $client;

    public function __construct() {

        require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/vendor/autoload.php';

        //get keys from json file
        $this->json = file_get_contents(AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/keys.json');
        $this->keys = json_decode($this->json, true);

        $this->config = [
            'key'   => $this->keys['cj']['key'],
            'id'    => $this->keys['cj']['id']
        ];

        $this->client = new \CROSCON\CommissionJunction\Client($this->config['key']);

    }

    public function search_footshop_eu($name, $style_code) {

        $match = array();
        $final_match = array();

        $sku = $style_code;

        $split_name = explode(" ",$name);
        $keywords = '';
        foreach($split_name as $word){
            $keywords .= '+'.$word.' ';
        }

        $match = $this->search_products('keywords', $keywords);

        if($match){
            foreach($match['products']['product'] as $product) {

                if($product['advertiser-name'] !== "Footshop.eu"){
                    continue;
                }
                if( $product["manufacturer-sku"] === $sku ){
                    array_push($final_match, array(
                        'retailer'      => 'Footshop.eu',
                        'deeplink'      => $product['buy-url'],
                        'in_stock'      => ($product['in-stock'] === 'true'),
                        'price'         => $product['price'].$product['currency'],
                        'sale-price'    => $product['sale-price'].$product['currency']
                    ));
                }
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Footshop.eu<br><br>';

        return $final_match;
    }


    public function search_cali_roots($name, $style_code) {

        $match = array();
        $final_match = array();

        $sku = $style_code;

        $split_name = explode(" ",$name);
        $keywords = '';
        foreach($split_name as $word){
            $keywords .= '+'.$word.' ';
        }
        $keywords = $name;

        $match = $this->search_products('keywords', $keywords);

        if($match){
            foreach($match['products']['product'] as $matched_row) {
                if($matched_row['advertiser-name'] !== "Caliroots"){
                    continue;
                }
                if(strpos($matched_row['buy-url'],$sku) !== false ){
                    array_push($final_match, array(
                        'retailer'      => 'Caliroots',
                        'deeplink'      => $product['buy-url'],
                        'in_stock'      => ($product['in-stock'] === 'true'),
                        'price'         => $product['price'].$product['currency'],
                        'sale-price'    => $product['sale-price'].$product['currency']
                    ));
                }
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Caliroots<br><br>';

        return $final_match;
    }

    public function search_sneakers_n_stuff($style_code, $size='10') {

        $match = array();
        $final_match = array();

        $sku = $style_code.'-'.$size;

        $match = $this->search_products_by_sku($sku);
        if($match){
            foreach($match as $matched_row) {
                if($matched_row['advertiser-name'] !== "Sneakersnstuff"){
                    continue;
                }
                //if(strpos($matched_row['buy-url'],$sku) !== false ){
                    array_push($final_match, array(
                        'retailer'      => 'SneakersnStuff',
                        'deeplink'      => $product['buy-url'],
                        'in_stock'      => ($product['in-stock'] === 'true'),
                        'price'         => $product['price'].$product['currency'],
                        'sale-price'    => $product['sale-price'].$product['currency']
                    ));
                //}
            }
        }

        echo '<br>Found: '.sizeof($final_match).'<br>';
        echo 'From: Sneakersnstuff<br><br>';

        return $match['products'];


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
