<?php
if (!defined('ABSPATH')) {
    exit;
}

if (empty($players)) {
    echo '<p>Нет данных об игроках.</p>';
    return;
}
?>

<table border="1" cellpadding="10">
    <tr>
        <th>Имя</th>
        <th>Позиция</th>
        <th>Национальность</th>
        <th>Дата рождения</th>
        <th>Номер</th>
    </tr>
    <?php foreach ($players as $player) : ?>
        <tr>
            <td><?php echo esc_html($player->player_name); ?></td>
            <td><?php echo esc_html($player->position); ?></td>
            <td><?php echo esc_html($player->nationality); ?></td>
            <td><?php echo esc_html($player->date_of_birth); ?></td>
            <td><?php echo esc_html($player->shirt_number); ?></td>
        </tr>
    <?php endforeach; ?>
</table>