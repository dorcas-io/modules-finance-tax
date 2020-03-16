@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <div class="col-sm-12" id="tax_authority">
                    @if(!empty($tax_authorities))
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('authority-search') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="authorities-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="name">Authority</th>
                                    <th data-field="payment_mode">Payment Mode</th>
                                    <th data-field="created_at">Added On</th>
                                    <th data-field="buttons">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @else
                        <div class="col s12" >
                            @component('layouts.blocks.tabler.empty-fullpage')
                                @slot('title')
                                    No Tax Authority Generated
                                @endslot
                                <a href="#" class="btn btn-primary" v-on:click.prevent="setTaxAuthority">Add Tax Authority</a>
                                &nbsp;
                                @slot('buttons')
                                @endslot
                            @endcomponent
                        </div>
                    @endif
                </div>

                @include('modules-finance-tax::modals.add-tax-authority')


            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        app.currentUser = {!! json_encode($dorcasUser) !!};
        const table = $('.bootstrap-table');
        let  Modal = new Vue({
                el: '#tax-authorities-add-modal',
                data() {
                    return {
                        form_data:{
                            default_fields: { bank: '', account: ''},
                            fields: [],
                            authority_name:'',
                            payment_type:'',
                            token:'{{csrf_token()}}',
                        }

                    }
                },
                methods: {
                    deleteValue: function(index){
                        this.form_data.fields.splice(index, 1);
                    },
                    addValue: function() {
                        this.form_data.fields.push({ bank: '', account: ''});
                        // this.$emit('input', this.fields);
                    },

                    submitForm: function () {
                        $('#submit-authority').addClass('btn-loading btn-icon')
                        axios.post('/mfn/tax-authorities',this.form_data)
                            .then(response=>{
                                $('#submit-authority').removeClass('btn-loading btn-icon')
                                this.form_data = {};
                                dropdown.hideTaxAuthorityModal();
                                swal({
                                    title:"Success!",
                                    text:"Tax Authority Successfully Created",
                                    type:"success",
                                    showLoaderOnConfirm: true,
                                }).then(function () {
                                    location.reload()
                                });
                            })
                            .catch(e=>{
                                console.log(e.response.data);
                                $('#submit-authority').removeClass('btn-loading btn-icon')

                                swal.fire({
                                    title:"Error!",
                                    text:e.response.data.message,
                                    type:"error",
                                    showLoaderOnConfirm: true,
                                });
                            })
                    }

                },

            });
        let dropdown = new Vue({
            el : '#sub-menu-action',
            data:{

            },
            methods:{
                viewTaxAuthorityModal(){
                    $('#tax-authorities-add-modal').modal('show');
                },
                hideTaxAuthorityModal(){
                    $('#tax-authorities-add-modal').modal('hide');

                }
            },

        })
        new Vue({
            el: '#tax_authority',
            data:{
            },
            methods:{
                clickAction: function (event) {
                    //console.log(event.target);
                    let target = event.target;
                    if (!target.hasAttribute('data-action')) {
                        target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                    }
                    //console.log(target, target.getAttribute('data-action'));
                    let action = target.getAttribute('data-action');
                    let name = target.getAttribute('data-name');
                    let id = target.getAttribute('data-id');
                    let index = parseInt(target.getAttribute('data-index'), 10);
                    switch (action) {
                        case 'view':
                            return true;
                        case 'delete_authority':
                            this.deleteAuthority(id,index,name)
                    }

                },
                setTaxAuthority(){
                    dropdown.viewTaxAuthorityModal()
                },
                deleteAuthority(id,index,name){
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete  " + name + " from this Authorities.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return axios.delete("/mfn/tax-authorities/" + id)
                                .then(function (response) {
                                    $('#authorities-table').bootstrapTable('removeByUniqueId', response.data.id);
                                    return swal("Deleted!", "The authority was successfully deleted.", "success");
                            }).catch(function (error) {
                                var message = '';
                                console.log(error);
                                    swal.fire({
                                        title:"Error!",
                                        text:error.response.data.message,
                                        type:"error",
                                        showLoaderOnConfirm: true,
                                    });
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()


                    });
                }

            },
            mounted(){
                table.bootstrapTable({data: this.authorities})
            }
        })

        function processRows(row, index) {
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
                row.buttons = '<a class="btn btn-sm btn-primary text-white" href="/mfn/tax-authorities/'+row.id+'">View</a> &nbsp; ' +
                    '<a class="btn btn-sm btn-danger text-white" data-index="'+index+'" data-action="delete_authority" data-id="'+row.id+'" data-name="'+row.name+'">Delete</a>'
            // row.account_link = '<a href="/mfn/finance-entries?account=' + row.account.data.id + '">' + row.account.data.display_name + '</a>';
            // row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            // row.buttons = '<a class="btn btn-danger btn-sm remove" data-action="remove" href="#" data-id="'+row.id+'">Delete</a>';
            // if (typeof row.account.data !== 'undefined' && row.account.data.name == 'unconfirmed') {
            //     row.buttons += '<a class="btn btn-warning btn-sm views" data-action="views" href="/mfn/finance-entries/' + row.id + '" >Confirm</a>'
            // }
            // return row;
        }
    </script>
@endsection
