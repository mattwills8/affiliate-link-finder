<?php

function mw_main($mw_echo=false) {

ob_start();

set_time_limit(12000);
ini_set('memory_limit', '20000M');
ini_set('max_file_uploads', '200000');
ini_set('max_input_time', '12000');
ini_set('post_max_size', '20000M');

require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/affilinet/affilinet.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/cj/cj.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains/webgains.php';

$retailers_id_list = [
  'Foot Locker'   =>  158,
  'Footshop.eu'   =>  314,
  'Caliroots'   =>  50,
  'SneakersnStuff'   =>  42,
  'End Clothing'   =>  49,
  'Kickgame'   =>  315,
  'slam jam socialism'   =>  41,
  'Sneaker Baas UK'   =>  316,
  'NIKE UK'   =>  53,

];

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

$kickgame->delete_old_feed();

$kickgame->get_new_feed();


$affilinet = new ExoAffilinet();

$cj = new ExoCJ();


foreach($exo_products as $product) {

    $result = array();
    $past_retailers = array();
    $found_retailers = array();
    $all_retailers_arr = array();
    $all_retailers = '';

    // set search vars and get necessary meta
    $product_id = $product->ID;
    $name = $product->post_title;
    $style_code = get_post_meta($product_id,'_sku')[0];
    $size = '10';

    $release_date_dt = new DateTime(get_post_meta($product_id,'product_release_date')[0]);
    $release_date_unix = $release_date_dt != '' ? $release_date_dt->getTimestamp() : '';

    $past_retailers = get_post_meta($product_id,'mw_all_retailers');
    if($past_retailers){
      $past_retailers_arr = explode("/",$past_retailers[0]);
    }

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

    if(is_object($affilinet)){
      //footlocker
      $affilinet_result = $affilinet->search_foot_locker($name,$style_code);
      if(!empty($afflinet_result)){
          $result[] = $affilinet_result;
      }
    } else {
      echo 'Couldnt search Affilinet since object was not created';
    }


    /*
    *
    * CJ SEARCHES
    *
    */
    echo '<h3>CJ....</h3>';

    if(is_object($cj)){
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
    } else {
      echo 'Couldnt search CJ since object was not created';
    }

    echo '<br><br>';

    if(sizeOf($result) != 0){

      $retailer_string = '';
      $retailer_ids = '';
      $retailer_links = '';
      $retailer_stock = '';
      $retailer_release_dates = '';
      $retailer_prices = '';
      $retailer_sale_prices = '';
      $i = 0;

      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];
        $found_retailers[] = $single_result['retailer'];
      }

      if($past_retailers) {
        foreach ($past_retailers_arr as $past_retailer) {
          if((!in_array($past_retailer,$found_retailers)) && $past_retailer != ''){
            echo 'past retailer: '.$past_retailer.' was not in results array<br>';
            array_push($result, array(array(
              'retailer'      => $past_retailer,
              'deeplink'      => '',
              'in_stock'      => false,
              'price'         => '',
              'sale-price'    => ''
            )));
          }
        }
      }

      var_dump($result);
      echo '<br><br>';
      //creating string to input into retailer field
      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];

        //format data for inputting into retailer field
        $retailer_id = $retailers_id_list[$single_result['retailer']];
        $retailer_id_len = strlen((string)$retailer_id);
        $all_retailers_arr[] = $single_result['retailer'];

        $retailer_link = $single_result['deeplink'];
        $retailer_link_len = strlen($retailer_link);

        $retailer_stock_status = (string)(int)$single_result['in_stock'];

        $retailer_release_date = $release_date_unix;

        $retailer_ids .= 'i:'.(string)$i.';s:'.$retailer_id_len.':"'.$retailer_id.'";';
        $retailer_links .= 'i:'.(string)$i.';s:'.$retailer_link_len.':"'.$retailer_link.'";';
        $retailer_stock .= 'i:'.(string)$i.';s:1:"'.$retailer_stock_status.'";';
        $retailer_release_dates .= 'i:'.(string)$i.';i:'.$retailer_release_date.';';

        if($single_result['price'] != ''){
          $retailer_prices .= $single_result['price'].'/';
        }

        if($single_result['sale-price'] != ''){
          $retailer_sale_prices .= $single_result['sale-price'].'/';
        }


        $i++;
      }
      $retailer_string .= 'a:4:{s:11:"retailer_id";a:'.(string)$i.':{'.$retailer_ids.'}';
      $retailer_string .= 's:13:"retailer_link";a:'.(string)$i.':{'.$retailer_links.'}';
      $retailer_string .= 's:12:"stock_status";a:'.(string)$i.':{'.$retailer_stock.'}';
      $retailer_string .= 's:21:"retailer_release_date";a:'.(string)$i.':{'.$retailer_release_dates.'}}';

      update_post_meta($product_id,'product_retailer',$retailer_string);
      update_post_meta($product_id,'mw_all_retailers',$all_retailers);
    }
}

$main_time_now = new DateTime();
echo '<p>Script run at: '.gmdate("Y-m-d H:i:s", $main_time_now->getTimestamp()).'</p>';

$buffer_size=ob_get_length();
if ($size > 0)
{
    $buffer_content = ob_get_contents();
    file_put_contents(AFFILIATE_LINK_FINDER_ROOT  . 'log.txt', $buffer_content, FILE_APPEND);
    ob_clean();
}

if($mw_echo === true){

  echo '<h2>Script Log: </h2>';
  echo $buffer_content;
}

return true;


}

?>
