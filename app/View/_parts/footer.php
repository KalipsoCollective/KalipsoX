   <footer class="footer footer-transparent d-print-none">
       <div class="container-xl">
           <div class="row text-center align-items-center flex-row-reverse">
               <div class="col-lg-auto ms-lg-auto">
                   <ul class="list-inline list-inline-dots mb-0">
                       <?php
                        global $kxAvailableLanguages, $kxLang, $kxRequestUri;
                        echo '
                        <li class="list-inline-item">
                            <div class="dropdown">
                            <a href="#" class="link-secondary" data-bs-toggle="dropdown">
                                <i class="ti ti-language"></i> ' . $Helper::lang('base.language') . ' (' . strtoupper($kxLang) . ')
                            </a>';

                        echo '
                                <div class="dropdown-menu">
                                    ';
                        foreach ($kxAvailableLanguages as $lang) {
                            echo '<a class="dropdown-item' . ($lang === $kxLang ? ' active' : '') . '" data-direct href="' . $Helper::base($kxRequestUri . '?lang=' . $lang) . '">
                                    ' . $Helper::lang('langs.' . $lang) . '
                                    </a>';
                        }
                        echo '
                                </div>
                            </div>
                        </li>';
                        ?>
                   </ul>
               </div>
               <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                   <ul class="list-inline list-inline-dots mb-0">
                       <?php echo '<li class="list-inline-item">' . $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name') . '</li>'; ?>
                       <li class="list-inline-item">
                           <a href="javascript:;" class="link-secondary" rel="noopener">
                               <?php echo KX_VERSION; ?>
                           </a>
                       </li>
                   </ul>
               </div>
           </div>
       </div>
   </footer>
   </div>