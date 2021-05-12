<?php
global $wpdb;
// require_once __DIR__ . '/../classes/MailChimp.php';
// $MailChimp = new DrewM\MailChimp\MailChimp( $this->mailchimp_api_key );
$mailchimp_interests = $this->MailChimp->get('lists/' . $this->mailchimp_list_id . '/interest-categories/' . $this->mailchimp_interest_category_id . '/interests', [ 'count' => 100 ]);
// echo '<pre>'; print_r( $mailchimp_interests ); exit;
if ( $mailchimp_interests ) {
  $mailchimp_interests = wp_list_pluck( $mailchimp_interests['interests'], 'name', 'id' );
}

// $t = $this->MailChimp->get('lists/' . $this->mailchimp_list_id . '/interest-categories/' . $this->mailchimp_interest_category_id . '/interests/c2b139c67e', [ 'count' => 50 ]);
// c2b139c67e
// echo '<pre>'; print_r( $t); exit;

$errors = isset( $_SESSION['errors'] ) ? $_SESSION['errors'] : [];
$formdata = isset( $_SESSION['formdata'] ) ? $_SESSION['formdata'] : [];

// var_dump( $_GET );
if ( isset( $_GET['id'] ) ) :
  if ( count( $formdata ) == 0 ):
    $observer_id = (int) $_GET['id'];
    $formdata = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = '{$observer_id}' LIMIT 1", ARRAY_A );

    $categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_list_categories WHERE list_id = '{$observer_id}'" );
    $formdata['category'] = wp_list_pluck( $categories, 'category_id' );

    // echo '<pre>'; print_r( $formdata ); exit;

  endif;
endif;
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

<h1>
<?php if ( isset( $_GET['id'] ) && $_GET['id'] > 0 ) : ?>
  Edit list &quot;<?php echo $formdata['title']; ?>&quot; - Brag Observer
<?php else : ?>
Add list - Brag Observer
<?php endif; ?>
</h1>

<?php
if ( count( $errors ) > 0 ) :
?>
<ul class="alert alert-danger">
  <?php foreach ( $errors as $error ) : ?>
    <li><?php echo $error; ?></li>
  <?php endforeach; ?>
</ul>
<?php
unset( $_SESSION['errors'] );
endif;
?>

<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" class="container-fluid">
  <input type="hidden" name="action" value="manage_observer_list">
  <?php if ( isset( $_GET['id'] ) && $_GET['id'] > 0 ) : ?>
  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
  <?php endif; // $observer_id?>
  <div class="row">
    <div class="col-md-4 mt-3">
      <label>Title</label>
      <input type="text" name="title" class="form-control" value="<?php echo isset( $formdata['title'] ) ? $formdata['title'] : ''; ?>">
    </div>

    <div class="col-md-4 mt-3">
      <label>Slug</label>
      <input type="text" name="slug" class="form-control" value="<?php echo isset( $formdata['slug'] ) ? $formdata['slug'] : ''; ?>">
    </div>

    <div class="col-md-4 mt-3">
      Categories
      <div>
        <?php
        // echo '<pre>'; print_r( $formdata ); exit;
         foreach( $this->categories as $category ) : ?>
          <label>
            <input type="checkbox" name="category[]" id="category_<?php echo $category->id; ?>" value="<?php echo $category->id; ?>"<?php echo isset( $formdata['category'] ) && in_array( $category->id, $formdata['category'] ) ? ' checked' : ''; ?>>
            <?php echo $category->title; ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-md-6 mt-3">
      <label>Keywords <small>Comma separated</small></label>
      <input type="text" name="keywords" class="form-control" value="<?php echo isset( $formdata['keywords'] ) ? $formdata['keywords'] : ''; ?>">
    </div>

    <div class="col-md-6 mt-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?php echo isset( $formdata['description'] ) ? $formdata['description'] : ''; ?></textarea>
    </div>

    <div class="col-md-6 mt-3">
      <label>Image URL</label>
      <input name="image_url" class="form-control" value="<?php echo isset( $formdata['image_url'] ) ? $formdata['image_url'] : ''; ?>">
    </div>

    <div class="col-md-6 mt-3">
      <label>Email Header Image URL</label>
      <input name="email_header_image_url" class="form-control" value="<?php echo isset( $formdata['email_header_image_url'] ) ? $formdata['email_header_image_url'] : ''; ?>">
    </div>

    <div class="col-md-4 mt-3">
      <label>MailChimp Interest</label>
      <select name="interest_id" class="form-control">
        <?php
        if ( $mailchimp_interests ) :
          foreach ( $mailchimp_interests as $interest_id => $interest_name ) :
        ?>
        <option value="<?php echo $interest_id; ?>"<?php echo isset( $formdata['interest_id'] ) && $interest_id == $formdata['interest_id'] ? ' selected' : ''; ?>><?php echo $interest_name; ?></option>
        <?php
          endforeach; // For Each $mailchimp_interests
        endif; // if $mailchimp_interests
        ?>
      </select>
    </div>

    <div class="col-md-4 mt-3">
      <label>Frequency</label>
      <select name="frequency" class="form-control">
      <?php
      $frequency_opts = [
        'Daily',
        'Weekly',
        'Fortnightly',
        'Monthly',
        'Breaking News',
      ];
      foreach ( $frequency_opts as $frequency_opt ) :
      ?>
        <option value="<?php echo $frequency_opt; ?>"<?php echo isset( $formdata['frequency'] ) && $frequency_opt == $formdata['frequency'] ? ' selected' : ''; ?>><?php echo $frequency_opt; ?></option>
      <?php endforeach; // For Each $frequency_opts ?>
      </select>
    </div>

    <div class="col-md-4 mt-3">
      <label>Status</label>
      <select name="status" class="form-control">
      <?php
      $status_opts = [
        'active',
        'soon',
      ];
      foreach ( $status_opts as $status_opt ) :
      ?>
        <option value="<?php echo $status_opt; ?>"<?php echo isset( $formdata['status'] ) && $status_opt == $formdata['status'] ? ' selected' : ''; ?>><?php echo $status_opt; ?></option>
      <?php endforeach; // For Each $status_opts ?>
      </select>
    </div>

    <!-- <div class="col-12 mt-3">
      <label>Welcome email intro</label>
    <?php
    /*
      $wpeditor_settings = array(
        // 'teeny' => true,
        'textarea_rows' => 15,
        'tabindex' => 1,
        'tinymce'       => array(
          'toolbar1' => 'formatselect,bold,italic,underline,separator,bullist,numlist,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo,removeformat',
        'toolbar2'      => '',
        'toolbar3'      => '',
    ),
      );
      wp_editor( isset( $formdata['welcome_email_intro'] ) ? $formdata['welcome_email_intro'] : '', 'welcome_email_intro', $wpeditor_settings);
      */
    ?>
    </div>

    <div class="col-12 mt-3">
      <label>Welcome email outro</label>
      <?php // wp_editor( isset( $formdata['welcome_email_outro'] ) ? $formdata['welcome_email_outro'] : '', 'welcome_email_outro', $wpeditor_settings); ?>
    </div> -->

    <div class="col-12 mt-3">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
</form>
<?php unset( $_SESSION['formdata'] );
