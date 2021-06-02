<?php
global $wpdb;

$mailchimp_interests = $this->MailChimp->get('lists/' . $this->mailchimp_list_id . '/interest-categories/' . $this->mailchimp_interest_category_id . '/interests', [ 'count' => 100 ]);

// echo '<pre>'; print_r( $mailchimp_interests ); exit;

$mailchimp_interests = $mailchimp_interests ? wp_list_pluck( $mailchimp_interests['interests'], 'name', 'id' ) : [];

$lists_query = "SELECT * FROM {$wpdb->prefix}observer_lists WHERE 1 = 1";
if ( isset( $_GET['status'] ) && '' != $_GET['status'] ) {
  $lists_query .= " AND status = '{$_GET['status']}' ";
}
$lists = $wpdb->get_results( $lists_query );

if ( $lists ) :
  if ( ! isset( $_GET['status'] ) ) :
    $list_interests = wp_list_pluck( $lists, 'interest_id' );
    $unassigned_mc_groups = array_diff( array_keys( $mailchimp_interests ), $list_interests );
    if ( count( $unassigned_mc_groups ) > 0 ) :
  ?>
    <h1 style="color: red;">Unassigned MailChimp Groups</h1>
    <ul style="color: red;">
  <?php foreach ( $unassigned_mc_groups as $group_id ) : ?>
      <li><?php echo $mailchimp_interests[ $group_id ]; ?></li>
  <?php endforeach; // For Each $unassigned_mc_groups ?>
  <?php
    endif; // If $unassigned_mc_groups
  endif;
?>

<p>Total Brag Observer lists<?php echo isset( $_GET['status'] ) ? ' (' .  $_GET['status'] . ')' : ''; ?>: <?php echo count( $lists ); ?></p>
<p>Total MailChimp Groups: <?php echo count( $mailchimp_interests ); ?></p>

<div style="display: flex; align-items: center;">
  <h1>Lists</h1>
  <div style="margin-left: 1rem;">
    <a href="<?php echo remove_query_arg( 'status' ); ?>">All</a>
    <a href="<?php echo add_query_arg( 'status', 'active' ); ?>">Active</a>
    <a href="<?php echo add_query_arg( 'status', 'soon' ); ?>">Soon</a>
  </div>
</div>
<table class="widefat">
  <thead>
    <th>&nbsp;</th>
    <th>Image</th>
    <th>Title / Desc</th>
    <th>Keywords</th>
    <th>Frequency</th>
    <th>Status</th>
    <th>MailChimp Group</th>
    <th>Categories</th>
    <th colspan="2">&nbsp;</th>
  </thead>
  <tbody>
<?php foreach ( $lists as $index => $list ) : ?>
  <tr style="border-top: 1px solid #ddd;">
    <td><?php echo ( $index + 1 ); ?></td>
    <td><img src="<?php echo $list->image_url ? $list->image_url : 'https://dummyimage.com/200x200/ccc/333.jpg&text=' . $list->title; ?>" width="70"></td>
    <td>
      <strong><?php echo $list->title; ?></strong>
      <?php if ( $list->slug ) : ?>
        <a href="<?php echo home_url( 'observer/' . $list->slug ); ?>" target="_blank" style="background: #333; color: #fff; padding: .25rem">/observer/<?php echo $list->slug; ?></a>
      <?php endif; ?>
      <?php echo wpautop( $list->description ); ?>
      <?php // if ( ! is_null( $list->welcome_email_intro ) && '' != trim( $list->welcome_email_intro ) ): ?>
      <!-- <strong>Welcome email</strong> -->
      <!-- <div style="height: 200px; overflow: scroll; border: 1px solid #ccc;">--><?php
      /*
      require_once __DIR__ . '/../classes/email.class.php';
      $email = new Email();

      $welcome_email = $email->getSubscribeConfirmationEmail($list->id);
      $subject = $welcome_email['subject'];
      $body = $welcome_email['body'];
      echo '<div style="text-align: center"><strong>Subject: ' . $subject . '</strong></div>';
      echo $body;
      */
      ?>
      <!-- </div> -->
      <?php // else : ?>
      <!-- <div style="background: red; color: #fff; padding: 1rem; text-align: center;">Welcome email intro not set</div> -->
      <?php // endif; ?>
      <?php
      /*
      if( 'active' == $list->status ) {
        $campaigns = $this->MailChimp->get('search-campaigns', [ 'query' => $list->title]);
        if( $campaigns && isset( $campaigns['results'] ) ) {
          echo '<ul>';
          foreach( $campaigns['results'] as $campaign ) {
            if ( 'sent' == $campaign['campaign']['status'] ) {
            // echo '<pre>'; print_r( $campaign );
              echo '<li><a href="' . $campaign['campaign']['archive_url'] . '" target="_blank">' . $campaign['campaign']['settings']['subject_line'] . '</a></li>';
            }
          }
          echo '</ul>';
        }
      }
      */
      ?>
    </td>
    <td><?php echo $list->keywords; ?></td>
    <td><?php echo $list->frequency; ?></td>
    <td><?php echo $list->status; ?></td>
    <td><?php echo $mailchimp_interests && count( $mailchimp_interests ) > 0 ? $mailchimp_interests[ $list->interest_id ] : ''; ?></td>
    <td>
      <?php
      $categories = $wpdb->get_results( "
        SELECT
          c.title
        FROM
          {$wpdb->prefix}observer_categories c
            JOIN {$wpdb->prefix}observer_list_categories lc
              ON c.id = lc.category_id
        WHERE lc.list_id = '{$list->id}'
      " );
      echo implode( '<br>', wp_list_pluck( $categories, 'title' ) );
      ?>
    </td>
    <td><a href="admin.php?page=brag-observer-manage-list&id=<?php echo $list->id; ?>">Edit</a></td>
    <td><a href="admin.php?page=brag-observer-manage-newsletter&list_id=<?php echo $list->id; ?>">Create Newsletter</a></td>
  </tr>
<?php endforeach; // For Each $list in $lists ?>
  </tbody>
</table>
<?php
endif; // If $lists from database
