<?php
/**
 * Plugin Name: Gert
 * Description: Tap into the FileReader API to handle any type or size of file without needing to upload it to the server first. Say goodbye to max upload sizes!
 * Author: Nick McNeany
 * Version: 0.1.0
 * Author URI: https://github.com/Nickiam7/gert
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'GERT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
require GERT_PLUGIN_PATH . 'inc/gert-worker-file-uploader.php';