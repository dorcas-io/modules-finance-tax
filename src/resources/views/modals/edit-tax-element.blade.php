<div class="modal fade" id="tax-element-edit-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update  Tax Element</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form  @submit.prevent="updateElement()"  id="tax_element_edit" method="put">
                    <fieldset>
                        <div class="form-group col-md-12">
                            <label class="form-label" >Name </label>
                            <input class="form-control"  type="text" v-model="elements_edit_form.element_name" >
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label" for="authority">Authority</label>
                            <input class="form-control" id="authority" :value="authority.name"  type="text" readonly>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="form-label" >Frequency</label>
                            <select  v-model="elements_edit_form.frequency"  class="form-control custom-select " tabindex="-1" required  @change="toggleEditFrequency($event)">
                                <option selected :value="elements_edit_form.frequency"  >@{{ elements_edit_form.frequency }}</option>
                                <option value="yearly">Yearly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 " v-show="elements_edit_form.isYearly">
                            <label class="form-label" >Add Run  Month and Date </label>
                            <input  type="text"  class="form-control custom-datepicker" v-model="elements_edit_form.frequency_year" >
                        </div>
                        <div class="form-group col-md-12" v-show="elements_edit_form.isMonthly">
                            <label class="form-label" for="element_type">Add Tax Run Day </label>
                            <input  type="number" class="col-md-4 form-control mr-4 mt-3" v-on:keyup="validateEditDay($event)"  v-model="elements_edit_form.frequency_month " placeholder="Add Tax Run Day eg (21)" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label" for="element_type">Element Type</label>
                            <select  v-model="elements_edit_form.element_type" class="form-control custom-select " tabindex="-1" required  @change="toggleEditElementType($event)">
                                <option selected :value="elements_edit_form.element_type"  > @{{ elements_edit_form.element_type }} </option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12" v-show="elements_edit_form.isPercent">
                            <label class="form-label" for="element_type">Add Tax Percentage Value </label>
                            <input type="hidden"   v-model="elements_edit_form.type_data.element_type = 'Percentage'"  />
                            <input type="number"  class=" col-md-4 form-control mr-4 mt-3"  v-model="elements_edit_form.type_data.value " placeholder="Add Value eg (5) " required/>
                        </div>

                            <div class="form-group col-md-12">
                                <select  v-model="elements_edit_form.target_accounts" class="form-control" multiple required>
                                    <option v-for="account in elements_edit_form.accounts"  selected  :value="account.id">@{{ account.display_name }}</option>
                                    <option v-for="account in elements_edit_form.unselected_accounts"    :value="account.id">@{{ account.display_name }}</option>
                                </select>
                            </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="edit-element" form="tax_element_edit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
