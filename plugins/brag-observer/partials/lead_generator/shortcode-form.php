<?php
function observer_lead_generator_form($atts)
{

  global $wpdb;

  $lead_generator_atts = shortcode_atts(array(
    'id' => NULL,
    'background' => '#e9ecef',
    'border' => '#fff',
    'width' => NULL,
    'cta' => 'Sign me up',
    'question1_opts' => NULL
  ), $atts);

  if (is_null($lead_generator_atts['id']))
    return;

  $id = absint($lead_generator_atts['id']);

  $lead_generator = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$id}");

  if (!$lead_generator)
    return;

  ob_start();
?>
  <style>
    .lead_generator-form-wrap {
      background-color: #<?php echo str_replace('#', '', $lead_generator_atts['background']); ?>;
      border: 1px solid #<?php echo str_replace('#', '', $lead_generator_atts['border']); ?>;
      width: <?php echo isset($lead_generator_atts['width']) && !is_null($lead_generator_atts['width']) ? absint($lead_generator_atts['width']) . 'px' : '100%'; ?>;
      max-width: 100%;
      margin: auto;
    }
  </style>

  <?php
  $already_voted = false;

  if (is_user_logged_in()) {
    $current_user = wp_get_current_user();

    $check_vote = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}observer_lead_generator_responses WHERE lead_generator_id = '{$lead_generator->id}' AND user_id = '{$current_user->ID}' LIMIT 1");
    if ($check_vote) {
      $already_voted = true;
    }
  }
  ?>
  <?php if (!$already_voted) : ?>
    <form class="lead_generator-form" method="post">
      <div class="lead_generator-form-wrap my-3 p-2 p-md-3 rounded">
        <div class="lead_generator-wrap">
          <input type="hidden" name="id" id="lead_generator<?php echo $lead_generator->id; ?>-id" value="<?php echo $lead_generator->id; ?>">
          <div class="row">
            <?php echo $lead_generator_atts['question1_opts']; ?>

            <?php if ($lead_generator->question1 && '' != trim($lead_generator->question1) && is_null($lead_generator_atts['question1_opts'])) : ?>
              <div class="col-12">
                <label for="lead_generator<?php echo $lead_generator->id; ?>-response1"><?php echo $lead_generator->question1; ?></label>
                <textarea name="response1" id="lead_generator<?php echo $lead_generator->id; ?>-response1" class="form-control mt-1" placeholder="Write your response here"></textarea>
              </div>
            <?php else : ?>
              Yoooo
            <?php endif; ?>

            <?php if (!is_user_logged_in()) : ?>
              <div class="col-12 mt-1">
                <label for="lead_generator<?php echo $lead_generator->id; ?>-email">Please enter your email</label>
                <input type="email" name="email" id="lead_generator<?php echo $lead_generator->id; ?>-email" class="form-control" placeholder="Your email address">
              </div>
            <?php endif; // If user is NOT logged in 
            ?>

            <?php if ($lead_generator->consent_promotional_marketing_text && '' != trim($lead_generator->consent_promotional_marketing_text)) : ?>
              <div class="col-12 mt-1">
                <label class="d-flex align-items-stretch">
                  <span class="mr-1" style="margin-right: 1rem;"><input type="checkbox" name="consent_promotional_marketing" id="lead_generator<?php echo $lead_generator->id; ?>-consent_promotional_marketing" value="1" checked></span>
                  <span><?php echo $lead_generator->consent_promotional_marketing_text; ?></span>
                </label>
              </div>
            <?php endif; ?>

            <?php if ($lead_generator->footer_text && '' != trim($lead_generator->footer_text)) : ?>
              <div class="col-12 mt-1">
                <p style="font-size: .8rem;"><?php echo $lead_generator->footer_text; ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="d-flex">
          <div class="col-12 d-flex flex-column">
            <div class="loading" style="display: none;">
              <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
              </div>
            </div>
            <div class="<?php echo !is_user_logged_in() ? 'my-1' : 'mb-1'; ?> text-center">
              <div class="js-errors"></div>
              <div class="js-success"></div>
            </div>
            <button type="submit" name="lead_generator-submit" class="lead_generator-submit btn btn-danger"><?php echo $lead_generator_atts['cta']; ?></button>
          </div>
        </div>
      </div>
    </form>
  <?php else : ?>
    <div class="lead_generator-form-wrap my-3 p-3 rounded">
      <div class="text-center alert alert-success">
        <?php echo !is_null($lead_generator->msg_thanks) ? $lead_generator->msg_thanks : 'Thank you for providing feedback!'; ?>
      </div>
    </div>
  <?php endif; ?>
<?php
  $form_html = ob_get_contents();
  ob_end_clean();
  return $form_html; // wpautop( "ID = {$lead_generator->id}" );
}
