<?php
/*
 * Created on August 13, 2019
 * Plugin Name: Kunta API ESPL extensions
 * Description: Kunta API extensions for ESPL
 * Version: 0.8
 * Author: Metatavu Oy
 */
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  require_once ('twig-loader.php');
  require_once('burning-form-switch.php');

  function burn_form_cron_schedules($schedules){
    if(!isset($schedules["minutely"])){
      $schedules["minutely"] = array(
        'interval' => 60,
        'display' => __('Once every minute')
      );
    }
    return $schedules;
  }

  add_filter('cron_schedules','burn_form_cron_schedules');

  function burn_form_switcher_activation() {
    if (! wp_next_scheduled ( 'burn-form-pages-check' )) {
      wp_schedule_event(time(), 'minutely', 'burn-form-pages-check');
    }
  }

  function burn_form_switcher_deactivation() {
    wp_clear_scheduled_hook('burn-form-pages-check');
  }
  
  register_deactivation_hook(__FILE__, 'burn_form_switcher_deactivation');
  register_activation_hook(__FILE__, 'burn_form_switcher_activation');
?>
