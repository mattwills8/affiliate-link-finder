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
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/kickgame/kickgame.php'; 
require_once plugin_dir_path( __FILE__ )  . '../../includes/affiliate-link-finder/affiliates/webgains/webgains.php';

//get woocommerce products    
$exo_args = array(
  'numberposts' => 10,
  'post_type'   => 'product'
);
 
$exo_products = get_posts( $exo_args );


//get feeds

$webgains = new ExoWebgains();

//$webgains->delete_old_feed();

//$webgains->get_new_feed();

$webgains->set_feed_path();

$webgains_csv = $webgains->get_csv_object();


$end = new ExoEnd();

//$end->delete_old_feed();
    
$end->get_new_feed();


$kickgame = new ExoKickgame();

//$end->delete_old_feed();
    
$kickgame->get_new_feed();


$affilinet = new ExoAffilinet();


$cj = new ExoCJ();


foreach($exo_products as $product) {
    
    // set search vars
    $product_id = $product->ID;
    $name = $product->post_title;
    $style_code = get_post_meta($product_id,'_sku')[0];
    $size = '10';
    
    echo '<h1>'.$name.'</h1>';
    echo $style_code.'<br>';

    
    /*
    *
    * WEBGAINS SEARCHES
    *
    */
    echo '<h3>Webgains....</h3>';

    //nikeUK
    $webgains->search_nike_uk($webgains_csv,$style_code);

    //slam jam
    $webgains->search_slam_jam($webgains_csv,$style_code);

    //sneaker baas
    $webgains->search_sneaker_bass($webgains_csv,$style_code);



    /*
    *
    * END SEARCHES
    *
    */
    echo '<h3>EndClothing....</h3>';

    $end->get_products_by_sku($style_code);



    /*
    *
    * KICKGAME SEARCHES
    *
    */
    echo '<h3>Kickgame....</h3>';

    $kickgame->get_products_by_sku($style_code);



    /*
    *
    * AFFILINET SEARCHES
    *
    */
    echo '<h3>Afflinet....</h3>';

    //footlocker
    $affilinet->search_foot_locker($name,$style_code);



    /*
    *
    * CJ SEARCHES
    *
    */
    echo '<h3>CJ....</h3>';

    $cj_count = 0;

    //sneakerstuff
    $cj->search_sneakers_n_stuff($style_code,$size);
    $cj_count++;

    //caliroots
    $cj->search_cali_roots($name,$style_code);
    $cj_count++;

    //footshop.eu
    $cj->search_footshop_eu($name,$style_code);

    //avoid rate limiting
    if($cj_count == 24){
        sleep(60);
        $cj_count = 0;
    }
}
?>