<div class="page page-center kx-auth">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="<?php echo $Helper::base(); ?>" class="navbar-brand">
        <img src="<?php echo $Helper::assets('img/x/logo.svg'); ?>" alt="KalipsoX" class="navbar-brand-image">
      </a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h2 class="h2 text-center mb-4"><?php echo $Helper::lang('auth.register_desc'); ?></h2>
        <?php if ($Helper::config('settings.registration_system', true) !== true) { ?>
          <div class="alert alert-warning" role="alert">
            <div class="d-flex">
              <i class="icon alert-icon ti ti-alert-circle"></i>
              <div>
                <div>
                  <h4 class="alert-title"><?php echo $Helper::lang('base.sorry'); ?></h4>
                  <div class="text-secondary"><?php echo $Helper::lang('auth.registration_system_disabled'); ?></div>
                </div>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <form data-kx-form action="<?php echo $Helper::base('auth/register'); ?>" method="post" autocomplete="off">
            <div class="mb-3">
              <label class="form-label"><?php echo $Helper::lang('auth.username'); ?></label>
              <input type="text" required name="username" class="form-control" autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            <div class="mb-2">
              <label class="form-label"><?php echo $Helper::lang('auth.email'); ?></label>
              <input type="email" required name="email" class="form-control" placeholder="email@example.com" autocomplete="off">
              <div class="invalid-feedback"></div>
            </div>
            <div class="mb-2">
              <label class="form-label">
                <?php echo $Helper::lang('auth.password'); ?>
              </label>
              <div class="input-group input-group-flat">
                <input type="password" require name="password" class="form-control" autocomplete="off">
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
                <span class="btn-loader spinner-border spinner-border-sm text-light" role="status"></span>
                <span class="btn-text"><?php echo $Helper::lang('auth.register'); ?></span>
              </button>
            </div>
          </form>
        <?php } ?>
      </div>
    </div>
    <?php
    $section = 'register';
    $layout = require $Helper::path('app/View/_parts/auth_footer.php');
    echo $layout; ?>
  </div>
</div>