    <?php if (isset($layout['beforeBodyClose']) !== false) echo $layout['beforeBodyClose']; ?>
    <script src="<?php echo $Helper::assets('libs/tabler/tabler.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/jquery-pjax/jquery.pjax.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/jquery-timeago/jquery.timeago.min.js'); ?>"></script>
    <?php global $kxLang;
    if ($kxLang === 'tr') { ?>
        <script src="<?php echo $Helper::assets('libs/jquery-timeago/locale/tr.min.js'); ?>"></script>
    <?php } ?>
    <script src="<?php echo $Helper::assets('libs/flatpickr/flatpickr.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/flatpickr/locale/tr.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/luxon/luxon.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/nprogress/nprogress.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/toastify/toastify.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/datatables/datatables.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('libs/vue/vue.min.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('js/modules.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('js/script.js'); ?>"></script>
    <script src="<?php echo $Helper::assets('js/app.js'); ?>"></script>

    </body>

    </html>