<?php
/**
 * Group Menu Widget
 *
 * Displays Group Menu widget.
 *
 * @extends  RP_Widget
 * @version  1.0.0
 * @package  RestaurantPress/Widgets
 * @category Widgets
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RP_Widget_Menu Class
 */
class RP_Widget_Menu extends RP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'restaurantpress widget_menu';
		$this->widget_description = __( 'Displays RestaurantPress Menu.', 'restaurantpress' );
		$this->widget_id          = 'restaurantpress_widget_menu';
		$this->widget_name        = __( 'RestaurantPress Menu', 'restaurantpress' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Group Menu', 'restaurantpress' ),
				'label' => __( 'Title', 'restaurantpress' )
			),
			'group' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Group ID', 'restaurantpress' )
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by', 'restaurantpress' ),
				'options' => array(
					'date'       => __( 'Date', 'restaurantpress' ),
					'title'      => __( 'Title', 'restaurantpress' ),
					'rand'       => __( 'Random', 'restaurantpress' ),
					'menu_order' => __( 'Menu Order', 'restaurantpress' ),
					'none'       => __( 'None', 'restaurantpress' ),
				)
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'desc',
				'label' => _x( 'Order', 'Sorting order', 'restaurantpress' ),
				'options' => array(
					'asc'  => __( 'ASC', 'restaurantpress' ),
					'desc' => __( 'DESC', 'restaurantpress' ),
				)
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		echo do_shortcode( '[restaurantpress_menu id=' . $instance['group'] . ' orderby=' . $instance['orderby'] . ' order=' . $instance['order'] . ']' );

		$this->widget_end( $args );
	}
}