<div class="nav-item dropdown">
    <a data-kx-action="toggle_theme" class="nav-link px-0" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="<?php echo $Helper::lang('base.toggle_theme'); ?>" title="<?php echo $Helper::lang('base.toggle_theme'); ?>">
        <i class="ti"></i>
    </a>
</div>
<div class="nav-item dropdown">
    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="<?php echo $Helper::lang('base.show_notifications'); ?>">
        <i class="ti ti-bell"></i>
        <span class="badge bg-red"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Last updates</h3>
            </div>
            <div class="list-group list-group-flush list-group-hoverable">
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
                        <div class="col text-truncate">
                            <a href="#" class="text-body d-block">Example 1</a>
                            <div class="d-block text-secondary text-truncate mt-n1">
                                Change deprecated html tags to text decoration classes (#29604)
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="list-group-item-actions">
                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot d-block"></span></div>
                        <div class="col text-truncate">
                            <a href="#" class="text-body d-block">Example 2</a>
                            <div class="d-block text-secondary text-truncate mt-n1">
                                justify-content:between ⇒ justify-content:space-between (#29734)
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="list-group-item-actions show">
                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot d-block"></span></div>
                        <div class="col text-truncate">
                            <a href="#" class="text-body d-block">Example 3</a>
                            <div class="d-block text-secondary text-truncate mt-n1">
                                Update change-version.js (#29736)
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="list-group-item-actions">
                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot status-dot-animated bg-green d-block"></span></div>
                        <div class="col text-truncate">
                            <a href="#" class="text-body d-block">Example 4</a>
                            <div class="d-block text-secondary text-truncate mt-n1">
                                Regenerate package-lock.json (#29730)
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="list-group-item-actions">
                                <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
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
        <?php if ($Helper::authorization('auth')) {
        ?>
            <a href="<?php echo $Helper::base('auth'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth'); ?>">
                <i class="ti ti-user icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.account'); ?>
            </a>
        <?php
        }
        if ($Helper::authorization('auth/notifications')) {
        ?>
            <a href="<?php echo $Helper::base('auth/notifications'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth/notifications'); ?>">
                <i class="ti ti-bell icon dropdown-item-icon"></i> <?php echo $Helper::lang('base.notifications'); ?>
                <span class="badge bg-danger text-primary-fg ms-auto">12</span>
            </a>
        <?php
        }
        ?>
        <div class="dropdown-divider"></div>
        <?php
        if ($Helper::authorization('auth/sessions')) {
        ?>
            <a href="<?php echo $Helper::base('auth/sessions'); ?>" class="dropdown-item<?php echo $Helper::currentPage('auth/sessions'); ?>">
                <i class="ti ti-device-tablet-star icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.sessions'); ?>
            </a>
        <?php
        } ?>
        <a href="<?php echo $Helper::base('auth/logout'); ?>" class="dropdown-item">
            <i class="ti ti-power icon dropdown-item-icon"></i> <?php echo $Helper::lang('auth.logout'); ?>
        </a>
    </div>
</div>