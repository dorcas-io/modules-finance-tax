<div class="modal fade" id="tax-authorities-edit-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update  Tax Authority</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" @submit.prevent="updateAuthority()"  id="tax_authority_edit" method="post">
                    <fieldset>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label" for="authority">Name</label>
                                <input class="form-control" id="authority" v-model="authority.name" placeholder="Enter Tax Authority Name" type="text" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="authority">Payment Mode</label>
                                <select  v-model="authority.payment_mode" class="form-control custom-select " tabindex="-1" required >
                                    <option selected :value="authority.payment_mode" >@{{ authority.payment_mode }}</option>
                                    <option value="paystack">paystack</option>
                                    <option value="flutterwave">flutterwave</option>
                                </select>

                            </div>
                            <div class="form-group col-md-12">

                                <label class="form-label" for="authority">Default Payment Details</label>

                                <div class="form-row">
                                    <input type="text"  class=" col-md-4 form-control mr-4 mt-3" v-model="default_bank_detail.bank"  placeholder="Add Bank Name"  />
                                    <input type="text"  class=" col-md-4 form-control mr-4 mt-3" v-model="default_bank_detail.account"  placeholder="Add Account Number"/>

                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="authority">Other Payment Details</label>

                                <div v-for="(item,index) in bank_details">

                                    <div class="d-flex flex-row ">

                                        <input type="text"  class=" col-md-4 form-control mr-4 mt-3" v-model="item.bank"  placeholder="Add Bank Name" />
                                        <input type="text"  class=" col-md-4 form-control mr-4 mt-3" v-model="item.account"  placeholder="Add Account Number" />
                                        <div class="">
                                            <button type="button" class=" mt-2 btn btn-icon btn-primary btn-danger"  @click="deleteValue(index)" ><i class="fe fe-trash"></i></button>

                                        </div>


                                    </div>
                                </div>
                                <button type="button" class="mt-4 btn btn-outline-primary" @click="addValue()"><i class="fe fe-plus mr-2"></i>More Payment Details</button>


                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="edit-authority" form="tax_authority_edit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
