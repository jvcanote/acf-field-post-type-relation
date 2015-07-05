<?php
/*
Plugin Name: Advanced Custom Fields: Post Type Relation
Plugin URI: 
Description: Post Type field for Advanced Custom Fields
Version: 1.0.1
Author: Jacob Vega Canote
Author URI: http://WEBDOGS.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: acf-field-post-type-relation
Domain Path: /languages
*/


class acf_field_post_type_relation_plugin
{
	/*
	*  Construct
	*
	*  @description:
	*  @since: 1.0
	*  @created: 01/04/15
	*/

	function __construct()
	{
		load_plugin_textdomain( 'acf-field-post-type-relation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


		// version 4+
		add_action('acf/register_fields', array($this, 'register_fields'));


	}

	/*
	*  register_fields
	*
	*  @description:
	*  @since: 1.0
	*  @created: 01/04/15
	*/

	function register_fields()
	{
		include_once('post_type.php');
	}


}

new acf_field_post_type_relation_plugin();

?>