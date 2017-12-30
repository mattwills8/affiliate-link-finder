<?php

function get_awin_feed() {

  require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin.php';

  $awin = new ExoAwin();

  $awin->delete_old_feed();

  $awin->get_new_feed();

}

function get_webgains_feed() {

  require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains/webgains.php';

  $webgains = new ExoWebgains();

  $webgains->delete_old_feed();

  $webgains->get_new_feed();

}

function get_webgains_de_feed() {

  require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains-de/webgains-de.php';

  $webgains_de = new ExoWebgainsDE();

  $webgains_de->delete_old_feed();

  $webgains_de->get_new_feed();

}

function get_end_feed() {

  set_time_limit(0); ini_set('memory_limit', '2048M');

  require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end.php';


  $end = new ExoEnd();

  $end->delete_old_feed();

  $end->get_new_feed();

}

function get_kickgame_feed() {

  set_time_limit(0); ini_set('memory_limit', '2048M');

  ini_set('output_buffering', 0);
  ini_set('implicit_flush', 1);
  ob_end_flush();
  ob_start();

  require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame.php';

  $kickgame = new ExoKickgame();

  $kickgame->delete_old_feed();

  $kickgame->get_new_feed();

}



function mw_main() {

set_time_limit(0); ini_set('memory_limit', '2048M');error_reporting(E_ALL);

ini_set('output_buffering', 0);
ini_set('implicit_flush', 1);
ob_end_flush();
ob_start();

$main_time_now = new DateTime();
update_option('mw_last_run', $main_time_now->getTimestamp());

echo "##################STARTING PROCESS################## <br>";
echo "##################".gmdate("Y-m-d H:i:s", $main_time_now->getTimestamp())."################## <br>";

require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/affilinet/affilinet.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/cj/cj.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains/webgains.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains-de/webgains-de.php';

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

$awin = new ExoAwin();

$awin->set_feed_path();

$awin_csv = $awin->get_csv_object();


$webgains = new ExoWebgains();

$webgains->set_feed_path();

$webgains_csv = $webgains->get_csv_object();


$webgains_de = new ExoWebgainsDE();

$webgains_de->set_feed_path();

$webgains_de_csv = $webgains_de->get_csv_object();


$end = new ExoEnd();

$end->get_downloaded_feed();

/*
$kickgame = new ExoKickgame();

$kickgame->get_downloaded_feed();
*/

$affilinet = new ExoAffilinet();

$cj = new ExoCJ();


foreach($exo_products as $product) {

    $result = array();
    $found_retailers = array();

    // set search vars and get necessary meta
    $product_id = $product->ID;
    $name = $product->post_title;
    $style_code = get_post_meta($product_id,'_sku')[0];
    $size = '10';

    echo '<h2>'.$name.'</h2>';
    echo '<h3>'.$style_code.'</h3>';

    ob_flush(); flush();

    if ( ! $style_code ) {
      echo '<h4>Style code not found in post...skipping to next product</h4><br><br>';
      ob_flush(); flush();
      continue;
    }

    /*
    *
    * AWIN SEARCHES
    *
    */
    echo '<h3>Awin....</h3>';

    //all awin
    $awin_result = $awin->search_awin($awin_csv,$name);
    if(!empty($awin_result)){
      foreach($awin_result as $awin_single_result){
        $result[] = array($awin_single_result);
      }
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

    // aphrodite
    $aphrodite_result = $webgains->search_aphrodite($webgains_csv,$style_code);
    if(!empty($aphrodite_result)){
        $result[] = $aphrodite_result;
    }

    //18Montrse
    $montrse_result = $webgains->search_18montrse($webgains_csv,$name);
    if(!empty($montrse_result)){
        $result[] = $montrse_result;
    }

    // kong online
    $kong_online_result = $webgains->search_kong_online($webgains_csv,$name);
    if(!empty($kong_online_result)){
        $result[] = $kong_online_result;
    }

    // lifestyle sports
    $lifestyle_sports_result = $webgains->search_lifestyle_sports($webgains_csv,$name);
    if(!empty($lifestyle_sports_result)){
        $result[] = $lifestyle_sports_result;
    }


    /*
    *
    * WEBGAINS DE SEARCHES
    *
    */
    echo '<h3>Webgains DE....</h3>';

    //bstn
    $bstn_result = $webgains_de->search_bstn($webgains_de_csv,$style_code);
    if(!empty($bstn_result)){
        $result[] = $bstn_result;
    }

    //overkill
    $overkill_result = $webgains_de->search_overkill($webgains_de_csv,$style_code);
    if(!empty($overkill_result)){
        $result[] = $overkill_result;
    }

    //sneaker world
    $sneaker_world_result = $webgains_de->search_sneaker_world($webgains_de_csv,$style_code);
    if(!empty($sneaker_world_result)){
        $result[] = $sneaker_world_result;
    }

    //afew store
    $afew_result = $webgains_de->search_afew($webgains_de_csv,$style_code);
    if(!empty($afew_result)){
        $result[] = $afew_result;
    }

    //allike store
    $allike_result = $webgains_de->search_allike($webgains_de_csv,$style_code);
    if(!empty($allike_result)){
        $result[] = $allike_result;
    }

    // sneaker studio
    $sneaker_studio_result = $webgains_de->search_sneaker_studio($webgains_de_csv,$style_code);
    if(!empty($sneaker_studio_result)){
        $result[] = $sneaker_studio_result;
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

          echo 'CJ needs to wait 60 seconds to avoid rate limiting...<br>';
          ob_flush(); flush();
          sleep(60);
          $cj_count = 0;
      }
    } else {
      echo 'Couldnt search CJ since object was not created';
    }




    echo '<br><br>';

    if(sizeOf($result) != 0){

      echo '<strong>'.sizeof($result).'</strong>';
      echo ' Retailers Matched<br><br>';

      $past_retailers_arr = array();
      $found_retailers = array();
      $all_retailers_arr = array();

      //get past retailer id array
      $past_retailers = get_post_meta($product_id,'mw_all_retailers');
      if($past_retailers){
        $past_retailers_arr = explode("/",$past_retailers[0]);
      }

      // append retailer id to found retailers array if the retailer name exists as a term
      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];
        $found_retailers = append_retailer_id_to_array_if_id_found($single_result['retailer'], $found_retailers, $retailers_id_list);
      }

      // for each past retailer, if it no longer appears in the feeds then add it as out of stock
      if( ! empty($past_retailers_arr) ) {
        foreach ($past_retailers_arr as $past_retailer_id) {

          if((!in_array($past_retailer_id,$found_retailers)) && $past_retailer_id != ''){
            $retailer_name = array_search($past_retailer_id, $retailers_id_list);

            if( ! $retailer_name ) {
              echo '<br>Couldnt find a retailer name for the past retailer with ID '.$past_retailer_id.'.<br>';
              continue;
            }
            echo 'past retailer: '.$retailer_name.' was not present in the feeds<br>';
            array_push($result, array(array(
              'retailer'      => $retailer_name,
              'deeplink'      => '',
              'in_stock'      => false,
              'price'         => '',
              'sale-price'    => ''
            )));
          }
        }
      }
      // we now have a complete list of results including past retailers that no longer appear
      // this list of results includes only retailers found automatically

      // update list of automatically found retailers
      foreach($result as $single_result_wrapper_arr) {
        $single_result = $single_result_wrapper_arr[0];
        $all_retailers_arr = append_retailer_id_to_array_if_id_found($single_result['retailer'], $all_retailers_arr, $retailers_id_list);
      }
      $all_retailers = implode("/", $all_retailers_arr);
      update_post_meta($product_id,'mw_all_retailers',$all_retailers);

      // result object shape to store in post meta
      $result_serialize = array(
        'retailer_id'           => array(),
        'retailer_link'         => array(),
        'stock_status'          => array(),
        'retailer_release_date' => array()
      );

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

$main_time_now = new DateTime();
update_option('mw_last_complete', $main_time_now->getTimestamp());
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

function get_all_retailer_terms() {
  $term_args = array(
    'taxonomy'               => 'retailer',
    'hide_empty'             => false,
    'fields'                 => 'all'
  );
  $term_query = new WP_Term_Query( $term_args );

  $terms = array();
  foreach ( $term_query->terms as $term ) {
      $terms[$term->name] = $term->term_id;
  }

  return $terms;
}

function append_retailer_id_to_array_if_id_found($retailer_name, $array, $id_list) {

  if( array_key_exists( $retailer_name, $id_list ) ){
    if( ! in_array( $id_list[$retailer_name], $array )) {
      $array[] = $id_list[$retailer_name];
    }
    return $array;
  }

  echo "<br>Couldnt find ID for retailer with name ".$retailer_name.". Make sure name matches retailer name in retailers list exactly.<br>";
  return $array;
}

?>
