<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $Helper::lang('base.logs'); ?></h3>
                        <div class="card-options">
                        </div>
                    </div>
                    <?php echo \KX\Helper\HTML::tableLayout('logs'); ?>
                </div>
            </div>
        </div>
    </div>
</div>