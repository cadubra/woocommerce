<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order Line Item (shipping).
 *
 * @version     2.7.0
 * @since       2.7.0
 * @package     WooCommerce/Classes
 * @author      WooThemes
 */
class WC_Order_Item_Shipping extends WC_Order_Item {

	/**
	 * Order Data array. This is the core order data exposed in APIs since 2.7.0.
	 * @since 2.7.0
	 * @var array
	 */
	protected $extra_data = array(
		'method_title' => '',
		'method_id'    => '',
		'total'        => 0,
		'total_tax'    => 0,
		'taxes'        => array(
			'total' => array(),
		),
	);

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set order item name.
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_name( $value ) {
		$this->set_method_title( $value );
	}

	/**
	 * Set method title.
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_method_title( $value ) {
		$this->set_prop( 'method_title', wc_clean( $value ) );
	}

	/**
	 * Set shipping method id.
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_method_id( $value ) {
		$this->set_prop( 'method_id', wc_clean( $value ) );
	}

	/**
	 * Set total.
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_total( $value ) {
		$this->set_prop( 'total', wc_format_decimal( $value ) );
	}

	/**
	 * Set total tax.
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	protected function set_total_tax( $value ) {
		$this->set_prop( 'total_tax', wc_format_decimal( $value ) );
	}

	/**
	 * Set taxes.
	 *
	 * This is an array of tax ID keys with total amount values.
	 * @param array $raw_tax_data
	 * @throws WC_Data_Exception
	 */
	public function set_taxes( $raw_tax_data ) {
		$raw_tax_data = maybe_unserialize( $raw_tax_data );
		$tax_data     = array(
			'total'    => array(),
		);
		if ( ! empty( $raw_tax_data['total'] ) ) {
			$tax_data['total']    = array_map( 'wc_format_decimal', $raw_tax_data['total'] );
		}
		$this->set_prop( 'taxes', $tax_data );
		$this->set_total_tax( array_sum( $tax_data['total'] ) );
	}

	/**
	 * Set properties based on passed in shipping rate object.
	 *
	 * @param WC_Shipping_Rate $tax_rate_id
	 * @throws WC_Data_Exception
	 */
	public function set_shipping_rate( $shipping_rate ) {
		$this->set_method_title( $shipping_rate->label );
		$this->set_method_id( $shipping_rate->id );
		$this->set_total( $shipping_rate->cost );
		$this->set_taxes( $shipping_rate->taxes );
		$this->set_meta_data( $shipping_rate->get_meta_data() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get order item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'shipping';
	}

	/**
	 * Get order item name.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_method_title( $context );
	}

	/**
	 * Get title.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_method_title( $context = 'view' ) {
		$method_title = $this->get_prop( 'method_title', $context );
		if ( 'view' === $context ) {
			return $method_title ? $method_title : __( 'Shipping', 'woocommerce' );
		} else {
			return $method_title;
		}
	}

	/**
	 * Get method ID.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_method_id( $context = 'view' ) {
		return $this->get_prop( 'method_id', $context );
	}

	/**
	 * Get total cost.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_total( $context = 'view' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get total tax.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_total_tax( $context = 'view' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get taxes.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_taxes( $context = 'view' ) {
		return $this->get_prop( 'taxes', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Array Access Methods
	|--------------------------------------------------------------------------
	|
	| For backwards compat with legacy arrays.
	|
	*/

	/**
	 * offsetGet for ArrayAccess/Backwards compatibility.
	 * @deprecated Add deprecation notices in future release.
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		if ( 'cost' === $offset ) {
			$offset = 'total';
		}
		return parent::offsetGet( $offset );
	}

	/**
	 * offsetSet for ArrayAccess/Backwards compatibility.
	 * @deprecated Add deprecation notices in future release.
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value ) {
		if ( 'cost' === $offset ) {
			$offset = 'total';
		}
		parent::offsetSet( $offset, $value );
	}

	/**
	 * offsetExists for ArrayAccess
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		if ( in_array( $offset, array( 'cost' ) ) ) {
			return true;
		}
		return parent::offsetExists( $offset );
	}

	/**
	 * Internal meta keys we don't want exposed as part of meta_data.
	 * @return array()
	 */
	protected function get_internal_meta_keys() {
		return array( 'method_id', 'cost', 'total_tax', 'taxes' );
	}
}
