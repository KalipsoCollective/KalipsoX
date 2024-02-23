<div class="page page-center kx-auth">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="<?php echo $Helper::base(); ?>" class="navbar-brand">
                <img src="<?php echo $Helper::assets('img/x/logo.svg'); ?>" alt="KalipsoX" class="navbar-brand-image">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4"><?php echo $Helper::lang('auth.login_desc'); ?></h2>
                <form data-kx-form action="<?php echo $Helper::base('auth/login'); ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $Helper::lang('auth.email_or_username'); ?></label>
                        <input type="text" required name="username" class="form-control" placeholder="email@example.com">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            <?php echo $Helper::lang('auth.password'); ?>
                            <span class="form-label-description">
                                <a href="<?php echo $Helper::base('auth/recovery'); ?>"><?php echo $Helper::lang('auth.recovery_account'); ?></a>
                            </span>
                        </label>
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
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" name="remember_me" class="form-check-input">
                            <span class="form-check-label"><?php echo $Helper::lang('auth.remember_me'); ?></span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <span class="btn-loader spinner-border spinner-border-sm text-light" role="status"></span>
                            <span class="btn-text"><?php echo $Helper::lang('auth.login'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        $section = 'login';
        $layout = require $Helper::path('app/View/_parts/auth_footer.php');
        echo $layout; ?>
    </div>
</div>