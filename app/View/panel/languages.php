<script>
    window.languageList = <?php echo json_encode($languages); ?>
</script>
<div class="page-body" id="languageEditor">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $Helper::lang('base.languages'); ?></h3>
                        <div class="card-options">
                            <?php if ($Helper::authorization('dashboard/languages/save')) : ?>
                                <button class="btn btn-sm btn-primary" @click="addTerm()">
                                    <?php echo $Helper::lang('base.add_term'); ?>
                                </button>
                                <button class="ms-1 btn btn-sm btn-primary" @click="addLanguage()">
                                    <?php echo $Helper::lang('base.add_language'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form id="languageForm" class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr class="sticky-bottom bottom-0">
                                    <th class="w-auto"><?php echo $Helper::lang('base.term'); ?></th>
                                    <th class="w-25" v-for="language in languages" class="w-1">{{language}}</th>
                                    <th class="w-1"><?php echo $Helper::lang('base.actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, key) in languageTerms" :key="key">
                                    <td><input class="form-control form-control-rounded form-control-sm" :value="key" type="text" @change="updateTerm(key, $event.target.value)" /></td>
                                    <td v-for="lang in languages" :key="lang">
                                        <input class="form-control form-control-sm" v-model="languageTerms[key][lang]" type="text" />
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