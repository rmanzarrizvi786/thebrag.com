<?php
namespace Ari\Wordpress;

final class Mail {
    public static function send( $to, $subject, $message, $options = array(), $headers = array(), $attachments = array() ) {
        $options = new Mail_Options( $options );

        if ( $options->html )
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

        if ( strlen( $options->from ) > 0 ) {
            $from = strlen( $options->from_name ) > 0
                ? $options->from_name . ' <' . $options->from . '>'
                : $options->from;

            $headers[] = 'From: ' . $from;
        }


        if ( count( $headers ) === 0)
            $headers = '';

        return wp_mail( $to, $subject, $message, $headers, $attachments );
    }
}
