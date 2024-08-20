<script>
    window.languageList = <?php echo json_encode($languages); ?>
</script>
<div class="page-body" id="languageEditor">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header sticky-top top-0" style="background-color: var(--tblr-body-bg)">
                        <h3 class="card-title">
                            <?php echo $Helper::lang('base.languages'); ?>
                            <small>
                        </h3>
                        <div class="card-options">
                            <?php if ($Helper::authorization('dashboard/languages/save')) : ?>
                                <button class="btn btn-sm btn-primary" @click="addTerm()">
                                    <?php echo $Helper::lang('base.add_term'); ?>
                                </button>
                                <button class="ms-1 btn btn-sm btn-primary" @click="addLanguage()">
                                    <?php echo $Helper::lang('base.add_language'); ?>
                                </button>
                                <button class="ms-1 btn btn-sm btn-success" @click="save()">
                                    <?php echo $Helper::lang('base.save'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form id="languageForm" class="table-responsive">
                        <table class="w-auto min-vw-100 table table-vcenter card-table table-bordered">
                            <thead>
                                <tr>
                                    <th class="sticky-top top-0"><?php echo $Helper::lang('base.term'); ?></th>
                                    <th class="sticky-top top-0" v-for="language in languages">{{language}}</th>
                                    <th class="sticky-top top-0" class="w-1"><?php echo $Helper::lang('base.actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, key) in languageTerms" :key="key">
                                    <td><input :id="'term_' + key" class="form-control form-control-rounded form-control-sm" :value="key" type="text" @change="updateTerm(key, $event.target.value)" /></td>
                                    <td v-for="lang in languages" :key="lang">
                                        <div class="text-secondary small">{{lang}}</div>
                                        <div>
                                            <input class="form-control form-control-sm" v-model="languageTerms[key][lang]" type="text" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-sm btn-block btn-outline-danger" type="button" @click="deleteTerm(key)"><?php echo $Helper::lang('base.delete'); ?></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>