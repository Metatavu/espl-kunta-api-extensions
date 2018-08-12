<?php

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

  function burn_form_shortcode_handler( $atts ){
    if(function_exists('dc_dcb_dev_content_block')) {
      $formEnabled = get_option( 'burn_form_enabled', 'false' );
      if ($formEnabled == 'false') {
        return do_shortcode('[dcb name=risujenpoltto-kielletty]');
      } else {
        return do_shortcode('[dcb name=risujenpoltto-sallittu]');
      }
    }
  }

  add_shortcode( 'risujenpoltto_lomake', 'burn_form_shortcode_handler' );

  function render_form_switcher() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      update_option('burn_form_enabled', $_POST['burn-form-visibility']);
      do_action('edit_post', 439);
      echo '<script type="text/javascript">window.location="admin.php?page=burn-form-switcher.php";</script>"';
    } else {
      $formEnabled = get_option( 'burn_form_enabled', 'false' );
      echo '<h1>Risujen- ja puutarhajätteen polttaminen</h1>';
      echo '<form method="POST" action="admin.php?page=burn-form-switcher.php">';
      if ($formEnabled == 'false') {
        echo '<p>Ilmoituslomake näkyvissä <input type="radio" name="burn-form-visibility" value="true"></p>';
        echo '<p>Ilmoituslomake EI näkyvissä <input type="radio" checked="checked" name="burn-form-visibility" value="false"></p>';
      } else {
        echo '<p>Ilmoituslomake näkyvissä <input type="radio" checked="checked" name="burn-form-visibility" value="true"></p>';
        echo '<p>Ilmoituslomake EI näkyvissä <input type="radio" name="burn-form-visibility" value="false"></p>';     
      }
      submit_button();
      echo '</form>';
    }
  }

  function burn_form_custom_menu() {
    add_menu_page(
      'Risujen- ja puutarhajätteen polttaminen- lomake',
      'Risujen polttoilmoitus',
      'manage_burn_form',
      'burn-form-switcher.php',
      'render_form_switcher'
    );
  }

  add_action( 'admin_menu', 'burn_form_custom_menu' );

?>