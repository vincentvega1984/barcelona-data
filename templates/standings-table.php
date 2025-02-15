<?php
if (!defined('ABSPATH')) {
    exit;
}

if (empty($standings)) {
    echo '<p>Нет данных о турнирной таблице.</p>';
    return;
}
?>

<table border="1" cellpadding="10">
    <tr>
        <th>Позиция</th>
        <th>Команда</th>
        <th>Игры</th>
        <th>Победы</th>
        <th>Ничьи</th>
        <th>Поражения</th>
        <th>Голы</th>
        <th>Разница</th>
        <th>Очки</th>
    </tr>
    <?php foreach ($standings as $team) : ?>
        <tr>
            <td><?php echo esc_html($team->position); ?></td>
            <td><?php echo esc_html($team->team_name); ?></td>
            <td><?php echo esc_html($team->played_games); ?></td>
            <td><?php echo esc_html($team->won); ?></td>
            <td><?php echo esc_html($team->draw); ?></td>
            <td><?php echo esc_html($team->lost); ?></td>
            <td><?php echo esc_html($team->goals_for . ' - ' . $team->goals_against); ?></td>
            <td><?php echo esc_html($team->goal_difference); ?></td>
            <td><?php echo esc_html($team->points); ?></td>
        </tr>
    <?php endforeach; ?>
</table>