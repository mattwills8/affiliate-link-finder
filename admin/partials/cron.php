<?php

$mw_cron_time = 1510023600;

//SETUP CRON TASKS
add_action( 'mw_affiliate_link_finder_cron_hook_awin', 'get_awin_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_awin' ) ) {
   wp_schedule_event( $mw_cron_time, 'daily', 'mw_affiliate_link_finder_cron_hook_awin' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_webgains', 'get_webgains_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_webgains' ) ) {
   wp_schedule_event( $mw_cron_time + 2 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook_webgains' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_webgains_de', 'get_webgains_de_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_webgains_de' ) ) {
   wp_schedule_event( $mw_cron_time + 4 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook_webgains_de' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_end', 'get_end_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_end' ) ) {
   wp_schedule_event( $mw_cron_time + 8 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook_end' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_kickgame', 'get_kickgame_feed' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_kickgame' ) ) {
   wp_schedule_event( $mw_cron_time + 12 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook_kickgame' );
}

add_action( 'mw_affiliate_link_finder_cron_hook', 'run_mw_main' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook' ) ) {
   wp_schedule_event( $mw_cron_time + 15 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook' );
}

add_action( 'mw_affiliate_link_finder_cron_hook_run_kickgame', 'run_mw_kickgame_main' );
if ( ! wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook_run_kickgame' ) ) {
   wp_schedule_event( $mw_cron_time + 75 * 60, 'daily', 'mw_affiliate_link_finder_cron_hook_run_kickgame' );
}

//unschedule next
/*$timestamp = wp_next_scheduled( 'mw_affiliate_link_finder_cron_hook' );
wp_unschedule_event( $timestamp, 'mw_affiliate_link_finder_cron_hook' );*/

//see events
//echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';

?>
