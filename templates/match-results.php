<?php
/**
 * Шаблон для вывода результатов матчей.
 *
 * @param array $matches Массив данных о матчах.
 */

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа
}

if (empty($matches)) {
    echo '<p>Нет данных о матчах.</p>';
    return;
}
?>

<table border="1" cellpadding="10">
    <tr>
        <th>Дата</th>
        <th>Дома</th>
        <th>Гости</th>
        <th>Счет</th>
        <th>Турнир</th>
        <th>Статус</th>
    </tr>
    <?php foreach ($matches as $match) : ?>
        <tr>
            <td><?php echo date('d.m.Y H:i', strtotime($match->match_date)); ?></td>
            <td><?php echo esc_html($match->home_team); ?></td>
            <td><?php echo esc_html($match->away_team); ?></td>
            <td><?php echo $match->home_score . ' - ' . $match->away_score; ?></td>
            <td><?php echo esc_html($match->competition); ?></td>
            <td><?php echo esc_html($match->status); ?></td>
        </tr>
    <?php endforeach; ?>
</table>