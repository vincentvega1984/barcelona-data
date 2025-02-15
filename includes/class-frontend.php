<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Frontend {
    public function __construct() {
        add_shortcode('barcelona_matches', [$this, 'display_matches']);
        add_shortcode('barcelona_standings', [$this, 'display_standings']);
        add_shortcode('barcelona_players', [$this, 'display_players']);
    }

    public function display_matches($atts) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';
    
        // Проверка, пуста ли таблица
        if (get_option('barcelona_matches_table_empty', false)) {
            $api = new Barcelona_Matches_API();
            $api->fetch_matches();
        }
    
        // Параметры шорткода
        $atts = shortcode_atts([
            'future' => 0, // Количество предстоящих матчей
            'past' => 0,   // Количество прошедших матчей
            'tournament' => '', // Название турнира
        ], $atts, 'barcelona_matches');
    
        // Формируем SQL-запрос в зависимости от параметров
        $where = [];
        $order = '';
    
        if ($atts['future'] > 0) {
            $where[] = "match_date > NOW()";
            $order = "ORDER BY match_date ASC";
        } elseif ($atts['past'] > 0) {
            $where[] = "match_date <= NOW()";
            $order = "ORDER BY match_date DESC";
        }
    
        if (!empty($atts['tournament'])) {
            $where[] = $wpdb->prepare("competition = %s", $atts['tournament']);
        }
    
        // Собираем WHERE-условие
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
        // Лимит для выборки
        $limit = '';
        if ($atts['future'] > 0 || $atts['past'] > 0) {
            $limit = "LIMIT " . max($atts['future'], $atts['past']);
        }
    
        // Выполняем запрос
        $matches = $wpdb->get_results("SELECT * FROM $table_name $where_clause $order $limit");
    
        if (empty($matches)) {
            return '<p>Нет данных о матчах.</p>';
        }
    
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

    public function display_players() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_players';
    
        // Получаем данные из таблицы
        $players = $wpdb->get_results("SELECT * FROM $table_name ORDER BY shirt_number ASC");
    
        // Подключаем шаблон
        ob_start();
        include BARCELONA_MATCHES_PLUGIN_DIR . 'templates/players-list.php';
        return ob_get_clean();
    }
}
