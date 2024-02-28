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
                            if (!empty($notifications) && count($notifications) >= 20) {
                                echo '<div class="card-footer bg-transparent mt-auto">
                                    <div class="btn-list justify-content-center more-notifications">
                                        <a href="javascript:;" data-kx-action="' . $Helper::base('auth/notifications?page=2') . '" class="btn btn-primary">
                                            <span class="btn-text">' . $Helper::lang('base.more') . '</span>
                                        </a>
                                    </div>
                                </div>';
                            }
                            break;
                        case 'sessions':
                            echo '
                            <div class="card-body">
                                <h3 class="card-title">' . $Helper::lang('auth.sessions') . '</h3>
                                <p class="card-subtitle">' . $Helper::lang('auth.sessions_desc') . '</p>';

                            if (!empty($sessions)) {
                                echo '<div class="list-group list-group-flush list-group-hoverable">';
                                global $kxAuthToken;
                                foreach ($sessions as $session) {

                                    $session->header  = $Helper::userAgentDetails($session->header);

                                    echo '
                                    <div class="list-group-item sessions-' . $session->id . '">
                                        ' . ($kxAuthToken === $session->auth_token ? '<div class="ribbon ribbon-top ribbon-start bg-green"><i class="ti ti-star"></i></div>' : '') . '
                                        <div class="row align-items-center">
                                            <div class="col-auto"><span class="status-dot d-block' . ($session->last_act_at > strtotime('-5 minutes') ? ' status-dot-animated bg-green' : '') . '"></span></div>
                                                <div class="col">
                                                    <div class="text-body d-inline-block" title="' . $session->header['user_agent'] . '">
                                                        <i class="' . $session->header['b_icon'] . '"></i> ' . $session->header['browser'] . ' ' . $session->header['version'] . '
                                                    </div>
                                                    <time class="ms-2 timeago badge badge-outline text-blue" datetime="' . date('c', (int)$session->last_act_at) . '">' . date('d.m H:i', (int)$session->last_act_at) . '</time>
                                                    <div class="d-block text-secondary mt-1">
                                                        <i title="' . $session->auth_token . '" class="ti ti-fingerprint"></i> ' . $session->ip . ' &middot; 
                                                        <i title="' . $session->header['platform'] . '" class="' . $session->header['p_icon'] . '"></i> ' . $session->header['os'] . ' &middot;
                                                        <i class="ti ti-eye-pin"></i> <kbd>' . $session->last_act_on . '</kbd>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="javascript:;" data-kx-again data-kx-action="' . $Helper::base('auth/logout/' . $session->id) . '" class="list-group-item-actions">
                                                        <i class="ti ti-power icon"></i>
                                                    </a>
                                                </div>
                                        </div>
                                    </div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p class="text-muted">' . $Helper::lang('base.no_more_data') . '</p>';
                            }
                            echo '</div>
                            <div class="card-footer bg-transparent mt-auto">
                                <div class="btn-list justify-content-end">
                                    <a href="javascript:;" data-kx-again data-kx-action="' . $Helper::base('auth/logout/all') . '" class="btn btn-danger">
                                        <span class="btn-text"><i class="ti ti-imp icon"></i> ' . $Helper::lang('auth.sessions_delete_all') . '</span>
                                    </a>
                                </div>
                            </div>';
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