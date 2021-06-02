<?php
class Referral extends BragObserver {

  public function __construct() {

    parent::__construct();

    // Admin menu
    add_action('admin_menu', array( $this, '_admin_menu' ) );

  }

  public function _admin_menu() {

    add_submenu_page(
      $this->plugin_slug,
      'Referral',
      'Referral',
      'edit_posts',
      $this->plugin_slug .'-referrals',
      [ $this, 'show_referrals' ]
    );

  }

  public function show_referrals() {

    wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

    $users = get_users( [
      'meta_key' => 'referrals_count',
      'compare' => 'EXISTS',
    ]);

  ?>
    <h1>Referrals</h1>
    <table class="table-sm table-hover table-bordered">
      <tr>
        <th>User email</th>
        <th>Referrals count</th>
      </tr>
  <?php
    if ( $users ) {


  ?>

  <?php foreach( $users as $user ) { ?>
      <tr>
        <th><?php echo $user->user_email; ?></th>
        <td><?php echo get_user_meta( $user->ID, 'referrals_count', true ); ?></td>
      </tr>
  <?php } // For Each $user ?>
  <?php } // If $users ?>
    </table>
  <?php
  }

}
new Referral();
