<?php 
/*
Plugin Name: Hm Weather widget
Plugin URI: 
Description: Hm weather widget is a simple  and lightweight plugin that will help you to show weather condition on your site .
Version: 1.0.0
Author: shimul
Author URI: https://www.behance.net/nazmulwp
License: GNU General Public License (Version 2 - GPLv2)
Text Domain: wf
Network: False
*/

if( ! defined( 'HM_WW_HACK_MSG' ) ) define( 'HM_WW_HACK_MSG', __( 'Sorry cowboy! This is not your place', 'wf' ) );

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( HM_WW_HACK_MSG );

/**
 * Defining constants
 */
if( ! defined( 'HM_WW_VERSION' ) ) define( 'HM_WW_VERSION', '1.0.0' );
if( ! defined( 'HM_WW_MENU_POSITION' ) ) define( 'HM_WW_MENU_POSITION', 96 );
if( ! defined( 'HM_WW_PLUGIN_DIR' ) ) define( 'HM_WW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'HM_WW_FILES_DIR' ) ) define( 'HM_WW_FILES_DIR', HM_WW_PLUGIN_DIR . 'hm_widget_assets' );
if( ! defined( 'HM_WW_PLUGIN_URI' ) ) define( 'HM_WW_PLUGIN_URI', plugins_url( '', __FILE__ ) );
if( ! defined( 'HM_WW_FILES_URI' ) ) define( 'HM_WW_FILES_URI', HM_WW_PLUGIN_URI . '/hm_widget_assets' );


require_once HM_WW_FILES_DIR . '/includes/geoplugin.class.php';
require_once HM_WW_FILES_DIR . '/includes/hm-weather-widgets.php';



