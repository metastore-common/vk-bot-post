<?php
/*
 * Class Vk
 * @author: Dmitriy Nyashkin
 * @link: https://github.com/fdcore/vk.api
 * @version: 2.0
 */

namespace VkApi;

// Возвращаемые ошибки https://vk.com/dev/errors

class VkException extends \Exception {
}

class Vk {

	private $v = '5.53'; // версия Api VKontakte @link https://vk.com/dev/versions
	const VERSION = '2.0'; // версия этой библиотеки

	const CALLBACK_BLANK = 'https://oauth.vk.com/blank.html';
	const AUTHORIZE_URL = 'https://oauth.vk.com/authorize?';
	const GET_TOKEN_URL = 'https://oauth.vk.com/access_token?';
	const METHOD_URL = 'https://api.vk.com/method/';

	public $secret_key = null;
	public $scope = [];
	public $client_id = null;
	public $access_token = null;
	public $owner_id = 0;
	public $debug = false;
	private $_api_scope = '';

	/**
	 * Vk constructor.
	 *
	 * @param array $options
	 * -------------------------------------------------------------------------------------------------------------- */

	function __construct( $options = [] ) {
		$this->scope[] = 'offline'; // обязательно запрашиваем права на оффлайн работу без участия пользователя
		$this->setOptions( $options );
	}

	/**
	 * Call.
	 *
	 * @param $method
	 * @param $params
	 *
	 * @return bool|mixed|string
	 * @throws VkException
	 */

	function __call( $method, $params ) {
		if ( ! isset( $params[0] ) ) {
			$params[0] = [];
		}

		return $this->api( $this->_api_scope . '.' . $method, $params[0] );
	}

	/**
	 * Get.
	 *
	 * @param $name
	 *
	 * @return $this
	 */
	function __get( $name ) {
		$this->_api_scope = $name;

		return $this;
	}

	/**
	 * Get API.
	 *
	 * @param string $method
	 * @param array $vars
	 *
	 * @return bool|mixed|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function api( $method = '', array $vars = [] ) {
		$vars['v']            = $this->v;
		$vars['access_token'] = $this->access_token;
		$params               = http_build_query( $vars );
		$url                  = $this->httpBuildQuery( $method, $params );

		// Для тестирования и отладки запросов
		if ( $this->debug ) {
			return $url;
		}

		return $this->call( $url );
	}

	/**
	 * Execute.
	 *
	 * @param $code
	 *
	 * @return bool|mixed|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function execute( $code ) {
		return $this->api( 'execute', array( 'code' => $code ) );
	}

	/**
	 * Build URI.
	 *
	 * @param $method
	 * @param string $params
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private function httpBuildQuery( $method, $params = '' ) {
		return self::METHOD_URL . $method . '?' . $params;
	}

	/**
	 * Get token code.
	 *
	 * @param string $type
	 * @param string $callback
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	public function getCodeToken( $type = "token", $callback = self::CALLBACK_BLANK ) {
		$url = self::AUTHORIZE_URL;

		$url .= http_build_query( array(
			'response_type' => $type,
			'client_id'     => $this->client_id,
			'redirect_uri'  => $callback,
			'display'       => 'page',
			'version'       => $this->v,
			'scope'         => implode( ',', $this->scope ),
		) );

		return $url;
	}

	/**
	 * Get token access.
	 *
	 * @param $code
	 * @param string $callback
	 *
	 * @return bool|mixed|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function getAccessToken( $code, $callback = self::CALLBACK_BLANK ) {
		$url = self::GET_TOKEN_URL;

		$url .= http_build_query( array(
			'code'          => $code,
			'client_id'     => $this->client_id,
			'redirect_uri'  => $callback,
			'client_secret' => $this->secret_key,
			'version'       => $this->v,
			'scope'         => implode( ',', $this->scope ),
		) );

		return $this->call( $url );
	}

	/**
	 * Call.
	 *
	 * @param string $url
	 *
	 * @return bool|mixed|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	private function call( $url = '' ) {
		if ( function_exists( 'curl_init' ) ) {
			$json = $this->curlPost( $url );
		} else {
			$json = file_get_contents( $url );
		}

		$json = json_decode( $json, true );

		// Произошла ошибка на стороне VK, коды ошибок тут https://vk.com/dev/errors
		if ( isset( $json['error'], $json['error']['error_msg'], $json['error']['error_code'] ) ) {
			throw new VkException( $json['error']['error_msg'], $json['error']['error_code'] );
		}

		if ( isset( $json['response'] ) ) {
			return $json['response'];
		}

		return $json;
	}

	/**
	 * Send cURL POST request.
	 *
	 * @param $url
	 *
	 * @return bool|mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private function curlPost( $url ) {
		if ( ! function_exists( 'curl_init' ) ) {
			return false;
		}

		$param = parse_url( $url );

		if ( $curl = curl_init() ) {

			curl_setopt( $curl, CURLOPT_URL, $param['scheme'] . '://' . $param['host'] . $param['path'] );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $param['query'] );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

			$out = curl_exec( $curl );

			curl_close( $curl );

			return $out;
		}

		return false;
	}

	/**
	 * Set options.
	 *
	 * @param array $options
	 * -------------------------------------------------------------------------------------------------------------- */

	public function setOptions( $options = [] ) {
		if ( count( $options ) > 0 ) {
			foreach ( $options as $key => $value ) {
				if ( $key == 'scope' && is_string( $value ) ) {
					$_scope      = explode( ',', $value );
					$this->scope = array_merge( $this->scope, $_scope );
				} else {
					$this->$key = $value;
				}

			}
		}
	}

	/**
	 * Upload photo.
	 *
	 * @param int $gid
	 * @param array $files
	 * @param bool $return_ids
	 * @param array $additional_data
	 * @param int $usleep
	 *
	 * @return array|bool
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function uploadPhoto( $gid = 0, array $files = [], $return_ids = false, array $additional_data = [], $usleep = 0 ) {
		if ( count( $files ) == 0 ) {
			return false;
		}

		if ( ! function_exists( 'curl_init' ) ) {
			return false;
		}

		$data_json = $this->api( 'photos.getWallUploadServer', array( 'group_id' => intval( $gid ) ) );

		if ( ! isset( $data_json['upload_url'] ) ) {
			return false;
		}

		$temp = array_chunk( $files, 4 ); // лимит файлов


		$attachments = [];

		foreach ( $temp as $chunk_index => $temp_chunk ) {
			if ( $chunk_index ) {
				usleep( $usleep );
			}

			$files = [];

			foreach ( $temp_chunk as $key => $data ) {
				$path = realpath( $data );

				if ( $path ) {
					$files[ 'file' . ( $key + 1 ) ] = ( class_exists( 'CURLFile', false ) ) ? new \CURLFile( realpath( $data ) ) : '@' . realpath( $data );
				}
			}

			$upload_url = $data_json['upload_url'];
			$ch         = curl_init( $upload_url );

			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-type: multipart/form-data" ) );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $files );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

			$upload_data             = json_decode( curl_exec( $ch ), true );
			$upload_data['group_id'] = intval( $gid );
			$upload_data             += $additional_data;

			usleep( $usleep );

			$response = $this->api( 'photos.saveWallPhoto', $upload_data );

			if ( count( $response ) > 0 ) {
				foreach ( $response as $photo ) {
					if ( $return_ids ) {
						$attachments[] = $photo['id'];
					} else {
						$attachments[] = 'photo' . $photo['owner_id'] . '_' . $photo['id'];
					}
				}
			}

		}

		return $attachments;

	}

	/**
	 * Upload docs.
	 *
	 * @param bool $gid
	 * @param $file
	 *
	 * @return bool|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function uploadDoc( $gid = false, $file ) {
		if ( ! is_string( $file ) ) {
			return false;
		}

		if ( ! function_exists( 'curl_init' ) ) {
			return false;
		}

		$data_json = $this->api( 'docs.getUploadServer', array( 'gid' => intval( $gid ) ) );

		if ( ! isset( $data_json['upload_url'] ) ) {
			return false;
		}

		$attachment = false;
		$path       = realpath( $file );

		if ( ! $path ) {
			return false;
		}

		$files['file'] = ( class_exists( 'CURLFile', false ) ) ? new \CURLFile( $file ) : '@' . $file;
		$upload_url    = $data_json['upload_url'];

		$ch = curl_init( $upload_url );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-type: multipart/form-data" ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $files );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$upload_data = json_decode( curl_exec( $ch ), true );
		$response    = $this->api( 'docs.save', $upload_data );

		if ( count( $response ) > 0 ) {
			foreach ( $response as $photo ) {
				$attachment = 'doc' . $photo['owner_id'] . '_' . $photo['did'];
			}
		}

		return $attachment;

	}

	/**
	 *
	 * Upload video.
	 *
	 * http://vk.com/dev/video.save
	 *
	 * @param array $options
	 * @param bool $file
	 *
	 * @return bool|string
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function uploadVideo( $options = [], $file = false ) {
		if ( ! is_array( $options ) ) {
			return false;
		}

		if ( ! function_exists( 'curl_init' ) ) {
			return false;
		}

		$data_json = $this->api( 'video.save', $options );

		if ( ! isset( $data_json['upload_url'] ) ) {
			return false;
		}

		$attachment = 'video' . $data_json['owner_id'] . '_' . $data_json['video_id'];
		$upload_url = $data_json['upload_url'];
		$ch         = curl_init( $upload_url );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-type: multipart/form-data" ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		// если указан файл то заливаем его отправкой POST переменной video_file
		if ( $file && file_exists( $file ) ) {
			//@todo надо протестировать заливку
			$path = realpath( $file );

			if ( ! $path ) {
				return false;
			}

			$files['video_file'] = ( class_exists( 'CURLFile', false ) ) ? new \CURLFile( $file ) : '@' . $file;

			curl_setopt( $ch, CURLOPT_POSTFIELDS, $files );
			curl_exec( $ch );

			// иначе просто обращаемся по адресу (ну надо так!)
		} else {
			curl_exec( $ch );
		}

		return $attachment;

	}

	/**
	 * Upload video from array.
	 *
	 * @param array $videos
	 *
	 * @return array
	 * @throws VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function uploadVideosFromArray( array $videos ) {
		$attached_videos = [];

		foreach ( $videos as $i => $video_url ) {
			$attached_videos[] = $this->uploadVideo( [
				'link'     => $video_url,
				'wallpost' => 0
			] );
		}

		return $attached_videos;
	}
}
