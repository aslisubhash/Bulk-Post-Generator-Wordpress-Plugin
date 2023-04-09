<?php
/**
 * Plugin Name: Bulk Content Generator
 * Plugin URI: https://digitalkumbh.in/bulk-content-generator
 * Description: A plugin to generate bulk WordPress posts/pages/categories with a given template and variable replacement.
 * Version: 1.0
 * Author: Subhash Chaudhary
 * Author URI: https://audiokumbh.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bulk-content-generator
 */

 
 function bcg_menu() {
     add_menu_page('Bulk Content Generator', 'Bulk Content Generator', 'manage_options', 'bulk-content-generator', 'bcg_settings_page');
 }
 add_action('admin_menu', 'bcg_menu');
 
 function bcg_admin_scripts($hook) {
     if ($hook != 'toplevel_page_bulk-content-generator') {
         return;
     }
 
     wp_enqueue_style('bcg-admin', plugin_dir_url(__FILE__) . 'admin-style.css');
     wp_enqueue_script('bcg-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), false, true);
 }
 add_action('admin_enqueue_scripts', 'bcg_admin_scripts');
 
 function bcg_settings_page() {
     include(plugin_dir_path(__FILE__) . 'settings.php');
 }
 