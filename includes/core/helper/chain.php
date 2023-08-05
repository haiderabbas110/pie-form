<?php

/**
 * Chain monad, useful for chaining certain array or string related functions.
 */
class PFORM_Core_Helper_Chain {

	/**
	 * Current value.
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * Class constructor.
	 *
	 * @param mixed $value Current value to start working with.
	 */
	public function __construct( $value ) {

		$this->value = $value;
	}

	/**
	 * Bind some function to value.
	 *
	 * @param mixed $fn Some function.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function bind( $fn ) {

		$this->value = $fn( $this->value );

		return $this;
	}

	/**
	 * Get value.
	 *
	 * @return mixed
	 */
	public function value() {

		return $this->value;
	}

	/**
	 * Magic call.
	 *
	 * @param string $name Method name.
	 * @param array  $params Parameters.
	 *
	 * @throws \BadFunctionCallException Invalid function is called.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function __call( $name, $params ) {

		if ( in_array( $name, $this->allowed_methods(), true ) ) {

			$params = null === $params ? array() : $params;
			array_unshift( $params, $this->value );
			$this->value = call_user_func_array( $name, $params );

			return $this;
		}

		throw new \BadFunctionCallException( "Provided function { $name } is not allowed. See Chain::allowed_methods()." );
	}

	/**
	 * Join array elements with a string.
	 *
	 * @param string $glue Defaults to an empty string.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function implode( $glue = '' ) {

		$this->value = implode( $glue, $this->value );

		return $this;
	}

	/**
	 * Split a string by a string.
	 *
	 * @param string $delimiter The boundary string.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function explode( $delimiter ) {

		$this->value = explode( $delimiter, $this->value );

		return $this;
	}

	/**
	 * Apply the callback to the elements of the given arrays.
	 *
	 * @param callable $cb Callback.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function map( $cb ) {

		$this->value = array_map( $cb, $this->value );

		return $this;
	}

	/**
	 * Pop array.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function pop() {

		$this->value = array_pop( $this->value );

		return $this;
	}

	/**
	 * Run first or second callback based on a condition.
	 *
	 * @param callable $condition Condition function.
	 * @param callable $true_result If condition will return true we run this function.
	 * @param callable $false_result If condition will return false we run this function.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public function iif( $condition, $true_result, $false_result = null ) {

		if ( ! is_callable( $false_result ) ) {
			$false_result = function() {
				return '';
			};
		}
		$this->value = array_map(
			function( $el ) use ( $condition, $true_result, $false_result ) {
				if ( call_user_func( $condition, $el ) ) {
					return call_user_func( $true_result, $el );
				}
				return call_user_func( $false_result, $el );
			},
			$this->value
		);

		return $this;
	}

	/**
	 * All allowed methods to work with data.
	 *
	 * @return array
	 */
	public function allowed_methods() {

		return array(
			'array_change_key_case',
			'array_chunk',
			'array_column',
			'array_combine',
			'array_count_values',
			'array_diff_assoc',
			'array_diff_key',
			'array_diff_uassoc',
			'array_diff_ukey',
			'array_diff',
			'array_fill_keys',
			'array_fill',
			'array_filter',
			'array_flip',
			'array_intersect_assoc',
			'array_intersect_key',
			'array_intersect_uassoc',
			'array_intersect_ukey',
			'array_intersect',
			'array_key_first',
			'array_key_last',
			'array_keys',
			'array_map',
			'array_merge_recursive',
			'array_merge',
			'array_pad',
			'array_pop',
			'array_product',
			'array_rand',
			'array_reduce',
			'array_replace_recursive',
			'array_replace',
			'array_reverse',
			'array_shift',
			'array_slice',
			'array_splice',
			'array_sum',
			'array_udiff_assoc',
			'array_udiff_uassoc',
			'array_udiff',
			'array_uintersect_assoc',
			'array_uintersect_uassoc',
			'array_uintersect',
			'array_unique',
			'array_values',
			'count',
			'current',
			'end',
			'key',
			'next',
			'prev',
			'range',
			'reset',
			'implode',
			'ltrim',
			'rtrim',
			'md5',
			'str_getcsv',
			'str_ireplace',
			'str_pad',
			'str_repeat',
			'str_rot13',
			'str_shuffle',
			'str_split',
			'str_word_count',
			'strcasecmp',
			'strchr',
			'strcmp',
			'strcoll',
			'strcspn',
			'strip_tags',
			'stripcslashes',
			'stripos',
			'stripslashes',
			'stristr',
			'strlen',
			'strnatcasecmp',
			'strnatcmp',
			'strncasecmp',
			'strncmp',
			'strpbrk',
			'strpos',
			'strrchr',
			'strrev',
			'strripos',
			'strrpos',
			'strspn',
			'strstr',
			'strtok',
			'strtolower',
			'strtoupper',
			'strtr',
			'substr_compare',
			'substr_count',
			'substr_replace',
			'substr',
			'trim',
			'ucfirst',
			'ucwords',
			'vfprintf',
			'vprintf',
			'vsprintf',
			'wordwrap',
		);
	}

	/**
	 * Create myself.
	 *
	 * @param mixed $value Current.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	public static function of( $value = null ) {

		return new self( $value );
	}
}