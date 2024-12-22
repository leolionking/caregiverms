<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="cms-login-wrap">
    <div class="cms-login-form">
        <h2><?php _e('Caregiver Login', 'caregiver-management-system'); ?></h2>
        
        <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
            <div class="cms-error-message">
                <?php _e('Invalid email or password', 'caregiver-management-system'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(wp_login_url()); ?>">
            <div class="cms-form-group">
                <label for="user_login"><?php _e('Email Address', 'caregiver-management-system'); ?></label>
                <input type="email" name="log" id="user_login" class="cms-form-control" required>
            </div>

            <div class="cms-form-group">
                <label for="user_pass"><?php _e('Password', 'caregiver-management-system'); ?></label>
                <input type="password" name="pwd" id="user_pass" class="cms-form-control" required>
            </div>

            <div class="cms-form-group">
                <label>
                    <input type="checkbox" name="rememberme" value="forever">
                    <?php _e('Remember Me', 'caregiver-management-system'); ?>
                </label>
            </div>

            <?php wp_nonce_field('cms-login'); ?>
            
            <button type="submit" class="button button-primary">
                <?php _e('Login', 'caregiver-management-system'); ?>
            </button>

            <p class="cms-login-links">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                    <?php _e('Forgot Password?', 'caregiver-management-system'); ?>
                </a>
            </p>
        </form>
    </div>
</div>