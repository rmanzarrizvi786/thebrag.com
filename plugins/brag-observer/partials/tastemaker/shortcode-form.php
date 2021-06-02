<?php
function observer_tastemaker_form($atts) {

  global $wpdb;

  $tastemaker_atts = shortcode_atts( array(
    'id' => NULL,
    'background' => '#e9ecef',
    'border' => '#fff',
    'width' => NULL
  ), $atts );

  if ( is_null( $tastemaker_atts['id'] ) )
    return;

  $id = absint( $tastemaker_atts['id'] );

  $tastemaker = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_tastemakers WHERE id = {$id}" );

  if ( ! $tastemaker )
    return;

  ob_start();
?>
<style>
.tastemaker-rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}
.tastemaker-rating>input {
  display: none
}

.tastemaker-rating>label {
  position: relative;
  width: 1em;
  font-size: 300%;
  color: #FFD600;
  cursor: pointer
}

.tastemaker-rating>label::before {
  content: "\2605";
  position: absolute;
  opacity: 0
}

.tastemaker-rating>label:hover:before,
.tastemaker-rating>label:hover~label:before {
  opacity: 1 !important
}

.tastemaker-rating>input:checked~label:before {
  opacity: 1
}

.tastemaker-rating:hover>input:checked~label:before {
  opacity: 0.4
}

.tastemaker-form-wrap {
  background-color: #<?php echo str_replace( '#', '', $tastemaker_atts['background'] ); ?>;
  border: 1px solid #<?php echo str_replace( '#', '', $tastemaker_atts['border'] ); ?>;
  width: <?php echo isset( $tastemaker_atts['width'] ) && ! is_null( $tastemaker_atts['width'] ) ? absint( $tastemaker_atts['width'] ) . 'px' : '100%'; ?>;
  max-width: 100%;
  margin: auto;
}

@media only screen and (max-width: 600px) {
  .tastemaker-rating>label {
    /* font-size: 12vw; */
  }
}
</style>

<?php
$already_voted = false;

if ( is_user_logged_in() ) {
  $current_user = wp_get_current_user();

  $check_vote = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}observer_tastemaker_reviews WHERE tastemaker_id = '{$tastemaker->id}' AND user_id = '{$current_user->ID}' LIMIT 1" );
  if ( $check_vote ) {
    $already_voted = true;
  }
}
?>
<?php if ( ! $already_voted ) : ?>
<form class="tastemaker-form" method="post">
  <div class="tastemaker-form-wrap my-3 p-3 rounded">
    <div class="tastemaker-wrap">
      <input type="hidden" name="id" id="tastemaker<?php echo $tastemaker->id; ?>-id" value="<?php echo $tastemaker->id; ?>">
      <div class="row">
        <div class="col-12">
          <div class="tastemaker-rating">
            <?php for ( $i = 5; $i >= 1; $i-- ) : ?>
              <input type="radio" name="rating" value="<?php echo $i; ?>" id="tastemaker<?php echo $tastemaker->id; ?>-rating-<?php echo $i; ?>"><label for="tastemaker<?php echo $tastemaker->id; ?>-rating-<?php echo $i; ?>">☆</label>
            <?php endfor; ?>
          </div>
        </div>

        <div class="col-12 mt-2">
          <textarea name="comments" id="tastemaker<?php echo $tastemaker->id; ?>-comments" class="form-control" placeholder="Write your review here"></textarea>
        </div>

        <?php if ( ! is_user_logged_in() ) : ?>
        <div class="col-12 mt-2">
          <input type="email" name="email" id="tastemaker<?php echo $tastemaker->id; ?>-email" class="form-control" placeholder="Your email address">
        </div>
        <?php endif; // If user is NOT logged in ?>
      </div>
    </div>

    <div class="row">
      <div class="col-12 mt-2">
        <div class="loading" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
        <div class="js-errors"></div>
        <div class="js-success text-center"></div>
        <button type="submit" name="tastemaker-submit" class="tastemaker-submit btn btn-danger">Submit</button>
      </div>
    </div>
  </div>
</form>
<?php else : ?>
  <div class="tastemaker-form-wrap my-3 p-3 rounded">
    <div class="text-center alert alert-success">Thank you for providing feedback!</div>
  </div>
<?php endif; ?>
<?php
  $form_html = ob_get_contents();
  ob_end_clean();
  return $form_html; // wpautop( "ID = {$tastemaker->id}" );
}
