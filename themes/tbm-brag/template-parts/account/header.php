<?php if (is_user_logged_in()) : ?>
    <div class="d-flex border-bottom pb-2 justify-content-between">
        <h1>My Dashboard</h1>
        <a class="text-dark" href="<?php echo wp_logout_url(); ?>">Logout</a>
    </div>
<?php endif; ?>