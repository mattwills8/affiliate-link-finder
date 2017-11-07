<?php

//SETUP CRON TASKS
add_action( 'mw_affiliate_link_finder_cron_hook_awin', 'get_awin_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_awin' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60), 'daily', 'mw_affiliate_link_finder_cron_hook_awin' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_webgains', 'get_webgains_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_webgains' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60) + 2, 'daily', 'mw_affiliate_link_finder_cron_hook_webgains' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_webgains_de', 'get_webgains_de_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_webgains_de' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60) + 4, 'daily', 'mw_affiliate_link_finder_cron_hook_webgains_de' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_end', 'get_end_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_end' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60) + 8, 'daily', 'mw_affiliate_link_finder_cron_hook_end' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_kickgame', 'get_kickgame_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_kickgame' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60) + 12, 'daily', 'mw_affiliate_link_finder_cron_hook_kickgame' );
}

add_action( 'mw_affiliate_link_finder_cron_hook', 'mw_main' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook' ) ) {
   wp_schedule_event( time() + ( 3 * 60 * 60) + 15, 'daily', 'mw_affiliate_link_finder_cron_hook' );
}
//unschedule next
/*$timestamp = wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook' );
wp_unschedule_event( $timestamp, 'mw_affiliate_link_finder_cron_hook' );*/

//see events
//echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';

?>
