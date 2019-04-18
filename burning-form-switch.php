<?php

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

  function burn_form_shortcode_handler( $atts ){
    if(function_exists('dc_dcb_dev_content_block') && class_exists("\KuntaAPI\Core\Api")) {
      $burnWarnings = [];
      $after = (new DateTime('NOW'))->format('c');
      $before = (new DateTime('+1 day'))->format('c');
      foreach (\KuntaAPI\Core\CoreSettings::getOrganizationIds() as $organizationId) {
        $environmentalWarnings = \KuntaAPI\Core\Api::getEnvironmentalWarningsApi(false)->listOrganizationEnvironmentalWarnings($organizationId, 0, "forest-fire-weather,grass-fire-weather", $before, $after);
        $burnWarnings = array_merge($burnWarnings, $environmentalWarnings);
      }

      if (sizeof($burnWarnings) < 1) {
        return do_shortcode('[dcb name=risujenpoltto-sallittu]');
      } else {
        return do_shortcode('[dcb name=risujenpoltto-kielletty]');
      }
    }
  }

  add_shortcode( 'risujenpoltto_lomake', 'burn_form_shortcode_handler' );
?>