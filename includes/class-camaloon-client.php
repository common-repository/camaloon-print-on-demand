<?php
/**
 * Camaloon API Client
 *
 * @package Camaloon
 */

/**
 * Camaloon client class
 */
class Camaloon_Client {
	/**
	 *
	 * User agent
	 *
	 * @var $user_agent
	 */
	private $user_agent = 'Camaloon WooCommerce Plugin';
	/**
	 *
	 * Api url
	 *
	 * @var $api_url
	 */
	private $api_url;


	/**
	 *
	 * Constructor
	 *
	 * @param string $host_param Host for api data
	 *
	 * @throws CamaloonException if the library failed to initialize
	 */
	public function __construct( $host_param ) {
		$host_param       = (string) $host_param;
		$this->user_agent = ' (WP ' . get_bloginfo( 'version' ) . ')';

		if ( ! function_exists( 'json_decode' ) || ! function_exists( 'json_encode' ) ) {
			throw new CamaloonException( 'PHP JSON extension is required for the Camaloon API library to work!' );
		}
		$this->api_url = $host_param;
	}

	/**
	 * Perform a GET request to the API
	 * @param string $path Request path (e.g. 'orders' or 'orders/123')
	 * @param array $params Additional GET parameters as an associative array
	 * @return mixed API response
	 * @throws CamaloonApiException if the API call status code is not in the 2xx range
	 * @throws CamaloonException if the API call has failed or the response is invalid
	 */
	public function get( $path, $params = array() ) {
		return $this->request( 'GET', $path, $params );
	}

	/**
	 * Perform a DELETE request to the API
	 * @param string $path Request path (e.g. 'orders' or 'orders/123')
	 * @param array $params Additional GET parameters as an associative array
	 * @return mixed API response
	 * @throws CamaloonApiException if the API call status code is not in the 2xx range
	 * @throws CamaloonException if the API call has failed or the response is invalid
	 */
	public function delete( $path, $params = array() ) {
		return $this->request( 'DELETE', $path, $params );
	}

	/**
	 * Perform a POST request to the API
	 * @param string $path Request path (e.g. 'orders' or 'orders/123')
	 * @param array $data Request body data as an associative array
	 * @param array $params Additional GET parameters as an associative array
	 * @return mixed API response
	 * @throws CamaloonApiException if the API call status code is not in the 2xx range
	 * @throws CamaloonException if the API call has failed or the response is invalid
	 */
	public function post( $path, $data = array(), $params = array() ) {
		return $this->request( 'POST', $path, $params, $data );
	}
	/**
	 * Perform a PUT request to the API
	 * @param string $path Request path (e.g. 'orders' or 'orders/123')
	 * @param array $data Request body data as an associative array
	 * @param array $params Additional GET parameters as an associative array
	 * @return mixed API response
	 * @throws CamaloonApiException if the API call status code is not in the 2xx range
	 * @throws CamaloonException if the API call has failed or the response is invalid
	 */
	public function put( $path, $data = array(), $params = array() ) {
		return $this->request( 'PUT', $path, $params, $data );
	}

	/**
	 * Perform a PATCH request to the API
	 * @param string $path Request path
	 * @param array $data Request body data as an associative array
	 * @param array $params
	 * @return mixed API response
	 * @throws CamaloonApiException if the API call status code is not in the 2xx range
	 * @throws CamaloonException if the API call has failed or the response is invalid
	 */
	public function patch( $path, $data = array(), $params = array() ) {
			return $this->request( 'PATCH', $path, $params, $data );
	}

	/**
	 * Internal request implementation
	 *
	 * @param $method
	 * @param $path
	 * @param array $params
	 * @param null $data
	 *
	 * @return
	 * @throws CamaloonApiException
	 * @throws CamaloonException
	 */
	private function request( $method, $path, array $params = array(), $data = null ) {
		$url = trim( $path, '/' );

		if ( ! empty( $params ) ) {
			$url .= '?' . http_build_query( $params );
		}

		$request = array(
			'user-agent' => $this->user_agent,
			'method'     => $method,
			'body'       => null !== $data ? wp_json_encode( $data ) : null,
			'timeout'    => 10,
		);

		$result = wp_remote_get( $this->api_url . $url, $request );

		$response = json_decode( $result['body'], true );
		if ( is_wp_error( $result ) ) {
			camaloon_disconnect_local_values();
			camaloon_console_log( 'API request failed - ' . $result->get_error_message() );
			return;
		}

		if ( ! isset( $response['status'], $response['result'] ) ) {
			camaloon_disconnect_local_values();
			camaloon_console_log( 'Invalid API response' );
			return;
		}
		$status = (int) $response['status'];
		if ( $status < 200 || $status >= 300 ) {
			camaloon_disconnect_local_values();
			camaloon_console_log( $response['result'] );
			return;
		}

		return $response['result'];
	}
}

/**
 * Class CamaloonException Generic Camaloon exception
 */
class CamaloonException extends Exception {}
/**
 * Class CamaloonApiException Camaloon exception returned from the API
 */
class CamaloonApiException extends CamaloonException {}
