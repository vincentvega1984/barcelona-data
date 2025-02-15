<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Frontend {
    public function __construct() {
        add_shortcode('barcelona_matches', [$this, 'display_matches']);
    }

    public function display_matches() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';

        // Проверка, пуста ли таблица
        if (get_option('barcelona_matches_table_empty', false)) {
            $api = new Barcelona_Matches_API();
            $api->fetch_matches();
        }

        $matches = $wpdb->get_results("SELECT * FROM $table_name ORDER BY match_date DESC");

        // Подключаем шаблон
        ob_start();
        include BARCELONA_MATCHES_PLUGIN_DIR . 'templates/match-results.php';
        return ob_get_clean();
    }
}
