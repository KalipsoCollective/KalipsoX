<div class="nav-item">
    <a data-kx-action="refresh" data-kx-pjax="true" class="nav-link px-0" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="<?php echo $Helper::lang('base.refresh'); ?>" title="<?php echo $Helper::lang('base.refresh'); ?>">
        <i class="ti ti-reload"></i>
    </a>
</div>
<div class="nav-item">
    <a data-kx-action="fullscreen" class="nav-link px-0" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="<?php echo $Helper::lang('base.fullscreen'); ?>" title="<?php echo $Helper::lang('base.fullscreen'); ?>">
        <i class="ti"></i>
    </a>
</div>
<div class="nav-item dropdown">
    <a data-kx-action="toggle_theme" class="nav-link px-0" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="<?php echo $Helper::lang('base.toggle_theme'); ?>" title="<?php echo $Helper::lang('base.toggle_theme'); ?>">
        <i class="ti"></i>
    </a>
</div>
<div class="nav-item dropdown">
    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" tabindex="-1" aria-label="<?php echo $Helper::lang('base.show_notifications'); ?>">
        <i class="ti ti-bell"></i>
        <span class="badge bg-red d-none notification-dot"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card p-0">
        <div class="card notification-card">
            <div class="card-header align-items-center">
                <h3 class="card-title d-flex w-100">
                    <span><?php echo $Helper::lang('base.latest_notifications'); ?></span>
                    <a href="<?php echo $Helper::base('auth/notifications'); ?>" class="ms-auto btn btn-sm"><?php echo $Helper::lang('base.see_all'); ?></a>
                </h3>
            </div>
            <?php /*
            <div class="card-body">
                <p class="text-center text-muted"><?php echo $Helper::lang('base.no_notifications'); ?></p>
            </div>
            */ ?>
            <div class="notification-list">
            </div>
        </div>
    </div>
</div>
<?php $userInfo = $Helper::sessionData('user'); ?>
<div class="nav-item dropdown ms-2">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
        <span class="avatar avatar-sm">
            <?php // style="background-image: url(./static/avatars/000m.jpg)" 
            ?>
            <?php echo $Helper::firstLetters($userInfo->name); ?>
        </span>
        <div class="d-none d-xl-block ps-2">
            <div>
                <?php echo $userInfo->name; ?>
            </div>
            <div class="mt-1 small text-secondary"><?php echo $userInfo->email; ?></div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" data-bs-theme="dark">
        <a href="<?php echo $Helper::base('auth'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth'); ?>">
            <i class="ti ti-user icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.account'); ?>
        </a>
        <a href="<?php echo $Helper::base('auth/notifications'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth/notifications'); ?>">
            <i class="ti ti-bell icon dropdown-item-icon"></i> <?php echo $Helper::lang('base.notifications'); ?>
            <span class="badge bg-danger text-primary-fg ms-auto notification-count d-none"></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="<?php echo $Helper::base('auth/sessions'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth/sessions'); ?>">
            <i class="ti ti-device-tablet-star icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.sessions'); ?>
        </a>
        <a data-kx-action="<?php echo $Helper::base('auth/logout'); ?>" href="javascript:;" class="dropdown-item">
            <i class="ti ti-power icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.logout'); ?>
        </a>
    </div>
</div>