<?php

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

  function burn_form_shortcode_handler( $atts ){
    if(class_exists("\KuntaAPI\Core\Api")) {
      $burnWarnings = [];
      $after = (new DateTime('NOW'))->format('c');
      $before = (new DateTime('+1 day'))->format('c');
      foreach (\KuntaAPI\Core\CoreSettings::getOrganizationIds() as $organizationId) {
        $environmentalWarnings = \KuntaAPI\Core\Api::getEnvironmentalWarningsApi(false)->listOrganizationEnvironmentalWarnings($organizationId, 0, "forest-fire-weather,grass-fire-weather", $before, $after);
        $burnWarnings = array_merge($burnWarnings, $environmentalWarnings);
      }

      if (sizeof($burnWarnings) < 1) {
        return '<iframe frameborder="0" height="1100" scrolling="no" src="https://www.webropolsurveys.com/S/1045CD621017B2E4.par" width="800"></iframe>';
      } else {
        return '<p style="font-weight: bold;color:#ff0000;">Ilmoitusta risujen/puutarhajätteen poltosta ei voida tehdä voimassa olevan ruohikko- tai metsäpalovaroituksen vuoksi.</p>';
      }
    }
  }

  add_shortcode( 'risujenpoltto_lomake', 'burn_form_shortcode_handler' );
?>
