<?php
/*
Plugin Name: Barcelona Data
Description: Получает данные о матчах "Барселоны" через API и выводит их на сайте.
Version: 1.0
Author: n.korotkiy@hiwayarts.com
*/

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа
}

// Константы плагина
define('BARCELONA_MATCHES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BARCELONA_MATCHES_PLUGIN_URL', plugin_dir_url(__FILE__));

// Подключение классов
require_once BARCELONA_MATCHES_PLUGIN_DIR . 'includes/class-database.php';
require_once BARCELONA_MATCHES_PLUGIN_DIR . 'includes/class-api.php';
require_once BARCELONA_MATCHES_PLUGIN_DIR . 'includes/class-admin.php';
require_once BARCELONA_MATCHES_PLUGIN_DIR . 'includes/class-frontend.php';

// Инициализация плагина
function barcelona_matches_init() {
    $database = new Barcelona_Matches_Database();
    $api = new Barcelona_Matches_API();
    $admin = new Barcelona_Matches_Admin();
    $frontend = new Barcelona_Matches_Frontend();
}
add_action('plugins_loaded', 'barcelona_matches_init');

// Регистрация функции создания таблицы при активации плагина
register_activation_hook(__FILE__, ['Barcelona_Matches_Database', 'create_table']);

register_deactivation_hook(__FILE__, ['Barcelona_Matches_Database', 'drop_table']);