<?php

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );
  
  function check_burn_form_pages() {
    $burnFormVisible = is_burn_form_enabled();
    $burnFormStatus = $burnFormVisible == true ? "visible" : "hidden";
    $burnFormVisibility = get_option( "burn-form-visibility" );

    if ( $burnFormStatus == $burnFormVisibility ) {
      return;
    }

    $postslist = get_posts( array(
      'post_type' => 'any',
      'meta_query' => array(
          array(
              'key'   => 'burn-form-in-use',
              'value' => 'true'
          )
      )
    ));

    foreach ( $postslist as $post ) {
      error_log("Updating burn form visibility in post with id " . $post->ID);
      wp_update_post( $post );
    }

    update_option("burn-form-visibility", $burnFormStatus);
  }

  add_action('burn-form-pages-check', 'check_burn_form_pages');

  function is_burn_form_enabled() {
    $formEnabled = get_option( 'burn_form_enabled', 'false' );
    $showForm = true;
    if ($formEnabled == 'false') {
      $showForm = false;
    } else if ($formEnabled == 'true') {
      $showForm = true;
    } else {
      if(class_exists("\KuntaAPI\Core\Api")) {
        $burnWarnings = [];
        $after = (new DateTime('NOW'))->format('c');
        $before = (new DateTime('+1 day'))->format('c');
        foreach (\KuntaAPI\Core\CoreSettings::getOrganizationIds() as $organizationId) {
          $environmentalWarnings = \KuntaAPI\Core\Api::getEnvironmentalWarningsApi(false)->listOrganizationEnvironmentalWarnings($organizationId, 0, "forest-fire-weather,grass-fire-weather", $after, $before);
          $burnWarnings = array_merge($burnWarnings, $environmentalWarnings);
        }

        $showForm = sizeof($burnWarnings) < 1;
      }
    }

    if ($showForm) {
      return true;
    } else {
      return false;
    }
  }

  function burn_form_shortcode_handler( $atts ){
    update_post_meta( get_the_ID(), "burn-form-in-use", "true");
    if (is_burn_form_enabled()) {
      return '<iframe frameborder="0" height="1100" scrolling="no" src="https://www.webropolsurveys.com/S/1045CD621017B2E4.par" width="800"></iframe>';
    } else {
      return '<p style="font-weight: bold;color:#ff0000;">Ilmoitusta risujen/puutarhajätteen poltosta ei voida tehdä voimassa olevan ruohikko- tai metsäpalovaroituksen vuoksi.</p>';
    }
  }

  add_shortcode( 'risujenpoltto_lomake', 'burn_form_shortcode_handler' );

  function render_form_switcher() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      update_option('burn_form_enabled', $_POST['burn-form-visibility']);
      echo '<script type="text/javascript">window.location="admin.php?page=burn-form-switcher.php";</script>"';
    } else {
      $formEnabled = get_option( 'burn_form_enabled', 'false' );
      ?>

      <h1>Risujen- ja puutarhajätteen polttaminen</h1>
      <form method="POST" action="admin.php?page=burn-form-switcher.php">
      <p>Ilmoituslomake AINA näkyvissä <input type="radio" name="burn-form-visibility" value="true" <?php echo ($formEnabled == 'true') ?  "checked" : "" ;  ?>></p>
      <p>Ilmoituslomake automaattinen <input type="radio" name="burn-form-visibility" value="auto" <?php echo ($formEnabled == 'auto') ?  "checked" : "" ;  ?>></p>
      <p>Ilmoituslomake EI näkyvissä <input type="radio" name="burn-form-visibility" value="false" <?php echo ($formEnabled == 'false') ?  "checked" : "" ;  ?>></p>
      <?php submit_button(); ?>
      </form>

      <?php
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
