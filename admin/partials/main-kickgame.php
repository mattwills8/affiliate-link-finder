<?php

function mw_kickgame_main() {

set_time_limit(0); ini_set('memory_limit', '2048M');error_reporting(E_ALL);

ini_set('output_buffering', 0);
ini_set('implicit_flush', 1);
ob_end_flush();
ob_start();

$main_time_now = new DateTime();
echo "##################STARTING KICKGAME ONLY PROCESS################## <br>";
echo "##################".gmdate("Y-m-d H:i:s", $main_time_now->getTimestamp())."################## <br>";

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
  //all posts if -1
  'numberposts' => -1,
  'post_type'   => 'product'
);

$exo_products = get_posts( $exo_args );

//get feeds

$kickgame = new ExoKickgame();

$kickgame->get_downloaded_feed();


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

    echo '<h2>'.$name.'</h2>';
    echo '<h3>'.$style_code.'</h3>';

     ob_flush(); flush();

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
    * PROCESS result
    */

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

      echo '<br>retailer string: '.$retailer_string.'<br>';

      update_post_meta($product_id,'product_retailer',$retailer_string);
      update_post_meta($product_id,'mw_all_retailers',$all_retailers);
    }
}

$main_time_now = new DateTime();
update_option('mw_last_run', $main_time_now->getTimestamp());
echo "##################END PROCESS################## /n";
echo "##################".gmdate("Y-m-d H:i:s", $main_time_now->getTimestamp())."################## /n";


$buffer_size=ob_get_length();
if ($size > 0)
{
    $buffer_content = ob_get_contents();
    file_put_contents(AFFILIATE_LINK_FINDER_ROOT  . 'log.txt', $buffer_content, FILE_APPEND);
    ob_clean();
}


echo '<h2>Script Log: </h2>';
echo $buffer_content;

return true;


}

?>
