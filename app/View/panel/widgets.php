<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $Helper::lang('base.widgets'); ?></h3>
                        <div class="card-options">
                            <?php if ($Helper::authorization('dashboard/widgets/add')) : ?>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                                    <?php echo $Helper::lang('base.add_widget'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php echo \KX\Helper\HTML::tableLayout('widgets'); ?>
                </div>
            </div>
        </div>
    </div>
</div>