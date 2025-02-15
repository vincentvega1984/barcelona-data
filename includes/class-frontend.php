<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Frontend {
    public function __construct() {
        add_shortcode('barcelona_matches', [$this, 'display_matches']);
        add_shortcode('barcelona_standings', [$this, 'display_standings']);
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

    public function display_standings() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_standings';
    
        // Проверка, пуста ли таблица
        if (get_option('barcelona_standings_table_empty', false)) {
            $api = new Barcelona_Matches_API();
            $api->fetch_standings();
        }
    
        // Получаем данные из таблицы
        $standings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY position ASC");
    
        // Подключаем шаблон
        ob_start();
        include BARCELONA_MATCHES_PLUGIN_DIR . 'templates/standings-table.php';
        return ob_get_clean();
    }
}
