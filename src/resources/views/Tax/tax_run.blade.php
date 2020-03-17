@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('head_css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('body_content_main')
    @include('layouts.blocks.tabler.alert')

    <div class="row">
        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9" id="tax-run">
            <div class="row row-cards row-deck " >
                <div  class="col-sm-12" id="tax_run" >
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                               data-pagination="true"
                               data-search="true"
                               data-side-pagination="server"
                               data-show-refresh="true"
                               data-unique-id="id"
                               data-id-field="id"
                               data-row-attributes="processRows"
                               data-url="{{ route('run-search') . '?' . http_build_query($args)  . '&id='. $element[0]['id']}}"
                               data-page-list="[10,25,50,100,200,300,500]"
                               data-sort-class="sortable"
                               data-search-on-enter-key="true"
                               id="tax-run"
                               v-on:click="clickAction($event)">
                            <thead>
                            <tr>
                                <th data-field="name">Authority</th>
                                <th data-field="isActive"> Active</th>
                                <th data-field="created_at">Added On</th>
                                <th data-field="buttons">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    @include('modules-finance-tax::modals.edit-tax-run')

                </div>




            </div>

        </div>
        @include('modules-finance-tax::modals.add-tax-run')

    </div>

    @endsection
@section('body_js')
    <script>
        app.currentUser = {!! json_encode($dorcasUser) !!};
        let  Modal = new Vue({
            el: '#tax-run-add-modal',
            data() {
                return {
                    form_data:{
                        run_name: null,
                        selected_element:{!! json_encode($element[0]['id']) !!}
                    }
                }
            },
            methods: {
                submitForm: function () {
                    $('#submit-run').addClass('btn-loading btn-icon')
                    axios.post('/mfn/tax-run',this.form_data)
                        .then(response=>{
                            $('#submit-run').removeClass('btn-loading btn-icon')
                            this.form_data = {};
                            $('#tax-run-add-modal').modal('hide');
                            swal({
                                title:"Success!",
                                text:"Tax Run Successfully Created",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            $('#submit-run').removeClass('btn-loading btn-icon')
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


        let  TaxRun = new Vue({
            el: '#tax-run',
            data() {
                return {
                    runs:[],
                    form_data:{
                    run_name: null,
                    selected_element: {!! json_encode($element[0]['id']) !!}
                }
            }
            },
            methods: {
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
                    let status = target.getAttribute('data-status');
                    let index = parseInt(target.getAttribute('data-index'), 10);
                    switch (action) {
                        case 'view':
                            return true;
                        case 'edit_run':
                            this.form_data['run_name'] = name;
                            this.form_data['run'] = id;
                            this.form_data['status'] = ( status === 'True' ?  1 : 0);
                            $('#tax-run-edit-modal').modal('show')
                    }

                },
                editRun(){
                    console.log(this.form_data)
                    axios.put('/mfn/tax-run/'+this.form_data.run, this.form_data)
                        .then(response=>{
                            $('#edit-run').removeClass('btn-loading btn-icon');
                            form_data = {};
                            $('#tax-run-edit-modal').modal('hide');

                            swal({
                                title:"Success!",
                                text:"Tax Run Successfully Updated",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            console.log(e.response.data);
                            $('#edit-run').removeClass('btn-loading btn-icon')
                            swal.fire({
                                title:"Error!",
                                text:e.response.data.message,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        })
                },

            },



        });

        function processRows(row,index) {
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            if(row.status !== 'processed'){
                '<a class="btn btn-warning" data-status="'+row.isActive+'" data-index="'+index+'"  data-action="edit_run" data-id="'+row.id+'" data-name="'+row.name+'">Update</a> &nbsp;';

            }
            row.buttons =
                '<a class="btn btn-sm btn-primary text-white"  href="/mfn/tax-run/total/authorities/' + row.id + '">View Tax  Authorities Slip</a> &nbsp;';


        }

    </script>
    @endsection