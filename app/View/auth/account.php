<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                    <div class="card-body">
                        <?php /*<h4 class="subheader">Business settings</h4> */ ?>
                        <div class="list-group list-group-transparent">
                            <a href="<?php echo $Helper::base('auth'); ?>" class="list-group-item list-group-item-action d-flex align-items-center<?php echo $Helper::currentPage('auth'); ?>">
                                <i class="ti ti-user icon dropdown-item-icon"></i>
                                <?php echo $Helper::lang('auth.account'); ?>
                            </a>
                            <a href="<?php echo $Helper::base('auth/notifications'); ?>" class="list-group-item list-group-item-action d-flex align-items-center<?php echo $Helper::currentPage('auth/notifications'); ?>">
                                <i class="ti ti-bell icon dropdown-item-icon"></i>
                                <?php echo $Helper::lang('base.notifications'); ?>
                            </a>
                            <a href="<?php echo $Helper::base('auth/sessions'); ?>" class="list-group-item list-group-item-action d-flex align-items-center<?php echo $Helper::currentPage('auth/sessions'); ?>">
                                <i class="ti ti-device-tablet-star icon dropdown-item-icon"></i>
                                <?php echo $Helper::lang('auth.sessions'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                    <?php
                    switch ($section) {
                        case 'notifications':
                            echo $notificationList;
                            break;
                        case 'sessions':
                            echo 's';
                            break;
                        default:
                    ?> <form data-kx-form action="<?php echo $Helper::base('auth'); ?>" method="post">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo $Helper::lang('base.settings'); ?></h3>
                                    <?php
                                    /*
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="avatar avatar-xl" style="background-image: url(./static/avatars/000m.jpg)"></span>
                                </div>
                                <div class="col-auto"><a href="#" class="btn">
                                        Change avatar
                                    </a></div>
                                <div class="col-auto"><a href="#" class="btn btn-ghost-danger">
                                        Delete avatar
                                    </a></div>
                            </div> */ ?>
                                    <div class="row g-3">
                                        <div class="col-md">
                                            <div class="form-label"><?php echo $Helper::lang('auth.username'); ?></div>
                                            <input type="text" class="form-control" value="<?php echo $userInfo->u_name; ?>" readonly>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-label"><?php echo $Helper::lang('auth.first_name'); ?></div>
                                            <input type="text" class="form-control" value="<?php echo $userInfo->f_name; ?>" name="first_name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-label"><?php echo $Helper::lang('auth.last_name'); ?></div>
                                            <input type="text" class="form-control" value="<?php echo $userInfo->l_name; ?>" name="last_name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-auto">
                                            <div class="form-label"><?php echo $Helper::lang('auth.birthdate'); ?></div>
                                            <input type="text" class="form-control date-birth" value="<?php echo ($userInfo->b_date ? date('Y-m-d', (int)$userInfo->b_date) : null); ?>" name="birthdate">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <h3 class="card-title mt-4"><?php echo $Helper::lang('auth.email'); ?></h3>
                                    <p class="card-subtitle"><?php echo $Helper::lang('auth.email_change_info'); ?></p>
                                    <div>
                                        <div class="row g-2">
                                            <div class="col-auto">
                                                <input type="email" name="email" class="form-control w-auto" value="<?php echo $userInfo->email; ?>">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="card-title mt-4"><?php echo $Helper::lang('auth.password'); ?></h3>
                                    <p class="card-subtitle"><?php echo $Helper::lang('auth.password_change_desc'); ?></p>
                                    <div>
                                        <a href="#" class="btn" data-kx-action="set_new_password">
                                            <?php echo $Helper::lang('auth.set_new_password'); ?>
                                        </a>
                                        <div class="row g-2 d-none set-new-password">
                                            <div class="col-auto">
                                                <input type="password" name="password" class="form-control" value="" placeholder="<?php echo $Helper::lang('auth.new_password'); ?>">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-auto">
                                                <input type="password" name="password_again" class="form-control" value="" placeholder="<?php echo $Helper::lang('auth.new_password_confirmation'); ?>">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php /*
                            <h3 class="card-title mt-4">Public profile</h3>
                            <p class="card-subtitle">Making your profile public means that anyone on the Dashkit network will be able to find
                                you.</p>
                            <div>
                                <label class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox">
                                    <span class="form-check-label form-check-label-on">Youe currently visible</span>
                                    <span class="form-check-label form-check-label-off">Youe
                                        currently invisible</span>
                                </label>
                            </div> */ ?>
                                </div>
                                <div class="card-footer bg-transparent mt-auto">
                                    <div class="btn-list justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="btn-loader spinner-border spinner-border-sm text-light" role="status"></span>
                                            <span class="btn-text"><?php echo $Helper::lang('base.save'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                    <?php
                            break;
                    }   ?>
                </div>
            </div>
        </div>
    </div>
</div>