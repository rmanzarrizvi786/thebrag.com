<?php
namespace Ari\Crypt;

define( 'ARI_CRYPT_IS_OPENSSL_INSTALLED', extension_loaded( 'openssl' ) );

class Crypt {
    const METHOD = 'aes-256-cbc';

    static public function crypt( $message, $key, $encode = true ) {
        if ( ! ARI_CRYPT_IS_OPENSSL_INSTALLED ) {
            if ( $encode )
                $message = base64_encode( $message );

            return $message;
        }

        $nonce_size = openssl_cipher_iv_length( self::METHOD );
        $nonce = openssl_random_pseudo_bytes( $nonce_size );

        $cipher_text = openssl_encrypt(
            $message,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        $res = $nonce . $cipher_text;

        if ( $encode ) {
            $res = base64_encode( $res );
        }

        return $res;
    }

    static public function decrypt( $message, $key, $decode = true ) {
        if ( $decode ) {
            $message = base64_decode($message, true);
        }

        if ( ! ARI_CRYPT_IS_OPENSSL_INSTALLED )
            return $message;

        if ( $decode ) {
            if ( false === $message ) {
                throw new \InvalidArgumentException( 'Encryption failure' );
            }
        }

        $nonce_size = openssl_cipher_iv_length( self::METHOD );
        $nonce = mb_substr( $message, 0, $nonce_size, '8bit' );
        $cipher_text = mb_substr( $message, $nonce_size, null, '8bit' );

        $decrypted_message = openssl_decrypt(
            $cipher_text,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $decrypted_message;
    }

    public static function support_crypt() {
        if ( ! ARI_CRYPT_IS_OPENSSL_INSTALLED )
            return false;

        $crypt_methods = openssl_get_cipher_methods();

        if ( ! in_array( self::METHOD, $crypt_methods ) )
            return false;

        return true;
    }

    public static function get_random_string() {
        mt_srand( (float) microtime() * 1000000 );
        $key = mt_rand();

        return md5( $key );
    }
}
