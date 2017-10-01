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

require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/affilinet/affilinet.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/cj/cj.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains/webgains.php';

//get woocommerce products
$exo_args = array(
  'numberposts' => 5,
  'post_type'   => 'product'
);

$exo_products = get_posts( $exo_args );


//get feeds

$webgains = new ExoWebgains();

$webgains->delete_old_feed();

$webgains->get_new_feed();

//$webgains->set_feed_path();

$webgains_csv = $webgains->get_csv_object();


$end = new ExoEnd();

$end->delete_old_feed();

$end->get_new_feed();


$kickgame = new ExoKickgame();

$end->delete_old_feed();

$kickgame->get_new_feed();


$affilinet = new ExoAffilinet();


$cj = new ExoCJ();


foreach($exo_products as $product) {

    $result = array();

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
    $nike_result = $webgains->search_nike_uk($webgains_csv,$style_code);
    if(!empty($nike_result)){
        $result[] = $nike_result;
    }


    //slam jam
    $slamjam_result = $webgains->search_slam_jam($webgains_csv,$style_code);
    if(!empty($slamjam_result)){
        $result[] = $slamjam_result;
    }

    //sneaker baas
    $sneakerbaas_result = $webgains->search_sneaker_bass($webgains_csv,$style_code);
    if(!empty($sneakerbaas_result)){
        $result[] = $sneakerbaas_result;
    }


    /*
    *
    * END SEARCHES
    *
    */
    echo '<h3>EndClothing....</h3>';

    $endclothing_result = $end->get_products_by_sku($style_code);
    if(!empty($endclothing_result)){
        $result[] = $endclothing_result;
    }


    /*
    *
    * KICKGAME SEARCHES
    *
    */
    echo '<h3>Kickgame....</h3>';

    $kickgame_result = $kickgame->get_products_by_sku($style_code);
    if(!empty($kickgame_result)){
        $result[] = $kickgame_result;
    }


    /*
    *
    * AFFILINET SEARCHES
    *
    */
    echo '<h3>Afflinet....</h3>';

    //footlocker
    $affilinet_result = $affilinet->search_foot_locker($name,$style_code);
    if(!empty($afflinet_result)){
        $result[] = $affilinet_result;
    }


    /*
    *
    * CJ SEARCHES
    *
    */
    echo '<h3>CJ....</h3>';

    $cj_count = 0;

    //sneakerstuff
    $sneakerstuff_result = $cj->search_sneakers_n_stuff($style_code,$size);
    $cj_count++;
    if(!empty($sneakerstuff_result)){
        $result[] = $sneakerstuff_result;
    }

    //caliroots
    $caliroots_result = $cj->search_cali_roots($name,$style_code);
    $cj_count++;
    if(!empty($caliroots_result)){
        $result[] = $caliroots_result;
    }

    //footshop.eu
    $footshop_eu_result = $cj->search_footshop_eu($name,$style_code);
    $cj_count++;
    if(!empty($footshop_eu_result)){
        $result[] = $footshop_eu_result;
    }

    //avoid rate limiting
    if($cj_count == 24){
        sleep(60);
        $cj_count = 0;
    }


    var_dump($result);
}

?>
