<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/public
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Form_Public {
	/**
	 * The turboSMTP API class used for validation
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Turbosmtp_Email_Validator_API
	 */
	private $api;

	/**
	 * @var string
	 */
	private $source;

	/**
	 * @var string
	 */
	private $formId;

	/**
	 * @var array
	 */
	private $validationPass;

	/**
	 * @param $source
	 * @param $formId
	 *
	 * @return void
	 */
	public function __construct( $source, $formId ) {
		$this->source         = $source;
		$this->formId         = $formId;
		$this->validationPass = get_option( 'turbosmtp_email_validator_validation_pass' );
		$this->api            = new Turbosmtp_Email_Validator_API(
			get_option( 'turbosmtp_email_validator_consumer_key' ),
			get_option( 'turbosmtp_email_validator_consumer_secret' ),
			get_option( 'turbosmtp_email_validator_api_timeout' )
		);
	}

	/**
	 * @return string
	 */
	public function set_error_message() {
		$custom_error = get_option( 'turbosmtp_email_validator_error_message' );

		$error_message = __( 'We cannot accept this email address.', 'turbosmtp-email-validator' );
		if ( isset( $custom_error ) && $custom_error ) {
			$error_message = $custom_error;
		}

		return $error_message;
	}

	/**
	 * @param $email
	 *
	 * @return array|null
	 */
	public function prep_validation_info( $email ): ?array {
		global $wpdb;

		$skip_validation = apply_filters( 'turbosmtp_email_validator_skip_validation', false, $email );

		if ( $skip_validation ) {
			return null;
		}

		$validationInfo = $this->api->validateEmail( $email );
		$isValidationInfoError = is_wp_error($validationInfo);

		$table_name = $wpdb->prefix . 'validated_emails';

		$validation_data = array(
			'email'        => $email,
			'source'       => $this->source,
			'ip_address'   => ( $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR'] ),
			'form_id'      => $this->formId,
			'status'       => $isValidationInfoError ? 'valid' : turbosmtp_email_validator_get_status($validationInfo['status'], $this->validationPass),
			'sub_status'   => $isValidationInfoError ? 'api_error' : $validationInfo['sub_status'],
			'original_status'   => $isValidationInfoError ? 'valid' : $validationInfo['status'],
			'validated_at' => current_time( 'mysql' ),
			'raw_data'     => $isValidationInfoError ? json_encode($validationInfo->get_error_data()) : json_encode( $validationInfo ),
		);

		$wpdb->insert(
			$table_name,
			$validation_data
		);

		do_action('turbosmtp_email_validator_validated_email', $email, $validation_data);


		return !$isValidationInfoError ? $validationInfo : $validationInfo->get_error_data();
	}

	/**
	 * @param ?array $validationInfo
	 * @param callable $callback
	 * @param $args
	 *
	 * @return WP_Error|null
	 */
	public function setup_form_validation( ?array $validationInfo, callable $callback, $args ) {
		if ( ! is_null( $validationInfo ) ) {
			if ( ! turbosmtp_email_validator_status_ok( $validationInfo['status'], $this->validationPass ) ) {
				return call_user_func( $callback, $args );
			}
		}

		return null;
	}
}
