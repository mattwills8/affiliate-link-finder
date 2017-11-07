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
echo "##################STARTING PROCESS################## <br>";
echo "##################".gmdate("Y-m-d H:i:s", $main_time_now->getTimestamp())."################## <br>";

require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/affilinet/affilinet.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/awin/awin.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/cj/cj.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/end/end.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/kickgame/kickgame.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains/webgains.php';
require_once AFFILIATE_LINK_FINDER_ROOT  . 'includes/affiliate-link-finder/affiliates/webgains-de/webgains-de.php';

$retailers_id_list = [
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

      echo '<strong>'.sizeof($result).'</strong>';
      echo ' Retailers Matched<br><br>';

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
