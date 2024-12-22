<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '../../../includes/models/class-caregiver.php';
use CMS\Models\Caregiver;

$caregiver = new Caregiver(get_current_user_id());
$upcoming_shifts = $caregiver->get_upcoming_shifts();
?>

<?php if ($upcoming_shifts) : ?>
    <ul class="cms-upcoming-shifts">
        <?php foreach ($upcoming_shifts as $shift) : ?>
            <li class="cms-upcoming-shift">
                <div class="cms-shift-date">
                    <i class="dashicons dashicons-calendar"></i>
                    <?php echo date_i18n('l, M j', strtotime($shift->shift_date)); ?>
                    <?php
                    if (date('Y-m-d') === $shift->shift_date) {
                        echo '<span class="cms-today-badge">' . __('Today', 'caregiver-management-system') . '</span>';
                    }
                    ?>
                </div>
                <div class="cms-shift-details">
                    <span class="cms-shift-type <?php echo esc_attr($shift->shift_type); ?>">
                        <?php echo esc_html(ucfirst($shift->shift_type)); ?>
                    </span>
                    <?php if (!empty($shift->location)) : ?>
                        <span class="cms-shift-location">
                            <i class="dashicons dashicons-location"></i>
                            <?php echo esc_html($shift->location); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p class="cms-no-shifts">
        <i class="dashicons dashicons-calendar-alt"></i>
        <?php _e('No upcoming shifts scheduled', 'caregiver-management-system'); ?>
    </p>
<?php endif; ?>