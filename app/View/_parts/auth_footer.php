<?php
return match ($section) {
    'login' => '
        <p class="text-secondary mt-3 mb-0 d-flex justify-content-center align-items-center">
            <span class="me-1">' . $Helper::lang('auth.dont_have_account_yet') . '</span>
            <a tabindex="7" class="me-1" href="' . $Helper::base('auth/register') . '" tabindex="-1">' . $Helper::lang('auth.register') . '.</a>
            <button tabindex="8" class="btn btn-sm btn-ghost-primary" data-kx-action="toggle_theme" data-bs-toggle="tooltip" title="' . $Helper::lang('base.toggle_theme') . '" tabindex="-1">
                <i class="ti ti-sun"></i>
            </button>
        </p>
        <p class="text-center text-secondary my-1 small">
            ' . $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name') . '
        </p>',
    'recovery' => '
        <p class="text-secondary mt-3 mb-0 d-flex justify-content-center align-items-center">
            <span class="me-1">' . $Helper::lang('auth.dont_have_account_yet') . '</span>
            <a tabindex="6" class="me-1" href="' . $Helper::base('auth/register') . '" tabindex="-1">' . $Helper::lang('auth.register') . '.</a>
            <button tabindex="7" class="btn btn-sm btn-ghost-primary" data-kx-action="toggle_theme" data-bs-toggle="tooltip" title="' . $Helper::lang('base.toggle_theme') . '" tabindex="-1">
                <i class="ti ti-sun"></i>
            </button>
        </p>
        <p class="text-center text-secondary my-1 small">
            ' . $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name') . '
        </p>',
    'register' => '
    <p class="text-secondary mt-3 mb-0 d-flex justify-content-center align-items-center">
      <span class="me-1">' . $Helper::lang('auth.you_have_already_account') . '</span>
      <a tabindex="6" class="me-1" href="' . $Helper::base('auth/login') . '" tabindex="-1">' . $Helper::lang('auth.login') . '.</a>
      <button tabindex="7" class="btn btn-sm btn-ghost-primary" data-kx-action="toggle_theme" data-bs-toggle="tooltip" title="' . $Helper::lang('base.toggle_theme') . '" tabindex="-1">
        <i class="ti ti-sun"></i>
      </button>
    </p>
    <p class="text-center text-secondary my-1 small">
      ' . $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name') . '
    </p>',
    'verify' => '
     <p class="text-center text-secondary my-1 small">
        ' . $Helper::lang('base.copyright') . ' © ' . date('Y') . ' - ' . $Helper::config('settings.name') . '
    </p>'
};
