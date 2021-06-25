<?php
class Email
{

  private $observer;
  protected $is_sandbox;

  public function __construct($observer = null)
  {
    if (!is_null($observer)) {
      $this->observer = $observer;
    }
    add_action('phpmailer_init', [$this, '_phpmailer_init']);
  }

  public function _phpmailer_init($phpmailer)
  {
    $phpmailer->isSMTP();

    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;

    $this->is_sandbox = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    if ($this->is_sandbox) {
      $phpmailer->Host       = 'smtp.gmail.com';
      $phpmailer->Username   = 'observer@thebrag.media';
      $phpmailer->Password   = '&E*U6bNa';
    } else {
      $phpmailer->Host       = 'in-v3.mailjet.com';
      $phpmailer->Username   = '5e9f80a265b26eaad4f43305eb4e0bda';
      $phpmailer->Password   = '626b9bb60689f87723183a8a5abf4d2d';
    }

    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From       = 'observer@thebrag.media';
    $phpmailer->FromName   = 'The Brag Observer';

    $phpmailer->IsSMTP();
  }

  /*
  * Send Subscription Welcome Email
  */
  public function sendSubscribeConfirmationEmail($user_id, $sub_lists)
  {
    $user = get_user_by('id', $user_id);
    if (!$user)
      return false;
    $to = $user->user_email;

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag Observer <observer@thebrag.media>';

    $welcome_email = $this->getSubscribeConfirmationEmail($user_id, $sub_lists);
    $subject = $welcome_email['subject'];
    $body = $welcome_email['body'];

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendSubscribeConfirmationEmail

  /*
  * Get Subscribe Welcome Email Subject and Body
  */
  public function getSubscribeConfirmationEmail($user_id, $sub_lists)
  {

    global $wpdb;

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
?>
    <p>Hi there! Thanks for signing up to The Brag Observer newsletters, where the topics you love get the coverage they deserve.</p>
    <p>You'll start getting The Brag Observer newsletters, direct to your inbox. You'll also receive a few breaking news blasts, and exclusive announcements too.</p>
    <p>You are now subscribed to the below;</p>
    <ul>
      <?php foreach ($sub_lists as $sub_list) : ?>
        <li><?php echo $sub_list; ?></li>
      <?php endforeach; ?>
    </ul>
    <p>To make sure the content you signed up for reaches you, follow these steps:</p>
    <p><strong>Gmail:</strong> add us to your primary inbox.</p>
    <ul>
      <li><strong>Mobile app:</strong> hover over the sender name (The Brag Observer) and a window will pop up and select &quot;Add to Contacts.&quot; or move us to &quot;Primary.&quot;</li>
      <li><strong>Desktop:</strong> Drag and drop this email into the &quot;Primary&quot; tab near the top left of your screen</li>
    </ul>
    <p><strong>Apple Mail:</strong> tap on our email address at the top and click &quot;Add to VIPs.&quot;</p>
    <p><strong>Everyone else:</strong> <a href="https://help.aweber.com/hc/en-us/articles/204029246" target="_blank">follow these instructions</a>.</p>
  <?php

    // include( get_template_directory() . '/email-templates/footer.php' );
    include(__DIR__ . '/../templates/email/footer-sub.php');
    $body = ob_get_contents();
    ob_end_clean();

    return [
      'subject' => 'Thanks for subscribing to The Brag Observer',
      'body' => $body
    ];
  } // getSubscribeConfirmationEmail

  /*
  * Send Email with User's Login Details
  */
  public function sendUserLoginDetails($user, $user_pass)
  {
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag <observer@thebrag.media>';
    $to = $user->user_email;

    $subject = 'Welcome to The Brag';

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
  ?>
    <p>Please find your account login details below;</p>
    <p>Username: <?php echo $user->user_login; ?></p>
    <p>Password: <?php echo $user_pass; ?></p>
    <p>&nbsp;</p>
    <p>Regards,<br>The Brag</p>
    <?php
    include(__DIR__ . '/../templates/email/footer.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendUserLoginDetails

  /*
  * Send verification email
  */
  public function sendUserVerificationEmail($user_id, $redirectTo = null)
  {

    $user_info = get_userdata($user_id); // gets user data
    $code = md5($user_id . time()); // creates md5 code to verify later
    $string = ['id' => $user_id, 'code' => $code]; // makes it into a code to send it to user via email
    update_user_meta($user_id, 'is_activated', 0); // creates activation code and activation status in the database
    update_user_meta($user_id, 'activationcode', $code);
    $url = get_site_url() . '/verify/?p=' . base64_encode(serialize($string)); // creates the activation url
    if (!is_null($redirectTo)) {
      $url .= '&returnTo=' . urlencode($redirectTo);
    }

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag <observer@thebrag.media>';
    $to = $user_info->user_email;

    $subject = 'Activate your Account';

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
    ?>
    <p>Please <a href="<?php echo $url; ?>">click here</a> to verify your email address and complete the registration process.</p>
    <p>&nbsp;</p>
    <p>Regards,<br>The Brag</p>
    <?php
    include(__DIR__ . '/../templates/email/footer.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendUserVerificationEmail

  /*
  * Send New User Email (RS Mag Subscription)
  * with User's Login Details
  */
  public function sendUserLoginDetailsRSMag($user_id, $user_pass)
  {
    $user = get_user_by('ID', $user_id);

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag <observer@thebrag.media>';

    $to = $user->user_email;

    $subject = 'Welcome to The Brag Observer';

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
    ?>
    <p>Hi <?php echo $user->first_name; ?></p>
    <p>Thanks for subscribing to Rolling Stone Australia magazine. Welcome to the family!</p>
    <p>While we work on ensuring your next magazine issue is the best it can possibly be, check out your login below to your very own Rolling Stone Australia portal.</p>
    <p>This portal allows you to manage your subscription, update your details, and also sign up to receive our digital content.</p>
    <p>All Rolling Stone Australia digital content is distributed through The Brag Observer, a dedicated newsletter platform. The Brag Observer allows you to subscribe to hyper specific and carefully curated newsletters relevant to your interests, and in some cases, content that is relevant to where you live.</p>
    <p>Use the login details below to update your profile and start receiving personalised newsletters to match your interests, just update your details and we will take it from there.</p>
    <p style="text-align: center; padding: 10px; border: 1px solid #333333; border-radius: 4px;">
      <a href="<?php echo home_url('/observer-subscriptions/'); ?>" target="_blank" style="color: #000000; font-weight: bold; text-decoration: none; display: block;">CLICK HERE TO UPDATE YOUR PREFERENCES</a>
    </p>
    <p>Username: <?php echo $user->user_login; ?></p>
    <p>Password: <?php echo $user_pass; ?></p>
    <p>&nbsp;</p>
    <p>Thanks again for continuing to support journalism.</p>
    <p>&nbsp;</p>
    <p>Regards,<br>The Brag</p>
    <?php
    include(__DIR__ . '/../templates/email/footer.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendUserLoginDetailsRSMag

  /*
  * Send Vote Confirmation Email
  */
  public function sendVoteConfirmationEmail($list_id, $user_id = NULL)
  {
    /*
    global $wpdb;

    if ( is_null( $user_id ) ) {
      $user = wp_get_current_user();
      $to = $user->user_email;
    } else {
      $user = get_user_by( 'id', $user_id );
      $to = $user->user_email;
    }

    $list = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = '{$list_id}'" );

    if ( ! $list ) {
      error_log( '---Observer--- List with ' . $list_id  . ' not found.');
      return;
    }

    $list->title = trim( str_ireplace( 'Observer', '', $list->title ) ) . ' Observer';

    ob_start();
    include( __DIR__ . '/../templates/email/header-list.php' );
?>
    <p>Hi <?php echo get_user_meta( $user->ID, 'first_name', true ); ?>,
    <p>Thanks for voting for the <?php echo $list->title; ?>. We’re excited to have you join the family!</p>
    <p>You'll automatically start receiving the newsletter straight to your inbox once it reaches 1000 votes. Want to speed things up? Get your friends to vote too by sharing the below link with them:
    <a href="<?php echo home_url( '/observer/' . $list->slug . '/' ); ?>"><?php echo home_url( '/observer/' . $list->slug . '/' ); ?></a></p>
    <p>&nbsp;</p>
    <p>Yours sincerely,<br>The Brag</p>
<?php
    include( __DIR__ . '/../templates/email/footer-sub.php' );
    $body = ob_get_contents();
    ob_end_clean();

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag Observer <observer@thebrag.media>';

    $subject = 'Welcome to the ' . $list->title;

    if( $body ) {
      return wp_mail( $to, $subject, $body, $headers );
    }
    */
  } // sendVoteConfirmationEmail

  /*
  * Send Unsubscription Confirmation Email
  */
  public function sendUnsubscribeConfirmationEmail($user_id, $unsub_lists)
  {
    $user = get_user_by('id', $user_id);
    if (!$user)
      return false;

    $to = $user->user_email;

    global $wpdb;

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
    ?>
    <p>Hi <?php echo get_user_meta($user->ID, 'first_name', true); ?>,
    <p>We're sorry to see you go, was it something we said? We're dedicated to curating content relevant to your interests, so we apologise if we got it wrong this time.</p>
    <p>You are now unsubscribed from the below;</p>
    <ul>
      <?php foreach ($unsub_lists as $unsub_list) : ?>
        <li><?php echo $unsub_list; ?></li>
      <?php endforeach; ?>
    </ul>
    <p>At The Brag Observer, we operate a dedicated platform for interest-based newsletters. Please check out the full suite of newsletters we offer below, you can easily subscribe to any of them for free, by clicking the links.</p>
    <?php
    $lists = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY sub_count ASC");
    if ($lists) :
    ?>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class="">
        <tr>
          <?php foreach ($lists as $k => $list) : ?>
            <td align="center" valign="top" style="vertical-align: top; text-align: center; padding-top: 20px; padding-bottom: 20px; padding-left: 5px; padding-right: 5px;">
              <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" target="_blank" style="text-decoration: none; color: #0073aa; font-size: 14px;">
                <img src="<?php echo $list->image_url; ?>" width="175" style="width: 175px; max-width: 175px;">
                <span style="margin-top: 15px;"><?php echo $list->title; ?></span>
              </a>
            </td>
            <?php if (($k + 1) % 3 == 0) : ?>
        </tr>
        <tr>
        <?php endif; ?>
      <?php endforeach; // For Each $list 
      ?>
        </tr>
      </table>
    <?php endif; // If $lists 
    ?>
    <p>&nbsp;</p>
    <p>Yours sincerely,<br>The Brag</p>
    <?php
    include(__DIR__ . '/../templates/email/footer-sub.php');
    $body = ob_get_contents();
    ob_end_clean();

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag Observer <observer@thebrag.media>';

    $subject = 'Sorry ' . get_user_meta($user->ID, 'first_name', true);

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendUnsubscribeConfirmationEmail

  /*
  * Send Registration Reminder Email
  */
  public function sendRegistrationReminderEmail($user = NULL, $subject = 'Hey there!')
  {
    global $wpdb;

    if (is_null($user))
      return;

    $to = $user->user_email;

    $list_query = "SELECT l.title FROM
      {$wpdb->prefix}observer_lists l
        JOIN {$wpdb->prefix}observer_subs s
          ON l.id = s.list_id
    WHERE
      l.status = 'active' AND
      s.user_id = '{$user->ID}'
    ORDER BY
      l.sub_count ASC
    LIMIT 1";

    $list = $wpdb->get_row($list_query);

    if (!$list)
      return;

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');

    if (get_user_meta($user->ID, 'is_imported', true) === '1' && get_user_meta($user->ID, 'oc_token', true)) {
      $unserialized_oc_token = ['id' => $user->ID, 'oc_token' => get_user_meta($user->ID, 'oc_token', true)];
      $profile_url = home_url('/verify/?oc=' . base64_encode(serialize($unserialized_oc_token)));
    } else {
      $profile_url = home_url('/profile/');
    }
    ?>
    <p>Hey there,
    <p>We noticed you're interested in subscribing to the <?php echo $list->title; ?> newsletter, but your registration didn't quite go there.</p>
    <p>If there was a technical error please let us know by replying to this email, otherwise please complete your registration <a href="<?php echo $profile_url; ?>" target="_blank">here</a>.</p>
    <p>Hope to have you as part of our family soon :)</p>
    <p>&nbsp;</p>
    <p>Yours sincerely,<br>The Brag</p>
    <?php
    include(__DIR__ . '/../templates/email/footer.php');
    $body = ob_get_contents();

    ob_end_clean();

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag Observer <observer@thebrag.media>';

    if ($body) {
      return wp_mail($to, $subject, $body, $headers);
    }
    return false;
  } // sendRegistrationReminderEmail

  /*
  * Send Tastemakers verification email
  */
  public function sendTastemakersVerificationEmail($user_id, $tastemaker_id)
  {

    $user_info = get_userdata($user_id); // gets user data

    if (!get_user_meta($user_id, 'tastemaker_verification_code', true)) {
      $code = md5($user_id . time()); // creates md5 code to verify later
      update_user_meta($user_id, 'tastemaker_verification_code', $code);
    } else {
      $code = get_user_meta($user_id, 'tastemaker_verification_code', true);
    }

    $string = ['id' => $user_id, 't_id' => $tastemaker_id, 'code' => $code]; // makes it into a code to send it to user via email

    $url = get_site_url() . '/verify-tastemaker/?p=' . base64_encode(serialize($string)); // creates the activation url

    add_action('phpmailer_init', function ($phpmailer) {
      $phpmailer->FromName   = 'Tone Deaf Tastemakers';
      $phpmailer->IsSMTP();
    });

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: Tone Deaf Tastemakers <observer@thebrag.media>';
    $to = $user_info->user_email;

    $subject = 'Please verify your review!';

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
    ?>
    <p>Thanks for your Tastemaker review!</p>
    <p>We're super grateful to have your input. Please <a href="<?php echo $url; ?>" target="_blank">click here</a> to confirm your entry.</p>
    <p>&nbsp;</p>
    <p>Regards,<br>The Brag Observer</p>
    <?php
    include(__DIR__ . '/../templates/email/footer-sub.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      return wp_mail($to, $subject, $body, $headers);
    }
  } // sendVerificationEmail()

  /*
  * Send Lead Generator verification email
  */
  public function sendLeadGeneratorVerificationEmail($user_id, $lead_generator_id)
  {

    $user_info = get_userdata($user_id); // gets user data

    if (!get_user_meta($user_id, 'lead_generator_verification_code', true)) {
      $code = md5($user_id . time()); // creates md5 code to verify later
      update_user_meta($user_id, 'lead_generator_verification_code', $code);
    } else {
      $code = get_user_meta($user_id, 'lead_generator_verification_code', true);
    }

    $string = ['id' => $user_id, 't_id' => $lead_generator_id, 'code' => $code]; // makes it into a code to send it to user via email

    $url = get_site_url() . '/verify-lg/?p=' . base64_encode(serialize($string)); // creates the activation url

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $to = $user_info->user_email;

    $subject = 'Please verify your response!';

    ob_start();
    include(__DIR__ . '/../templates/email/header.php');
    ?>
    <p>Thanks for your response!</p>
    <p>We're super grateful to have your input. Please <a href="<?php echo $url; ?>" target="_blank">click here</a> to confirm your entry.</p>
    <p>&nbsp;</p>
    <p>Regards,<br>The Brag Observer</p>
    <?php
    include(__DIR__ . '/../templates/email/footer-sub.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      return wp_mail($to, $subject, $body, $headers);
    }
  } // sendVerificationEmail()

  /*
  * Send Observer Invitation
  */
  public function sendObserverInvitation($user, $list, $invitee, $message)
  {
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: The Brag <observer@thebrag.media>';

    $user_refer_code = get_user_meta($user->ID, 'refer_code', true);
    $referral_link_base = home_url('/refer/?rc=' . $user_refer_code);
    $referral_link = $referral_link_base . '&l=' . $list->id . '&email=' . $invitee;

    $message = stripslashes($message);

    $to = $invitee;

    $subject = $user->user_email . ' wants you to check out The ' . trim(str_ireplace(['the', 'observer',], ['', '',], $list->title)) . ' Observer!';

    ob_start();
    include(__DIR__ . '/../templates/email/header-list.php');
    ?>
    <p>Hi</p>
    <p>Your friend, <?php echo $user->user_email; ?>, thinks you'd enjoy the <?php echo trim(str_ireplace(['the', 'observer',], ['', '',], $list->title)); ?> Observer, a newsletter powered by The Brag Observer. They've sent you a private email invite with this message:</p>
    <?php echo wpautop($message); ?>

    <div align="center" class="mcnButtonContent" valign="middle" style="font-family:Arial; font-size:16px; padding:10px; background-color:#000000; max-width:175px; border-radius:3px; text-align:center; margin-top:25px; margin-bottom:25px">
      <a href="<?php echo $referral_link; ?>" target="_blank" title="JOIN" style="letter-spacing:normal; line-height:150%; text-align:center; text-decoration:none; color:#FFFFFF; display:block">JOIN</a>
    </div>

    <p>&nbsp;</p>
    <p>Regards,<br>The Brag</p>
<?php
    include(__DIR__ . '/../templates/email/footer.php');
    $body = ob_get_contents();
    ob_end_clean();

    if ($body) {
      wp_mail($to, $subject, $body, $headers);
    }
  } // sendObserverInvitation

}
