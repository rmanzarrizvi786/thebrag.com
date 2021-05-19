<?php
/*
 * Template Name: Login
 */

if (!is_user_logged_in()) {
  wp_redirect(wp_login_url());
} else {
  wp_redirect(home_url('/observer-subscriptions/'));
}
exit;