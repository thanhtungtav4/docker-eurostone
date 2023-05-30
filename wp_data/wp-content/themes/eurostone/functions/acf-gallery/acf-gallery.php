<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('acf_plugin_gallery') ):

class acf_plugin_gallery {
	
	// vars
	var $settings;
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->settings = array(
			
			// basic
			'name'				=> __('Advanced Custom Fields: Gallery Field', 'acf'),
			'version'			=> '2.1.0',
						
			// urls
			'slug'				=> dirname(plugin_basename( __FILE__ )),
			'basename'			=> plugin_basename( __FILE__ ),
			'path'				=> plugin_dir_path( __FILE__ ),
			'dir'				=> plugin_dir_url( __FILE__ ),
			
		);
		
		// include v5 field
		add_action('acf/include_field_types', array($this, 'include_field_types'));
		
		// include v4 field
		add_action('acf/register_fields', array($this, 'include_field_types'));
		
		
	}
	
	
	/*
	*  include_file
	*
	*  This function will check if a file exists before including it
	*
	*  @type	function
	*  @date	22/2/17
	*  @since	5.5.8
	*
	*  @param	$file (string)
	*  @return	n/a
	*/
	
	function include_file( $file = '' ) {
		$file = dirname(__FILE__) . '/'. $file;
		if( file_exists($file) ) include_once( $file );
	}
	
	
	/*
	*  include_field_types
	*
	*  This function will include the v5 field type
	*
	*  @type	function
	*  @date	12/06/2015
	*  @since	5.2.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function include_field_types() {
		
		// vars
		$version = '';
		
		// version 5
		if( defined('ACF_VERSION') ) {
			
			// version 5.7+
			if( version_compare(ACF_VERSION, '5.7.0', '>=') ) {
				$version = '5-7';
			
			//  version 5.0
			} else {
				$version = '5-0';
			}
		
		// version 4
		} else {
			$version = '4-0';
		}
		
		// include
		$this->include_file( "includes/$version/acf-gallery-field.php" );
	}
}


// globals
global $acf_plugin_gallery;


// instantiate
$acf_plugin_gallery = new acf_plugin_gallery();


// end class
endif;

?>