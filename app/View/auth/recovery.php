<div class="page page-center kx-auth">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="<?php echo $Helper::base(); ?>" class="navbar-brand">
                <img src="<?php echo $Helper::assets('img/x/logo.svg'); ?>" alt="KalipsoX" class="navbar-brand-image">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-4"><?php echo $Helper::lang('auth.recovery'); ?></h2>
                <?php
                switch ($step) {
                    case 'request':
                ?>
                        <p class="text-secondary mb-4"><?php echo $Helper::lang('auth.recovery_desc'); ?></p>
                        <form data-kx-form action="<?php echo $Helper::base('auth/recovery'); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $Helper::lang('auth.email'); ?></label>
                                <input type="email" required name="email" class="form-control" placeholder="email@example.com">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    <div class="btn-loader spinner-border spinner-border-sm text-light" role="status"></div>
                                    <span class="btn-text"><?php echo $Helper::lang('auth.recovery'); ?></span>
                                </button>
                            </div>
                        </form>
                    <?php
                        break;
                    case 'reset':
                    ?>
                        <p class="text-secondary mb-4"><?php echo $Helper::lang('auth.recovery_password_desc'); ?></p>
                        <form data-kx-form action="<?php echo $Helper::base('auth/recovery?token=' . $token); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label
                                "><?php echo $Helper::lang('auth.new_password'); ?></label>
                                <div class="input-group input-group-flat">
                                    <input type="password" required name="password" class="form-control">
                                    <span class="input-group-text">
                                        <a data-kx-action="show_password" class="link-secondary" title="<?php echo $Helper::lang('auth.show_password'); ?>" data-bs-toggle="tooltip">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </span>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label
                                "><?php echo $Helper::lang('auth.new_password_confirmation'); ?></label>
                                <div class="input-group input-group-flat">
                                    <input type="password" required name="password_again" class="form-control">
                                    <span class="input-group-text">
                                        <a data-kx-action="show_password" class="link-secondary" title="<?php echo $Helper::lang('auth.show_password'); ?>" data-bs-toggle="tooltip">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </span>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    <span class="btn-loader
                                    spinner-border spinner-border-sm text-light" role="status"></span>
                                    <span class="btn-text"><?php echo $Helper::lang('base.reset'); ?></span>
                                </button>
                            </div>
                        </form>
                <?php
                        break;
                }   ?>
            </div>
        </div>
        <?php
        $section = 'recovery';
        $layout = require $Helper::path('app/View/_parts/auth_footer.php');
        echo $layout; ?>
    </div>
</div>