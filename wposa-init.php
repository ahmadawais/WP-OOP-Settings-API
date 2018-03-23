<?php
/**
 * Plugin Name: WP OOP Settings API
 * Plugin URI: http://AhmadAwais.com/
 * Description: WP-OOP-Settings-API is a Settings API wrapper built with Object Oriented Programming practices.
 * Author: mrahmadawais, WPTie
 * Author URI: http://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package WPOSA
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'WPOSA_VERSION' ) ) {
    define( 'WPOSA_VERSION', '1.0.0' );
}

if ( ! defined( 'WPOSA_NAME' ) ) {
    define( 'WPOSA_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined('WPOSA_DIR' ) ) {
    define( 'WPOSA_DIR', WP_PLUGIN_DIR . '/' . WPOSA_NAME );
}

if ( ! defined('WPOSA_URL' ) ) {
    define( 'WPOSA_URL', WP_PLUGIN_URL . '/' . WPOSA_NAME );
}

/**
 * WP-OOP-Settings-API Initializer
 *
 * Initializes the WP-OOP-Settings-API.
 *
 * @since 	1.0.0
 */


/**
 * Class `WP_OOP_Settings_API`.
 *
 * @since 1.0.0
 */
if ( file_exists( WPOSA_DIR . '/class-wposa.php' ) ) {
    require_once( WPOSA_DIR . '/class-wposa.php' );
}


/**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  1.0.0
 */
if ( class_exists( 'WP_OSA' ) ) {
	/**
	 * Object Instantiation.
	 *
	 * Object for the class `WP_OSA`.
	 */
	$wposa_obj = new WP_OSA();


    // Section: Basic Settings.
    $wposa_obj->add_section(
    	array(
			'id'    => 'wposa_basic',
			'title' => __( 'Basic Settings', 'WPOSA' ),
		)
    );

    // Section: Other Settings.
    $wposa_obj->add_section(
    	array(
			'id'    => 'wposa_other',
			'title' => __( 'Other Settings', 'WPOSA' ),
		)
    );



	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'      => 'title',
			'type'    => 'title',
			'name'    => '<h1>Title</h1>',
		)
	);



    // Field: Text.
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'      => 'text',
			'type'    => 'text',
			'name'    => __( 'Text Input', 'WPOSA' ),
			'desc'    => __( 'Text input description', 'WPOSA' ),
			'default' => 'Default Text',
		)
	);


	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'                => 'text_no',
			'type'              => 'number',
			'name'              => __( 'Number Input', 'WPOSA' ),
			'desc'              => __( 'Number field with validation callback `intval`', 'WPOSA' ),
			'default'           => 1,
			'sanitize_callback' => 'intval'
		)
	);


	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'      => 'password',
			'type'    => 'password',
			'name'    => __( 'Password Input', 'WPOSA' ),
			'desc'    => __( 'Password field description', 'WPOSA' ),
		)
	);


	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'textarea',
			'type' => 'textarea',
			'name' => __( 'Textarea Input', 'WPOSA' ),
			'desc' => __( 'Textarea description', 'WPOSA' ),
		)
	);


	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'checkbox',
			'type' => 'checkbox',
			'name' => __( 'Checkbox', 'WPOSA' ),
			'desc' => __( 'Checkbox Label', 'WPOSA' ),
		)
	);



	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'radio',
			'type' => 'radio',
			'name' => __( 'Radio', 'WPOSA' ),
			'desc' => __( 'Radio Button', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);



	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'multicheck',
			'type' => 'multicheck',
			'name' => __( 'Multile checkbox', 'WPOSA' ),
			'desc' => __( 'Multile checkbox description', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);


	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'select',
			'type' => 'select',
			'name' => __( 'A Dropdown', 'WPOSA' ),
			'desc' => __( 'A Dropdown description', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);



	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'image',
			'type' => 'image',
			'name' => __( 'Image', 'WPOSA' ),
			'desc' => __( 'Image description', 'WPOSA' ),
			'options' => array(
				'button_label' => 'Choose Image'
			)
		)
	);


	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'file',
			'type' => 'file',
			'name' => __( 'File', 'WPOSA' ),
			'desc' => __( 'File description', 'WPOSA' ),
			'options' => array(
				'button_label' => 'Choose file'
			)
		)
	);


	$wpdas_obj->add_field(
		'wposa_other',
		array(
			'id'      => 'separator',
			'type'    => 'separator',
		)
	);



	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'color',
			'type' => 'color',
			'name' => __( 'Color', 'WPOSA' ),
			'desc' => __( 'Color description', 'WPOSA' ),
		)
	);



	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'wysiwyg',
			'type' => 'wysiwyg',
			'name' => __( 'WP_Editor', 'WPOSA' ),
			'desc' => __( 'WP_Editor description', 'WPOSA' ),
		)
	);



}
