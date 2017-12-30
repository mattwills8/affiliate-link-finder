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

include_once AFFILIATE_LINK_FINDER_ROOT  . 'admin/partials/main.php';
include_once AFFILIATE_LINK_FINDER_ROOT  . 'admin/partials/main-kickgame.php';

//schedule cron tasks
include_once AFFILIATE_LINK_FINDER_ROOT  . 'admin/partials/cron.php';


?>

<h1>Afilliate Link Finder<h1>

<h3>Script last run (GMT): </h3>
<p><?php
  if(get_option('mw_last_run')){
    echo gmdate("Y-m-d H:i:s", get_option('mw_last_run'));
  }
  ?>
</p>

<h3>Script last fully completed (GMT): </h3>
<p><?php
  if(get_option('mw_last_complete')){
    echo gmdate("Y-m-d H:i:s", get_option('mw_last_run'));
  }
  ?>
</p>

<a href="<?php echo AFFILIATE_LINK_FINDER_ROOT_URL  . 'log.txt'; ?>">See log</a><br>


<form method="post" action="">
  <input value="Get Awin Feed" type="submit" name='run_get_awin_feed' class="btn" />
</form>

<?php if(isset($_POST['run_get_awin_feed'])){
  get_awin_feed();
} ?>


<form method="post" action="">
  <input value="Get Webgains Feed" type="submit" name='run_get_webgains_feed' class="btn" />
</form>

<?php if(isset($_POST['run_get_webgains_feed'])){
  get_webgains_feed();
} ?>


<form method="post" action="">
  <input value="Get Webgains DE Feed" type="submit" name='run_get_webgains_de_feed' class="btn" />
</form>

<?php if(isset($_POST['run_get_webgains_de_feed'])){
  get_webgains_de_feed();
} ?>


<form method="post" action="">
  <input value="Get End Feed" type="submit" name='run_get_end_feed' class="btn" />
</form>

<?php if(isset($_POST['run_get_end_feed'])){
  get_end_feed();
} ?>


<form method="post" action="">
  <input value="Get Kickgame Feed" type="submit" name='run_get_kickgame_feed' class="btn" />
</form>

<?php if(isset($_POST['run_get_kickgame_feed'])){
  get_kickgame_feed();
} ?>

<form method="post" action="">
  <input value="Run Now" type="submit" name='run_affiliate_link_finder' class="btn" />
</form>

<form method="post" action="">
  <input value="Run Kickgame Only" type="submit" name='run_affiliate_link_finder_kickgame' class="btn" />
</form>

<p>This could take a while if running manually... Go make yourself a cuppa'!</p>

<?php

function run_mw_main() {

  if(mw_main() === true) {
    $time_now = new DateTime();
    update_option('mw_last_run', $time_now->getTimestamp());
  } else {
    echo 'Failed... Check log';
  }
}
if(isset($_POST['run_affiliate_link_finder'])){
  run_mw_main();
}


function run_mw_kickgame_main() {

  if(mw_kickgame_main() === true) {
    $time_now = new DateTime();
    update_option('mw_last_run', $time_now->getTimestamp());
  } else {
    echo 'Failed... Check log';
  }
}
if(isset($_POST['run_affiliate_link_finder_kickgame'])){
  run_mw_kickgame_main();
}

?>
