        <div class="page page-center">
            <div class="container-tight py-4">
                <div class="empty">
                    <div class="empty-header"><?php echo $code; ?></div>
                    <p class="empty-title"><?php echo $description; ?></p>
                    <p class="empty-subtitle text-secondary">
                        <?php echo $subText; ?>
                    </p>
                    <?php
                    if (isset($link['text']) !== false && isset($link['url']) !== false) {
                    ?>
                        <div class="empty-action">
                            <a href="<?php echo $link['url']; ?>" class="btn btn-primary">
                                <i class="ti ti-arrow-left"></i>
                                <span class="d-inline-block ms-2"><?php echo $link['text']; ?></span>
                            </a>
                        </div>
                    <?php
                    }   ?>
                </div>
            </div>
        </div>