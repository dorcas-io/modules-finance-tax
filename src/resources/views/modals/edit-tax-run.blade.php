<div class="modal fade" id="tax-run-edit-modal" tabindex="-1" role="dialog" aria-labelledby="run-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Tax Run</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="editRun()"  id="tax_run_edit" method="post">
                    <fieldset>
                        <div class="row">
                            <div class="form-group col-md-12 "  >
                                <label class="form-label mt-4">Run Name </label>
                                <input type="text" class="form-control" v-model="form_data.run_name" placeholder="Enter Name of Tax Run">
                            </div>
                            <div class="form-group col-md-12 "  >
                                <label class="form-label mt-4">Activity</label>
                                <label class="custom-switch">
                                    <input type="checkbox" v-model="form_data.status" class="custom-switch-input" :checked="form_data.status">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Activity</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="edit-run" form="tax_run_edit" class="btn btn-primary">Update Run</button>
            </div>
        </div>
    </div>
</div>
