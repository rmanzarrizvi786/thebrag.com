<?php
/**
 * Plugin Name: OutVoice
 * Plugin URI: https://outvoice.com
 * Description: Plugin for interfacing with OutVoice
 * Author: OutVoice
 * Version: 1.2.3
 * License: GPLv2 or later
 */


if( ! defined( 'ABSPATH') ) {
    exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-outvoiceapi.php');

function outvoice_enqueue_assets() {
    wp_enqueue_script(
      'outvoice-sidebar',
      plugins_url( 'build/index.js', __FILE__ ),
      array( 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' )
    );
    // check login status.
    $status = 1;
    $token  = outvoice_token_check();
    $tokens = outvoice_retrieve_tokens();
    if ( ! $token ) {
      $status = false;
    }
    $data = array(
      'status' => $status,
    );
    $message = "You are not logged in.";
    if ( ! empty( $tokens['access'] ) ) {
      $data['token'] = $tokens['access'];
      $message = "You are logged in as " . $token['username'];
    }
    $data['message'] = $message;
    $data['contributors'] = outvoice_list_contributors();
    wp_localize_script( 'outvoice-sidebar', 'outvoiceData', $data );
    
    wp_register_style( 'outvoice-react-css', plugin_dir_url( __FILE__ ) . 'css/outvoice-react.css', false, '1.0.2' );
	  wp_enqueue_style( 'outvoice-react-css' );
  }
  add_action( 'enqueue_block_editor_assets', 'outvoice_enqueue_assets' );


/**
 * Set meta box
 *
 * @param string $post OutVoice meta box.
 */
function outvoice_custom_meta_boxes( $post ) {
	if ( outvoice_show() ) {
		add_meta_box(
			'outvoice-meta-box',
			__( 'OutVoice' ),
			'render_outvoice_meta_box',
			'post',
			'side',
			'high'
		);
	}
}

/**
 * Render Meta Box
 */
function render_outvoice_meta_box() {
	print wp_kses( outvoice_fields(), array() );
}

add_action( 'post_submitbox_misc_actions', 'outvoice_fields' );
/**
 * Set Admin fields
 *
 * @param null $class Sets class for outvoice fields.
 *
 * @return string
 */
function outvoice_fields( $class = null ) {

  if ('post' === get_post_type()) {

    if ( ! outvoice_show() ) {
      return 'This post has already been published';
    }
  
    $token   = outvoice_token_check();
    $options = outvoice_list_freelancers();

    if ( $token ) { ?>
      <div class="outvoice-options">
        <div class="outvoice-options-title">
          <div class="outvoice-title"><img class="outvoice-logo" src="<?php print esc_url( plugin_dir_url( __FILE__ ) ); ?>img/logo_166.png"></div>
        </div>
        <div class="outvoice-options-body">

          <div class="outvoice-body-wrapper"></div>

          <input type="hidden" id="outvoice_post_nonce" name="outvoice_post_nonce" value="<?php print wp_kses( wp_create_nonce( 'outvoice_post' ), array() ); ?>">

          <div class="outvoice-username">You are logged in
            as <?php echo wp_kses( $token['username'], array() ); ?></div>

          <div><a class="outvoice-add-contributor">add a contributor</a></div>

          <div id="outvoice-contrib">
            <div class="outvoice-options-row freelancer">
              <select name="outvoice-freelancer" id="ov-combobox" data-options="prompt:'Select contributor...'">
                <?php
                print wp_kses(
                  $options,
                  array(
                    'option' => array(
                      'value'    => array(),
                      'selected' => array(),
                    ),
                  )
                );
                ?>
              </select>
            </div>
            <div class="outvoice-options-row payment">
              <select name="outvoice-currency" id="outvoice-currency">
                <option value='USD'>USD</option>
              </select>
              <span class="outvoice-currency-symbol">$</span>
              <input class="ov-amount" type="text" size="9" name="outvoice-amount">
                          <input type="hidden" value="" name="ov-paid">
            </div>
          </div>

          <div><a class="outvoice-add-contributor-1">add contributor</a></div>

          <div id="outvoice-contrib-1">
            <hr>
            <div class="outvoice-options-row freelancer">
              <select name="outvoice-freelancer-1" id="ov-combobox-1">
                <?php
                print wp_kses(
                  $options,
                  array(
                    'option' => array(
                      'value'    => array(),
                      'selected' => array(),
                    ),
                  )
                );
                ?>
              </select>
            </div>
            <div class="outvoice-options-row payment">
              <select name="outvoice-currency-1">
                <option value='USD'>USD</option>
              </select>
              <span class="outvoice-currency-symbol">$</span>
              <input class="ov-amount" type="text" size="9" name="outvoice-amount-1">
            </div>
          </div>

          <div><a class="outvoice-no-contributor">X</a></div>

        </div>
      </div>
      <?php
    } else {
      ?>
      <div class="outvoice-options">
        <div class="outvoice-options-title">
          <div class="outvoice-title"><img class="outvoice-logo" src="<?php print esc_url( plugin_dir_url( __FILE__ ) ); ?>img/logo_166.png"></div>
        </div>
        <div class="outvoice-options-body">
          <div class="outvoice-body-wrapper"></div>
          <div>
            <label class="ov-form" for="outvoice-user">Email:</label>
            <input class="ov-form" type="text" name="outvoice-user">
            <label  class="ov-form" for="outvoice-pass">Pass:</label>
            <input class="ov-form ov-password" type="password" name="outvoice-pass">
          </div>
          <div>
            <a class="button ov-form ov-login">Log In</a>
            <a class="button ov-form ov-show">Log In</a>
          </div>
          <p id="outvoice-bypass-text">To publish without using OutVoice <a id="outvoice-bypass">click here</a>.</p>
        </div>

      </div>
      <?php
    }
  }
}

// classic tinyMCE editor.
add_action( 'media_buttons_context', 'outvoice_mce_before_init' );

/**
 * Add script
 */
function outvoice_mce_before_init() {
  if ('post' === get_post_type() && is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
    wp_enqueue_script( 'outvoice', plugin_dir_url( __FILE__ ) . 'js/outvoice.js', array( 'ov-combobox' ), '1.1.0', false );
    // check login status.
    $status = 1;
    $token  = outvoice_token_check();
    if ( ! $token ) {
      $status = false;
    }
    $data = array(
      'status' => $status,
      'nonce'  => wp_create_nonce( 'outvoice_post' ),
    );
    wp_localize_script( 'outvoice', 'outvoiceData', $data );
  }
  else {
    return false;
  }
}

/**
 * Save tokens to DB
 *
 * @param mixed $tokens Save tokens to DB.
 *
 * @return string
 */
function outvoice_save_tokens( $tokens ) {

	$id = get_current_user_id();
	// write tokens to outvoice table.
	$new_tokens = array(
		'access'  => $tokens['access_token'],
		'refresh' => $tokens['refresh_token'],
		'expires' => time() + 3000,
	);
	$serialized = serialize( $new_tokens );
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}outvoice (id, token) VALUES(%d, %s)
          ON DUPLICATE KEY UPDATE id = %d, token = %s",
			$id,
			$serialized,
			$id,
			$serialized
		)
	);
	return 'saved';

}

/**
 * Retrieve tokens from DB
 *
 * @return array|mixed
 */
function outvoice_retrieve_tokens() {
	$id = get_current_user_id();
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( "SELECT token FROM {$wpdb->prefix}outvoice WHERE id = %d", $id ) );
	$tokens = array();
	foreach ( $result as $record ) {
		$tokens = unserialize( $record->token );
	}

	return $tokens;

}

/**
 * Delete tokens from DB
 */
function outvoice_delete_token_entry() {
	$id = get_current_user_id();
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}outvoice WHERE id = %d", $id ) );
	delete_transient( 'outvoice_status' );

}

/**
 * Check status of tokens
 *
 * @return bool|mixed
 */
function outvoice_token_check() {
	$ov     = new Outvoice\Api\Outvoiceapi();
	$tokens = outvoice_retrieve_tokens();
	if ( empty( $tokens['access'] ) ) {
		delete_transient( 'outvoice_status' );
		return false;
	}
	$ov->set_tokens( $tokens['access'], $tokens['refresh'] );
	// reset access token if about to expire.
	if ( ! empty( $tokens['refresh'] ) && ! empty( $tokens['expires'] ) && $tokens['expires'] < time() ) {
		$ov->refresh_tokens();
		// save new tokens.
		$new_tokens = array(
			'access_token'  => $ov->get_access_token(),
			'refresh_token' => $ov->get_refresh_token(),
			'expires'       => $ov->get_token_expires(),
		);
		outvoice_save_tokens( $new_tokens );
		$ov->set_tokens( $new_tokens['access_token'], $new_tokens['refresh_token'] );
	}

	$status = $ov->auth_check();
	if ( 200 !== $status['response_code'] ) {
		delete_transient( 'outvoice_status' );
		return false;
	}
	set_transient( 'outvoice_status', true, 30 * DAY_IN_SECONDS );

	return $status;
}

/**
 * Initial installation
 */
function outvoice_install() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$wpdb->prefix}outvoice (
  id mediumint(9) NOT NULL,
  token longtext,
  PRIMARY KEY  (id)
) $charset_collate;";

	include_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'outvoice_db_version', '1.0' );
}
register_activation_hook( __FILE__, 'outvoice_install' );

/**
 * Add admin scripts
 *
 * @param mixed $hook This is the hook for adding scripts.
 */
function outvoice_admin_script( $hook ) {
	if ( ! outvoice_show() ) {
		return;
	}
	if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        wp_enqueue_script('ov-combobox', plugin_dir_url(__FILE__) . 'js/jquery.ov-combobox.js', array('jquery'), '1.04', false);
        wp_enqueue_script('jquery-easing', plugin_dir_url(__FILE__) . 'js/jquery.easing.min.js', array('jquery'), '1.02', false);
        wp_enqueue_script('latinize', plugin_dir_url(__FILE__) . 'js/latinize.js', array('jquery'), '1.02', false);
        wp_register_style('outvoice_css', plugin_dir_url(__FILE__) . 'css/outvoice.css', false, '1.0.15');
        wp_enqueue_style('outvoice_css');
    }

}
add_action( 'admin_enqueue_scripts', 'outvoice_admin_script' );

/**
 * Init function
 */
function outvoice_settings_init() {
	register_setting( 'outvoice', 'outvoice_options', 'outvoice_options_validate' );

	add_settings_section(
		'outvoice_section_developers',
		__( '', 'outvoice' ),
		'outvoice_section_developers_cb',
		'outvoice'
	);

	$text = 'OutVoice Credentials';
	if ( get_transient( 'outvoice_status' ) ) {
		$text = 'Log out by clicking below';
	}

	add_settings_field(
		'outvoice_field_options',
		__( $text, 'outvoice' ),
		'outvoice_options_callback',
		'outvoice',
		'outvoice_section_developers',
		array(
			'label_for'            => 'outvoice_field_options',
			'class'                => 'outvoice_row',
			'outvoice_custom_data' => 'custom',
		)
	);
}

/**
 * Register our outvoice_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'outvoice_settings_init' );

/**
 * Make sure values go to correct fields.
 *
 * @param mixed $args Validate field entries.
 */
function outvoice_options_validate( $args ) {

	if ( empty( $args['access'] ) || empty( $args['refresh'] ) ) {
		outvoice_delete_token_entry();
		add_settings_error( 'outvoice_settings', 'access', 'You have been logged out.', 'error' );
		return false;
	}
	$ov = new Outvoice\Api\Outvoiceapi();
	$ov->generate_tokens( $args['mail'], $args['pass'] );
	$tokens = array(
		'access_token'  => $ov->get_access_token(),
		'refresh_token' => $ov->get_refresh_token(),
	);
	outvoice_save_tokens( $tokens );
	if ( 'saved' !== $tokens ) {
		add_settings_error( 'outvoice_settings', 'access', 'Please enter a valid username and password.', 'error' );
		return false;
	}

	return $args;
}

/**
 * Fires when a post is published.
 *
 * @param string $new_status The new status of the post.
 * @param string $old_status The old status of the post.
 * @param mixed  $post The post object.
 */
function outvoice_post_published( $new_status, $old_status, $post ) {
	if ( 'publish' === $new_status || 'future' === $new_status ) {
		if ( ! empty( $_POST['outvoice-freelancer'] ) && ! empty( $_POST['outvoice-amount'] ) && ! empty( $_POST['outvoice-currency'] ) ) {

			if ( isset( $_POST['outvoice_post_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['outvoice_post_nonce'] ), 'outvoice_post' ) ) {

			    // check if gutenberg has already paid
                if ( !empty( $_POST['ov-paid'] )) {
                    $paid = sanitize_text_field( wp_unslash( $_POST['ov-paid'] ) );
                    if ('paid' === $paid) {
                        return;
                    }
                }

				$transaction = new Outvoice\Api\Outvoiceapi();
				if ( ! empty( $_POST['ov-user'] ) && ! empty( $_POST['ov-pass'] ) ) {
					$transaction->generate_tokens( sanitize_text_field( wp_unslash( $_POST['ov-user'] ) ), sanitize_text_field( wp_unslash( $_POST['ov-pass'] ) ) );
				} else {
					$tokens = outvoice_retrieve_tokens();
					$transaction->set_tokens( $tokens['access'], $tokens['refresh'] );
				}
				$info = [
					'freelancer' => sanitize_text_field( wp_unslash( $_POST['outvoice-freelancer'] ) ),
					'amount'     => sanitize_text_field( wp_unslash( $_POST['outvoice-amount'] ) ),
					'currency'   => sanitize_text_field( wp_unslash( $_POST['outvoice-currency'] ) ),
					'url'        => $post->guid,
					'title'      => $post->post_title,
				];
				if ( ! empty( $_POST['outvoice-freelancer-1'] ) && ! empty( $_POST['outvoice-amount-1'] ) && ! empty( $_POST['outvoice-currency-1'] ) ) {
					$info['freelancer1'] = sanitize_text_field( wp_unslash( $_POST['outvoice-freelancer-1'] ) );
					$info['amount1']     = sanitize_text_field( wp_unslash( $_POST['outvoice-amount-1'] ) );
					$info['currency1']   = sanitize_text_field( wp_unslash( $_POST['outvoice-currency-1'] ) );
				}
				$transaction->payment( $info );
			}
		}
	}

}
add_action( 'transition_post_status', 'outvoice_post_published', 10, 3 );

/**
 * Sets success notice.
 */
function outvoice_success_notice() {
	if ( get_transient( 'outvoice_success' ) ) {
		?>
	<div class="notice notice-success is-dismissible">
		<p><?php print 'OutVoice payment successful. ' . wp_kses( get_transient( 'outvoice_success' ), array() ); ?>.</p>
	</div>
		<?php
		delete_transient( 'outvoice_success' );
	}
}
add_action( 'admin_notices', 'outvoice_success_notice' );

/**
 * Sets failure notice.
 */
function outvoice_failure_notice() {
	if ( get_transient( 'outvoice_error' ) ) {
		?>
	<div class="notice notice-error is-dismissible">
		<p><?php print 'OutVoice payment failed. ' . wp_kses( get_transient( 'outvoice_error' ), array() ); ?></p>
	</div>
		<?php
		delete_transient( 'outvoice_error' );
	}
}
add_action( 'admin_notices', 'outvoice_failure_notice' );

/**
 * Show outvoice fields based on page and post status.
 *
 * @return bool
 */
function outvoice_show() {
	global $pagenow;
	if ( ( 'post-new.php' !== $pagenow && 'post.php' !== $pagenow ) || ! is_admin() ) {
		return false;
	}
	$post_id = get_the_ID();
	$status  = get_post_status( $post_id );
	if ( 'publish' === $status || 'future' === $status ) {
		return false;
	}
	return true;
}

/**
 * Calls list of all freelancers.
 *
 * @return bool|string
 */
function outvoice_list_freelancers() {
	$freelancers = '';
	$options     = outvoice_retrieve_tokens();
	if ( empty( $options['access'] ) ) {
		return false;
	}
	$list = new Outvoice\Api\Outvoiceapi();
	$list->set_tokens( $options['access'], $options['refresh'] );
	$freelancer_list = $list->list_contributors();
	if ( $freelancer_list ) {
		foreach ( $freelancer_list as $freelancer ) {
			foreach ( $freelancer as $key => $value ) {
				$freelancers .= "<option value='$key'>$value</option>";
			}
		}
	}

	return $freelancers;

}

/**
 * Calls list of all contributors for gutenberg.
 *
 * @return array
 */
function outvoice_list_contributors() {
  $contributors = array();
  $contributors[0]['id'] = 0;
  $contributors[0]['name'] = 'No contributors available';
	$options     = outvoice_retrieve_tokens();
	if ( empty( $options['access'] ) ) {
		return $contributors;
	}
	$list = new Outvoice\Api\Outvoiceapi();
	$list->set_tokens( $options['access'], $options['refresh'] );
  $freelancers = $list->list_contributors();
  $count = 0;
  foreach ($freelancers as $item => $freelancer) {
    foreach ($freelancer as $key => $value);
    $contributors[$count]['id'] = $key;
    $contributors[$count]['name'] = $value;
    $count++;
  }

	return $contributors;

}

/**
 * Make sure user can access OutVoice
 *
 * @param null $user check user role.
 *
 * @return bool
 */
function outvoice_user_role( $user = null ) {
	if ( current_user_can( 'publish_posts' ) ) {
		return true;
	}
	return false;
}


add_action( 'admin_menu', 'outvoice_setup_page' );

/**
 * Page Setup
 */
function outvoice_setup_page() {
	add_menu_page( '', 'Outvoice', 'publish_posts', 'outvoice', 'outvoice_settings', plugin_dir_url( __FILE__ ) . 'img/ov.png', 99 );
}

/**
 * Settings
 */
function outvoice_settings() {
	// check user capabilities.
	if ( ! outvoice_user_role() ) {
		return;
	}

	if ( isset( $_REQUEST['nonce'] ) && ! empty( $_POST ) && wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'outvoice_nonce' ) ) {

		// handle $_POST login info.
		if ( isset( $_POST['ov-submit'] ) ) {
			if ( 'Log Out' === $_POST['ov-submit'] ) {
				outvoice_delete_token_entry();
				add_settings_error( 'outvoice_settings', 'access', 'You have been logged out.', 'error' );
			}
			if ( 'Log In' === $_POST['ov-submit'] ) {

				if ( isset( $_POST['mail'] ) && isset( $_POST['pass'] ) ) {
					$ov = new Outvoice\Api\Outvoiceapi();
					if ( $ov->generate_tokens( sanitize_text_field( wp_unslash( $_POST['mail'] ) ), sanitize_text_field( wp_unslash( $_POST['pass'] ) ) ) ) {
						$tokens = [
							'access_token'  => $ov->get_access_token(),
							'refresh_token' => $ov->get_refresh_token(),
						];
						outvoice_save_tokens( $tokens );
					} else {
						add_settings_error( 'outvoice_settings', 'access', 'Your username or password is incorrect.', 'error' );
					}
				} else {
					add_settings_error( 'outvoice_settings', 'access', 'Please enter a username and password.', 'error' );
				}
			}
		}
	}
	$token_check = outvoice_token_check();
	// header.
	echo '<div class="wrap">';
	settings_errors();
	$logo_url = plugin_dir_url( __FILE__ ) . 'img/outvoice.png';
	echo sprintf( '<img src="%s">', esc_url( $logo_url ) );

	if ( 200 !== $token_check['response_code'] ) {
		// logged out page.
		echo sprintf(
			'<h2>You are not currently logged in. Please enter your OutVoice username and password below.</h2>
          <form action="?page=outvoice&nonce=%s" method="post">
            <div>
            <label for="mail">email:</label><br>
            <input size="40" type="text" name="mail"><br>
              <label for="pass">password:</label><br>
            <input size="40" type="password" name="pass">
            </div>
            <div><br><input class="button button-primary" type="submit" value="Log In" name="ov-submit"></div>
          </form>
          <br>
          <p class="description">
            For security reasons, your login credentials for OutVoice will not be stored in WordPress.<br>This login will provide a token that WordPress can use to connect.
          </p>',
			wp_kses( wp_create_nonce( 'outvoice_nonce' ), array() )
		);
	} else {
		// logged in page.
		echo "<h2 style='margin-bottom:20px;'>You are currently logged in as " . wp_kses( $token_check['username'], array() ) . '.</h2>';
		echo sprintf(
			'
        <form style="margin-bottom:50px;" action="?page=outvoice&nonce=%s" method="post">
            <div>
                <input type="hidden" name="mail">
                <input type="hidden" name="pass">
                <input type="submit" class="button button-primary" value="Log Out" name="ov-submit">
            </div>
            </form>
        ',
			wp_kses( wp_create_nonce( 'outvoice_nonce' ), array() )
		);
		// footer.
		echo '</div>';
	}

}