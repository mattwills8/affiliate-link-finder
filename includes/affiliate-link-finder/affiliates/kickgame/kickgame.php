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
    public $xmlReader;
    public $DOMDocument;

    public function __construct() {

        libxml_use_internal_errors(true);
        set_time_limit ( 300 );

        include AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/functions/file-handling.php';

        //get keys from json file
        $this->json = file_get_contents(AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/keys.json');
        $this->keys = json_decode($this->json, true);

        //set remote feed url and local dir
        $this->feed_url = $this->keys['kickgame']['feedURL'];
        $this->feed_dir = AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame-feed/';
        $this->feed_filename = 'kickgame-feed.xml';
        $this->feed_path = $this->feed_dir.$this->feed_filename;

    }

    public function get_products_by_sku($sku) {

        $match = array();

        $checked = 0;

        // move to the first <entry /> node
        while ($this->feed_xml->read() && $this->feed_xml->name !== 'entry');

        // now that we're at the right depth, hop to the next <entry /> until the end of the tree
        while ($this->feed_xml->name === 'entry')
        {
            $node = new SimpleXMLElement($this->feed_xml->readOuterXML());
            if ($node === NULL) {
              continue;
            }
            $this->ns = $node->getNamespaces(true);

            foreach($node->children($this->ns['g']) as $product_info){

                if($product_info->getName() === 'description') {

                    if(strpos($product_info->asXml(),$sku) !== false) {

                        // echo dom_import_simplexml($product_info)->nodeValue;

                        $stock = true; // can assume that if we found one then it's in stock - seems to be how the feed is set up
                        /*foreach($node->children($this->ns['g']) as $product_info_stock){
                            if($product_info_stock->getName() === 'availability') {

                                if(strpos($product_info_stock->asXml(),"In Stock") !== false) {
                                    $stock = true;
                                }
                            }
                        }*/

                        $link = '';
                        foreach($node->children($this->ns['g']) as $product_info_2){
                            if($product_info_2->getName() === 'link') {
                                $link = str_replace("</g:link>","",str_replace("<g:link>","",$product_info_2->asXml()));
                            }
                        }

                        $price = '';
                        foreach($node->children($this->ns['g']) as $product_info_3){
                            if($product_info_3->getName() === 'price') {
                                $price = substr($product_info_3->asXml(),7,-8);
                            }
                        }

                        array_push($match, array(
                            'retailer'      => 'Kickgame',
                            'deeplink'      => $link,
                            'in_stock'      => $stock,
                            'price'         => $price,
                            'sale-price'    => ''
                        ));
                    }
                }
            }

            //TODO: UNCOMMENT THIS. WE NEED TO JUST LOOP THROUGH THE XML ONCE, NOT FOR EVERY PRODUCT, MAYBE USING AN ARRAY OF SKU
            // go to next <entry />
            $checked = $checked + 1;
            $this->feed_xml->next('entry');
        }

        echo '<br>Found: '.sizeof($match).'<br>';
        echo 'From: Footshop.eu<br>';
        echo '<br>Checked: '.$checked.' Entries<br><br>';

        return $match;
    }

    public function delete_old_feed() {

        exo_delete_files_from_dir($this->feed_dir);
    }

    public function get_new_feed() {

        exo_download_in_chunks($this->feed_url, $this->feed_path);

        $this->get_downloaded_feed();
    }

    public function get_downloaded_feed() {

        $this->create_xmlReader();

        $this->feed_xml->open($this->feed_path);

        if ($this->feed_xml === false) {
            echo "Failed loading XML";
        }
    }

    public function create_xmlReader() {

      $this->feed_xml= new XMLReader;
    }

    public function create_DOMDocument() {

      $this->DOMDocument = new DOMDocument;
    }
}

}

?>
