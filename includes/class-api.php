<?php
if (!defined('ABSPATH')) {
    exit;
}

class Barcelona_Matches_API {
    private $api_key;

    public function __construct() {
        $this->api_key = get_option('barcelona_matches_api_key', '');
        add_action('barcelona_matches_cron', [$this, 'fetch_matches']);
        $this->schedule_cron();
    }
    
    private function schedule_cron() {
        if (!wp_next_scheduled('barcelona_matches_cron')) {
            $interval = get_option('barcelona_matches_cron_interval', 24) * HOUR_IN_SECONDS;
            wp_schedule_event(time(), $interval, 'barcelona_matches_cron');
        }
    }

    public function fetch_matches() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'barcelona_matches';
    
        $team_id = 81; // ID "Барселоны"
        $url = "https://api.football-data.org/v4/teams/$team_id/matches";
    
        $args = [
            'headers' => [
                'X-Auth-Token' => $this->api_key,
            ],
        ];
    
        $response = wp_remote_get($url, $args);
    
        if (is_wp_error($response)) {
            error_log('Ошибка API: ' . $response->get_error_message());
            return false;
        }
    
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        if (empty($data['matches'])) {
            error_log('Нет данных о матчах в ответе API.');
            return false;
        }
    
        // Очистка таблицы перед сохранением новых данных
        $wpdb->query("TRUNCATE TABLE $table_name");
    
        foreach ($data['matches'] as $match) {
            $wpdb->insert(
                $table_name,
                [
                    'match_date' => $match['utcDate'],
                    'home_team' => $match['homeTeam']['name'],
                    'away_team' => $match['awayTeam']['name'],
                    'home_score' => $match['score']['fullTime']['home'] ?? null,
                    'away_score' => $match['score']['fullTime']['away'] ?? null,
                    'competition' => $match['competition']['name'],
                    'status' => $match['status'],
                ]
            );
    
            if ($wpdb->last_error) {
                error_log('Ошибка при вставке данных: ' . $wpdb->last_error);
            }
        }

        // Сбрасываем флаг, если данные успешно загружены
        update_option('barcelona_matches_table_empty', false);
    
        return true;
    }
}
