<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/mattwills8
 * @since      1.0.0
 *
 * @package    Affiliate_Link_Finder
 * @subpackage Affiliate_Link_Finder/admin/partials
 */

?>

<?php

require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/affilinet/affilinet.php'; 
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/awin/awin.php'; 
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/cj/cj.php'; 
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/end/end.php'; 
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/webgains/webgains.php';

//get woocommerce products    
$exo_args = array(
  'numberposts' => 2000,
  'post_type'   => 'product'
);
 
$exo_products = get_posts( $exo_args );


//create feed objects

$affilinet = new ExoAffilinet();

/*
$cj = new ExoCJ();


$awin = new ExoAwin();

$awin->delete_old_feed();

$awin->get_new_feed();

$awin_csv = $awin->get_csv_object();*/

/*
$webgains = new ExoWebgains();

$webgains->delete_old_feed();

$webgains->get_new_feed();

$webgains_csv = $webgains->get_csv_object();


$matching_rows = array();
*/

$style_code = '921948-401';
$style_code_split = explode("-",$style_code);
$style_code_1 = $style_code_split[0];
$style_code_2 = $style_code_split[1];

/*
*
* WEBGAINS SEARCHES
*

//nikeUK
$match = array();
$final_match = array();
$match = $webgains_csv->filter_rows_by_col_value('manufacturers_product_number',$style_code_1);
if($match){
    foreach($match as $matched_row) {
        
        if(strpos($matched_row[5],$style_code_1.'_'.$style_code_2) !== false) {
            array_push($final_match, $matched_row);
            echo $matched_row[10];
        }
    }
    echo '<br>Found: '.sizeof($final_match).'<br>';
    echo 'From: Nike UK<br><br>';
}

//slam jam
$match = array();
$final_match = array();
$match = $webgains_csv->filter_rows_by_col_value_contains('description',$style_code_1);
if($match){
    foreach($match as $matched_row) {
        
        if(strpos($matched_row[4],$style_code_2) !== false) {
            array_push($final_match, $matched_row);
            echo $matched_row[10];
        }
    }
    echo '<br>Found: '.sizeof($final_match).'<br>';
    echo 'From: Slamjam<br><br>';
}

//sneaker baas
$match = array();
$match = $webgains_csv->filter_rows_by_col_value('product_id','SB-'.$style_code);
if($match){
    foreach($match as $matched_row) {
        echo $matched_row[10].'<br>';
    }
    echo '<br>Found: '.sizeof($match).'<br>';
    echo 'From: SB<br><br>';
}



/*
*
* CJ SEARCHES
*

$cj_count = 0;

//sneakerstuff
$match = array();
$final_match = array();
$size = '10'
$sku = $style_code.'-'.$size;
$match = $cj->search_products_by_sku($sku);
if($match){
    foreach($match as $matched_row) {
        echo $matched_row['products']['product']['name'].'<br>';
    }
    echo '<br>Found: '.sizeof($match).'<br>';
    echo 'From: sneakerstuff<br><br>';
}

//caliroots
$match = array();
$final_match = array();
$sku = $style_code;
$keywords = '+Reebok +DMX +Run +10'
$match = $cj->search_products('keywords', $keywords);

if($match){
    foreach($match['products']['product'] as $matched_row) {
        if(strpos($matched_row['buy-url'],$sku) !== false ){
            array_push($final_match, $matched_row);
            echo $matched_row['buy-url'].'<br>';
        }
    }

    echo '<br>Found: '.sizeof($final_match).'<br>';
    echo 'From: caliroots<br><br>';
}


//avoid rate limiting
$cj_count++;
if($cj_count == 24){
    sleep(60);
    $cj_count = 0;
}
*/

$match = array();
$final_match = array();
$sku = '896176-601';
$name = 'Nike Dunk Retro Low';
$match = $affilinet->get_products_by_name($name);
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

    echo '<br>Found: '.sizeof($final_match).'<br>';
    echo 'From: affilinet<br><br>';
}

?>