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

        include AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/functions/file-handling.php';

        //get keys from json file
        $this->json = file_get_contents(AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/keys.json');
        $this->keys = json_decode($this->json, true);

        //set remote feed url and local dir
        $this->feed_url = $this->keys['end']['feedURL'];
        $this->feed_dir = AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end-feed/';
        $this->feed_filename = 'end-feed.xml';
        $this->feed_path = $this->feed_dir.$this->feed_filename;

    }

    public function get_products_by_sku($sku) {

        $match = array();

        foreach($this->feed_xml->entry as $product){
            foreach($product->children($this->ns['g']) as $product_info){
                if($product_info->getName() === 'mpn') {

                    $val_with_tag = $product_info->asXml();
                    $val = substr($val_with_tag,7,-8);

                    if($val === $sku) {

                        $stock = false;
                        foreach($product->children($this->ns['g']) as   $product_info_stock){
                            if($product_info_stock->getName() === 'availability') {

                                $val_with_tag = $product_info_stock->asXml();
                                $val = substr($val_with_tag,16,-17);

                                $stock = ($val === 'in stock');
                            }
                        }

                        $link = '';
                        foreach($product->children($this->ns['g']) as   $product_info_2){
                            if($product_info_2->getName() === 'link') {
                                $link = $product_info_2->asXml();
                            }
                        }

                        $price = '';
                        foreach($product->children($this->ns['g']) as   $product_info_3){
                            if($product_info_3->getName() === 'price') {
                                $price = $product_info_3->asXml();
                            }
                        }

                        array_push($match, array(
                            'retailer'      => 'End Clothing',
                            'deeplink'      => $link,
                            'in_stock'      => $stock,
                            'price'         => $price,
                            'sale-price'    => ''
                        ));
                    }
                }
            }
        }

        echo '<br>Found: '.sizeof($match).'<br>';
        echo 'From EndClothing<br><br>';

        return $match;
    }

    public function delete_old_feed() {

        exo_delete_files_from_dir($this->feed_dir);
    }

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
}

}

?>
