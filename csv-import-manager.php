<?php
/**
 * Plugin Name: CSV Import Manager
 * Plugin URI: https://kevin-benabdelhak.fr/plugins/csv-import-manager/
 * Description: CSV Import Manager est un plugin WordPress pratique pour créer des publications en intégrant les données d'un CSV dans des champs personnalisées.
 * Version: 1.0
 * Author: Kevin Benabdelhak
 * Author URI: https://kevin-benabdelhak.fr/
 * Text Domain: csv-import-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



if ( !class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$monUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kevinbenabdelhak/CSV-Import-Manager/', 
    __FILE__,
    'csv-import-manager' 
);

$monUpdateChecker->setBranch('main');








require_once plugin_dir_path( __FILE__ ) . 'admin/menu.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/scripts.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/import.php';