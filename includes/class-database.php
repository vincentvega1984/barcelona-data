<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Database {
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL-запрос для создания таблицы
        $sql = "CREATE TABLE $table_name (
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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Проверка на ошибки
        if (!empty($wpdb->last_error)) {
            error_log('Ошибка при создании таблицы: ' . $wpdb->last_error);
        }

        // Проверка, пуста ли таблица
        $rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($rows == 0) {
            // Если таблица пуста, устанавливаем флаг
            update_option('barcelona_matches_table_empty', true);
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