<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Barcelona Matches',
            'Barcelona Matches',
            'manage_options',
            'barcelona-matches',
            [$this, 'admin_page'],
            'dashicons-schedule',
            100
        );
    }

    public function register_settings() {
        register_setting('barcelona_matches_options', 'barcelona_matches_api_key');
        register_setting('barcelona_matches_options', 'barcelona_matches_cron_interval');
        register_setting('barcelona_matches_options', 'barcelona_standings_cron_interval');

        add_settings_section(
            'barcelona_matches_main',
            'Основные настройки',
            null,
            'barcelona-matches'
        );

        add_settings_field(
            'barcelona_matches_api_key',
            'API Key',
            [$this, 'api_key_field'],
            'barcelona-matches',
            'barcelona_matches_main'
        );

        add_settings_field(
            'barcelona_matches_cron_interval',
            'Частота обновления матчей (в часах)',
            [$this, 'matches_cron_interval_field'],
            'barcelona-matches',
            'barcelona_matches_main'
        );

        add_settings_field(
            'barcelona_standings_cron_interval',
            'Частота обновления турнирной таблицы (в часах)',
            [$this, 'standings_cron_interval_field'],
            'barcelona-matches',
            'barcelona_matches_main'
        );
    }

    public function api_key_field() {
        $api_key = get_option('barcelona_matches_api_key', '');
        echo '<input type="text" name="barcelona_matches_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
    }

    public function matches_cron_interval_field() {
        $interval = get_option('barcelona_matches_cron_interval', 24);
        echo '<input type="number" name="barcelona_matches_cron_interval" value="' . esc_attr($interval) . '" min="1" class="small-text"> часов';
    }

    public function standings_cron_interval_field() {
        $interval = get_option('barcelona_standings_cron_interval', 24);
        echo '<input type="number" name="barcelona_standings_cron_interval" value="' . esc_attr($interval) . '" min="1" class="small-text"> часов';
    }

    public function admin_page() {
        global $wpdb;
        $matches_table_name = $wpdb->prefix . 'barcelona_matches';
        $standings_table_name = $wpdb->prefix . 'barcelona_standings';

        // Обработка ручного запуска обновления матчей
        if (isset($_POST['barcelona_matches_manual_update'])) {
            $api = new Barcelona_Matches_API();
            $result = $api->fetch_matches();

            if ($result) {
                echo '<div class="notice notice-success"><p>Данные матчей успешно обновлены.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Ошибка при обновлении данных матчей. Проверьте лог ошибок.</p></div>';
            }
        }

        // Обработка ручного запуска обновления турнирной таблицы
        if (isset($_POST['barcelona_standings_manual_update'])) {
            $api = new Barcelona_Matches_API();
            $result = $api->fetch_standings();

            if ($result) {
                echo '<div class="notice notice-success"><p>Данные турнирной таблицы успешно обновлены.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Ошибка при обновлении данных турнирной таблицы. Проверьте лог ошибок.</p></div>';
            }
        }

        // Проверка данных в таблицах
        $matches = $wpdb->get_results("SELECT * FROM $matches_table_name");
        $standings = $wpdb->get_results("SELECT * FROM $standings_table_name");

        if (!empty($matches)) {
            echo '<div class="notice notice-success">Данные матчей:</div>';
        } else {
            echo '<div class="notice notice-warning"><p>Таблица матчей пуста. Проверьте лог ошибок.</p></div>';
        }

        if (!empty($standings)) {
            echo '<div class="notice notice-success">Данные турнирной таблицы:</div>';
        } else {
            echo '<div class="notice notice-warning"><p>Таблица турнирной таблицы пуста. Проверьте лог ошибок.</p></div>';
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

            <form method="post">
                <input type="hidden" name="barcelona_matches_manual_update" value="1">
                <?php submit_button('Обновить данные матчей вручную'); ?>
            </form>

            <form method="post">
                <input type="hidden" name="barcelona_standings_manual_update" value="1">
                <?php submit_button('Обновить турнирную таблицу вручную'); ?>
            </form>
        </div>
        <?php
    }
}