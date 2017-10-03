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

<h1>Afilliate Link Finder<h1>

<h3>Script last run (GMT): </h3>
<p><?php
  if(get_option('mw_last_run')){
    echo gmdate("Y-m-d H:i:s", get_option('mw_last_run'));
  }
  ?>
</p>

<a href="<?php echo AFFILIATE_LINK_FINDER_ROOT  . 'log.txt'; ?>">See log</a>

<form method="post" action="">
  <input value="Run Now" type="submit" name='run_affiliate_link_finder' class="btn" />
</form>

<p>This could potentially take a couple of hours..</p>

<?php

if(isset($_POST['run_affiliate_link_finder'])){

  include_once AFFILIATE_LINK_FINDER_ROOT  . 'admin/partials/main.php';

  if(mw_main($mw_echo = true) === true) {
    $time_now = new DateTime();
    update_option('mw_last_run', $time_now->getTimestamp());
  } else {
    echo 'Failed... Check log';
  }
}

?>
