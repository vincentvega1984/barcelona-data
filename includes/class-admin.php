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
            'Частота обновления (в часах)',
            [$this, 'cron_interval_field'],
            'barcelona-matches',
            'barcelona_matches_main'
        );
    }

    public function api_key_field() {
        $api_key = get_option('barcelona_matches_api_key', '');
        echo '<input type="text" name="barcelona_matches_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
    }

    public function cron_interval_field() {
        $interval = get_option('barcelona_matches_cron_interval', 24);
        echo '<input type="number" name="barcelona_matches_cron_interval" value="' . esc_attr($interval) . '" min="1" class="small-text"> часов';
    }

    public function admin_page() {
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
}