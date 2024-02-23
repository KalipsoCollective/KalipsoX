<div class="page page-center kx-auth">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="<?php echo $Helper::base(); ?>" class="navbar-brand">
                <img src="<?php echo $Helper::assets('img/x/logo.svg'); ?>" alt="KalipsoX" class="navbar-brand-image">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4"><?php echo $Helper::lang('auth.verify_account'); ?></h2>
                <?php if (isset($alert['type']) !== false) { ?>
                    <div class="alert alert-<?php echo $alert['type']; ?>" role="alert">
                        <?php echo $alert['message']; ?>
                    </div>
                <?php } else { ?>
                    <p class="text-center mb-4">
                        <?php echo $Helper::lang('auth.verify_account_desc'); ?>
                    </p>
                <?php } ?>
            </div>
        </div>
        <?php
        $section = 'verify';
        $layout = require $Helper::path('app/View/_parts/auth_footer.php');
        echo $layout; ?>
    </div>
</div>