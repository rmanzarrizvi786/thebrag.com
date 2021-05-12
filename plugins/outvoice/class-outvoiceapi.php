<?php
/**
 * This class is for making calls to the OutVoice API
 *
 * @package OutVoice
 * Description: This class is for making calls to the OutVoice API
 */

namespace Outvoice\Api;

/**
 * Helper Class for OutVoice functions.
 */
class Outvoiceapi {


	/**
	 * The OutVoice API address.
	 *
	 * @var string
	 */
	protected $api_url = 'https://api.outvoice.com/';

	/**
	 * The OutVoice API Version.
	 *
	 * @var string
	 */
	protected $api_version = 'api/v1.0/';

	/**
	 * The OutVoice module version.
	 *
	 * @var string
	 */
	protected $module_version = 'wordpress-1.2.3';

	/**
	 * The OutVoice Client ID.
	 *
	 * @var string
	 */
	protected $client_id = '08a3b847-025c-40a9-bfc8-4ed891286f99';

	/**
	 * OAuth Access Token.
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * OAuth Refresh Token.
	 *
	 * @var string
	 */
	public $refresh_token;

	/**
	 * OAuth token expires.
	 *
	 * @var string
	 */
	public $token_expires;

	/**
	 * Generates OAuth tokens from username and password.
	 *
	 * @param string $username User's OutVoice username.
	 * @param string $password User's OutVoice password.
	 *
	 * @return bool
	 *   Returns TRUE if successful.
	 */
	public function generate_tokens( $username, $password ) {

		$data    = [
			'body' => [
				'grant_type' => 'password',
				'client_id'  => $this->client_id,
				'username'   => $username,
				'password'   => $password,
			],
		];
		$request = wp_remote_post( $this->api_url . 'oauth/token', $data );
		$tokens  = json_decode( wp_remote_retrieve_body( $request ) );
		if ( isset( $tokens->access_token ) ) {
			$this->access_token  = $tokens->access_token;
			$this->refresh_token = $tokens->refresh_token;
			$this->token_expires = time() + 3000;
			return true;
		}
		return false;

	}

	/**
	 * Refreshes OAuth tokens.
	 *
	 * @return bool
	 *   Returns TRUE if successful.
	 */
	public function refresh_tokens() {

		$data    = [
			'body' => [
				'grant_type'    => 'refresh_token',
				'client_id'     => $this->client_id,
				'refresh_token' => $this->refresh_token,
			],
		];
		$request = wp_remote_post( $this->api_url . 'oauth/token', $data );
		$tokens  = json_decode( wp_remote_retrieve_body( $request ) );
		if ( isset( $tokens->access_token ) ) {
			$this->access_token  = $tokens->access_token;
			$this->refresh_token = $tokens->refresh_token;
			$this->token_expires = time() + 3000;
			return true;
		}
		return false;

	}

	/**
	 * Checks for authorization
	 *
	 * @return mixed
	 */
	public function auth_check() {

		$headers                 = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->get_access_token(),
			),
		);
		if (function_exists('vip_safe_wp_remote_get')) {
            $request = vip_safe_wp_remote_get( $this->api_url . $this->api_version . 'auth-check', '', 10, 3, 10, $headers );
        } else {
            $request = wp_remote_get( $this->api_url . $this->api_version . 'auth-check', $headers );
        }
		$output['response_code'] = wp_remote_retrieve_response_code( $request );
		$output['username']      = json_decode( wp_remote_retrieve_body( $request ), true );
		$output['access_token']  = $this->get_access_token();
		$output['expires']       = $this->get_token_expires();

		return $output;
	}

	/**
	 * Sets OAuth tokens.
	 *
	 * @param string $access OAuth access token.
	 * @param string $refresh OAuth refresh token.
	 */
	public function set_tokens( $access, $refresh ) {
		$this->access_token  = $access;
		$this->refresh_token = $refresh;
		$this->token_expires = time() + 3000;
	}

	/**
	 * Retrieves access token and refreshes it if needed.
	 */
	public function get_access_token() {
		if ( time() > $this->token_expires ) {
			$this->refresh_tokens();
		}
		return $this->access_token;
	}

	/**
	 * Retrieves refresh token.
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Retrieves token expiration time.
	 */
	public function get_token_expires() {
		return $this->token_expires;
	}

	/**
	 * Handles an OutVoice payment.
	 *
	 * @param array $info An array of values for the payment.
	 *     - freelancer: The ID of the contributor.
	 *     - amount: The payment amount.
	 *     - currency: Payment currency.
	 *     - freelancer1: ID of second contributor (optional).
	 *     - amount1: The second payment amount (optional).
	 *     - url: The URL of the content published.
	 *     - title: The title of the content published.
	 *
	 * @return string
	 *   Returns message in plain text.
	 */
	public function payment( array $info ) {

		$hash = $this->generate_hash( $info );

		$data = [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->get_access_token(),
				'Content-Type'  => 'application/json',
			],
			'body'    => [
				0 => [
					'freelancer' => $info['freelancer'],
					'amount'     => self::outvoice_format_amount( $info['amount'] ),
					'currency'   => $info['currency'],
					'url'        => $info['url'],
					'title'      => $info['title'],
					'version'    => $this->module_version,
					'hash'       => $hash,
				],
			],
		];
		// Add second contributor.
		if ( ! empty( $info['amount1'] ) ) {
			$data['body'][1] = [
				'freelancer' => $info['freelancer1'],
				'amount'     => self::outvoice_format_amount( $info['amount1'] ),
				'currency'   => $info['currency1'],
				'url'        => $info['url'],
				'title'      => $info['title'],
				'version'    => $this->module_version,
				'hash'       => $hash,
			];
		}
		$data['body'] = wp_json_encode( $data['body'] );
		$transaction  = wp_remote_post( $this->api_url . $this->api_version . 'transaction', $data );

    function is_wp_error( $transaction ) {
      $error = 'We were unable to get a confirmation from OutVoice. Please log in to your account and confirm the payment went through.';
      set_transient( 'outvoice_error', $error, 3600 );
      delete_transient( 'outvoice_success' );
      return false;
    }

		if ( isset( $transaction['response']['code'] ) && 200 === $transaction['response']['code'] ) {
			// success message.
			set_transient( 'outvoice_success', $transaction['body'], 3600 );
			delete_transient( 'outvoice_error' );
			$message = true;
		} else {
			$error = 'We were unable to get a confirmation from OutVoice. Please log in to your account and confirm the payment went through.';
			set_transient( 'outvoice_error', $error, 3600 );
			delete_transient( 'outvoice_success' );
			$message = false;
		}

		return $message;

	}

	/**
	 * List of all contributors.
	 *
	 * @return array
	 *   Returns an array of contributors keyed by their IDs
	 */
	public function list_contributors() {

		$data             = [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->get_access_token(),
			],
		];
		if (function_exists('vip_safe_wp_remote_get')) {
            $request = vip_safe_wp_remote_get( $this->api_url . $this->api_version . 'list-freelancers', '', 10, 3, 10, $data );
        } else {
            $request = wp_remote_get($this->api_url . $this->api_version . 'list-freelancers', $data);
        }
		$contributor_list = json_decode( wp_remote_retrieve_body( $request ) );
		return $contributor_list;

	}

	/**
	 * Generate unique enough hash for payment.
	 *
	 * @param array $data takes data from transaction.
	 *
	 * @return string
	 *   Returns a string
	 */
	public function generate_hash( $data ) {
		$string = $data['url'] . $data['title'] . $data['freelancer'] . $data['amount'] . $data['currency'];
		return hash( 'crc32b', $string );
	}

	/**
	 * Formats currency amounts.
	 *
	 * @param int $amount Number indicating amount to be paid.
	 *
	 * @return string
	 *   Formatted without decimal.
	 */
	public static function outvoice_format_amount( $amount ) {

		$decimal = strpos( $amount, '.' );
		if ( false === $decimal ) {
			$amount = $amount . '00';
		} else {
			// Ensure 2 digits after decimal.
			$exploded = explode( '.', $amount );
			if ( empty( strlen( $exploded[1] ) ) ) {
				$amount = $amount . '00';
			}
			if ( 1 === strlen( $exploded[1] ) ) {
				$amount = $amount . '0';
			}
		}
		return $amount;

	}

}
