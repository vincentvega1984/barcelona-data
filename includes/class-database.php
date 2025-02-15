<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Database {
    public static function create_table() {
        global $wpdb;
        $matches_table_name = $wpdb->prefix . 'barcelona_matches';
        $standings_table_name = $wpdb->prefix . 'barcelona_standings';
        $charset_collate = $wpdb->get_charset_collate();
    
        // Таблица для матчей
        $sql_matches = "CREATE TABLE $matches_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            match_date datetime NOT NULL,
            home_team varchar(255) NOT NULL,
            away_team varchar(255) NOT NULL,
            home_score int(11),
            away_score int(11),
            competition varchar(255),
            status varchar(50),
            PRIMARY KEY (id)
        ) $charset_collate;";
    
        // Таблица для турнирной таблицы
        $sql_standings = "CREATE TABLE $standings_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            position int(11) NOT NULL,
            team_name varchar(255) NOT NULL,
            played_games int(11),
            won int(11),
            draw int(11),
            lost int(11),
            points int(11),
            goals_for int(11),
            goals_against int(11),
            goal_difference int(11),
            PRIMARY KEY (id)
        ) $charset_collate;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_matches);
        dbDelta($sql_standings);
    
        // Проверка на ошибки
        if (!empty($wpdb->last_error)) {
            error_log('Ошибка при создании таблиц: ' . $wpdb->last_error);
        }
    
        // Проверка, пуста ли таблица матчей
        $matches_rows = $wpdb->get_var("SELECT COUNT(*) FROM $matches_table_name");
        if ($matches_rows == 0) {
            update_option('barcelona_matches_table_empty', true);
        }
    
        // Проверка, пуста ли таблица турнирной таблицы
        $standings_rows = $wpdb->get_var("SELECT COUNT(*) FROM $standings_table_name");
        if ($standings_rows == 0) {
            update_option('barcelona_standings_table_empty', true);
        }
    }

    public function admin_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';
    
        // Проверка существования таблицы
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            echo '<div class="notice notice-error"><p>Таблица не существует. Попробуйте деактивировать и активировать плагин.</p></div>';
        }
    
        ?>
        <div class="wrap">
            <h1>Barcelona Matches</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('barcelona_matches_options');
                do_settings_sections('barcelona-matches');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function drop_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}