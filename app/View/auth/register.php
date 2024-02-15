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
        <form data-kx-form action="<?php echo $Helper::base('auth/register'); ?>" method="post" autocomplete="off" novalidate>
          <div class="mb-3">
            <label class="form-label"><?php echo $Helper::lang('auth.username'); ?></label>
            <input type="text" require name="username" class="form-control" autocomplete="off">
          </div>
          <div class="mb-2">
            <label class="form-label"><?php echo $Helper::lang('auth.email'); ?></label>
            <input type="email" require name="email" class="form-control" placeholder="email@example.com" autocomplete="off">
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
            </div>
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100"><?php echo $Helper::lang('auth.register'); ?></button>
          </div>
        </form>
      </div>
    </div>
    <p class="text-secondary mt-3 mb-0 d-flex justify-content-center align-items-center">
      <span class="me-1"><?php echo $Helper::lang('auth.you_have_already_account'); ?></span>
      <a class="me-1" href="<?php echo $Helper::base('auth/login'); ?>" tabindex="-1"><?php echo $Helper::lang('auth.login'); ?>.</a>
      <button class="btn btn-sm btn-ghost-primary" data-kx-action="toggle_theme" data-bs-toggle="tooltip" title="<?php echo $Helper::lang('base.toggle_theme'); ?>" tabindex="-1">
        <i class="ti ti-sun"></i>
      </button>
    </p>
    <p class="text-center text-secondary my-1 small">
      <?php echo $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name'); ?>
    </p>
  </div>
</div>