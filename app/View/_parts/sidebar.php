<aside class="navbar navbar-overlap navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="h1 navbar-brand navbar-horizontal pe-0 pe-md-3 d-lg-none">
            <a href="<?php echo $Helper::base('/'); ?>">
                <?php echo $Helper::config('settings.name'); ?>
            </a>
        </div>
        <div class="navbar-nav flex-row d-lg-none">
            <?php require $Helper::path('app/View/_parts/user_nav.php'); ?>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <?php $routes = require $Helper::path('app/External/sidebar_routes.php');
            if (!empty($routes)) {

                echo '
                <ul class="navbar-nav">';
                // 3-level sidebar menu
                $subLinks = '';
                foreach ($routes as $name => $details) { // Level 1

                    if (isset($details['link']) !== false && !$Helper::authorization($details['link'])) {
                        continue;
                    }


                    $level2Active = false;
                    $subLinks2 = '';

                    if (isset($details['children']) !== false) {

                        foreach ($details['children'] as $name2 => $details2) { // Level 2

                            if (isset($details2['link']) !== false && !$Helper::authorization($details2['link'])) {
                                continue;
                            }

                            $active2 = isset($details2['link']) !== false ?
                                $Helper::currentPage($details2['link'], '', true) :
                                false;

                            $dropdown3 = false;
                            $subLinks3 = '';
                            $level3HasActive = false;
                            if (isset($details2['children']) !== false) {
                                foreach ($details2['children'] as $name3 => $details3) { // Level 3

                                    if (isset($details3['link']) !== false && !$Helper::authorization($details3['link'])) {
                                        continue;
                                    }

                                    $active3 = isset($details3['link']) !== false ?
                                        $Helper::currentPage($details3['link'], '', true) :
                                        false;

                                    $subLinks3 .= '<a class="dropdown-item' . ($active3 ? ' active' : '') . '" href="' . $Helper::base($details3['link']) . '">' . $Helper::lang('base.' . $name3) . ' ' . (isset($details3['badge']) !== false ? $details3['badge'] : '') . '</a>';

                                    if ($active3) {
                                        $level3HasActive = true;
                                    }
                                }
                            }

                            $subLinks2 .= !empty($subLinks3) ? '<div class="dropend">
                                    <a class="dropdown-item dropdown-toggle' . ($level3HasActive ? ' active show' : '') . '" href="javascript:;" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                        ' . $Helper::lang('base.' . $name2) . '
                                        ' . (isset($details2['badge']) !== false ? $details2['badge'] : '') . '
                                    </a>
                                    <div class="dropdown-menu' . ($level3HasActive ? ' show' : '') . '">' . $subLinks3 . '</div>' : '<a class="dropdown-item' . ($active2 ? ' active' : '') . '" href="' . $Helper::base($details2['link']) . '">' . $Helper::lang('base.' . $name2) . ' ' . (isset($details2['badge']) !== false ? $details2['badge'] : '') . '</a>';

                            if ($active2 || $level3HasActive) {
                                $level2Active = true;
                            }
                        }
                    }

                    $subLinks .= !empty($subLinks2) ? '<li class="nav-item dropdown' . ($level2Active ? ' active' : '') . '">
                        <a class="nav-link dropdown-toggle' . ($level2Active ? ' show' : '') . '" href="javascript:;" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                            ' . (isset($details['icon']) !== false ? '<span class="nav-link-icon d-md-none d-lg-inline-block ' . $details['icon'] . '">
                            </span>' : '') . '
                            <span class="nav-link-title">
                            ' . $Helper::lang('base.' . $name) . '
                            </span>
                            ' . (isset($details['badge']) !== false ? $details['badge'] : '') . '
                        </a>
                        <div class="dropdown-menu' . ($level2Active ? ' show' : '') . '"><div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">' . $subLinks2 . '</div></div></div>' : '<li class="nav-item' . ($Helper::currentPage($details['link'], '', true) ? ' active' : '') . '">
                        <a class="nav-link" href="' . $Helper::base($details['link']) . '">
                        ' . (isset($details['icon']) !== false ? '<span class="nav-link-icon d-md-none d-lg-inline-block ' . $details['icon'] . '">
                            </span>' : '') . '    
                        <span class="nav-link-title">
                            ' . $Helper::lang('base.' . $name) . '
                            </span>
                            ' . (isset($details['badge']) !== false ? $details['badge'] : '') . '
                        </a>';
                }

                echo
                $subLinks . '</ul>';
            }   ?>
        </div>
    </div>
</aside>