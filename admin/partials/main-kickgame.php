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

$known_retailers = [
  'Foot Locker'           =>  158,
  'Footshop.eu'           =>  314,
  'Caliroots'             =>  50,
  'SneakersnStuff'        =>  42,
  'End Clothing'          =>  49,
  'Kickgame'              =>  315,
  'slam jam socialism'    =>  41,
  'Sneaker Baas UK'       =>  316,
  'NIKE UK'               =>  53,
  'Bstnstore.com'         =>  322,
  'OVERKILL'              =>  323,
  'Sneakerworldshop.com'  =>  324,
  'afew-store'            =>  38,
  'sneakerstudio.de'      =>  325,
  'Allike'                =>  52,
  'Offspring'             =>  317,
  '5pointz'               =>  326,
  'Footasylum'            =>  327,
  'Hipstore'              =>  328,
  'Office Shoes'          =>  173,
  'Offspring'             =>  317,
  '18montrose'            =>  329,
  'KongOnline.co.uk'      =>  330,
  'Aphrodite'             =>  331,
  'Life Style Sports'     =>  332,
  'Stuarts London'        =>  333,
];

$retailers_id_list = array_merge($known_retailers, get_all_retailer_terms());

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

      echo '<strong>'.sizeof($result).'</strong>';
      echo ' Retailers Matched<br><br>';

      // retailer found in the past
      $past_retailers_arr = array();
      $all_retailers_arr = array();

      $past_retailers = get_post_meta($product_id,'mw_all_retailers');
      if($past_retailers){
        $all_retailers_arr = explode("/",$past_retailers[0]);
      }

      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];
        $all_retailers_arr = append_retailer_id_to_array_if_id_found($single_result['retailer'], $all_retailers_arr, $retailers_id_list);
      }
      $all_retailers = implode("/", $all_retailers_arr);

      update_post_meta($product_id,'mw_all_retailers',$all_retailers);

      // result object shape to store in post meta
      $current_retailers_meta = get_post_meta($product_id,'product_retailer')[0];
      if( ! $current_retailers_meta) {
        $current_retailers_meta = '';
      }
      $current_retailers = unserialize($current_retailers_meta);

      // release date entered manually
      $release_date_dt = new DateTime(get_post_meta($product_id,'product_release_date')[0]);
      $release_date_unix = $release_date_dt != '' ? $release_date_dt->getTimestamp() : '';

      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];

        if( array_key_exists( $single_result['retailer'], $retailers_id_list ) ){
          $retailer_id = (string)$retailers_id_list[$single_result['retailer']];

          // if not in retailer string then add it
          if( ! in_array( $retailer_id, $current_retailers['retailer_id']) ) {
            $current_retailers['retailer_id'][] = $retailer_id;
            $current_retailers['retailer_link'][] = $single_result['deeplink'];
            $current_retailers['stock_status'][] = (string)(int)$single_result['in_stock'];
            $current_retailers['retailer_release_date'][] = $release_date_unix;
          }

          // if in retailer string then find index and update
          $retailer_index = array_search($retailer_id, $current_retailers['retailer_id']);
          if( $retailer_index !== false ) {
            $current_retailers['retailer_id'][$retailer_index] = $retailer_id;
            $current_retailers['retailer_link'][$retailer_index] = $single_result['deeplink'];
            $current_retailers['stock_status'][$retailer_index] = (string)(int)$single_result['in_stock'];
            $current_retailers['retailer_release_date'][$retailer_index] = $release_date_unix;
          }
        } else {
          echo "<br>Couldnt find ID for retailer with name ".$single_result['retailer'].". Make sure name matches retailer name in retailers list exactly.<br>";
        }
      }

      $retailer_string = serialize($current_retailers);

      update_post_meta($product_id,'product_retailer',$retailer_string);
    }
}

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
