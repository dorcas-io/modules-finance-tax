@extends('layouts.tabler')
@section('head_css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="{{cdn('vendors/Datatable/data-tables.min.css')}}" rel="stylesheet" type="text/css" />
    <style>

    </style>
@endsection
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">
        <div class="card">
            <div class="card-header d-print-none">
                <h3 class="card-title">Payslip</h3>
                <div class="card-options">
                    <button type="button" class="btn btn-primary" onclick="javascript:window.print();"><i class="si si-printer"></i> Print Payslip
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="h3">{{$dorcasUser->company['data']['name']}}</p>

                    </div>
                    <div class="col-12 my-5">
                        <h1>{{\Carbon\Carbon::parse($payment_date)->format(' F Y'). ' Payslip'}}</h1>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 1%"></th>
                            <th>Authorities</th>
                            <th class="text-center" style="width: 1%"></th>
                            <th class="text-right" style="width: 1%">Payable Amount</th>
                            <th class="text-right" style="width: 1%">Amount</th>
                        </tr>
                        </thead>
                        <tbody>

                        @forelse($processed_authorities as $authorities)
                            <tr>
                                <td style="width: 1%">{{$loop->iteration}}</td>
                                <td>{{$authorities['authority_name']}}</td>
                                <td style="width: 1%;"></td>
                                <td class="text-right"  style="width: 1%">{{\Dorcas\ModulesFinanceTax\Http\Helpers\Helper::MoneyConvert($authorities['total_amount'],'full')}}</td>
                                <td class="text-right"  style="width: 1%">{{\Dorcas\ModulesFinanceTax\Http\Helpers\Helper::MoneyConvert($authorities['total_amount'],'full')}}</td>
                            </tr>
                        @empty
                        @endforelse
                        <tr>
                            <td colspan="4" class="font-weight-bold text-uppercase text-right">Total Amount</td>
                            <td class="font-weight-bold text-right"> {{'&#8358;'.'' .\Dorcas\ModulesFinanceTax\Http\Helpers\Helper::MoneyConvert($total_amount_payable,'full')}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted text-center">Thank you very much for doing business with us. We look forward to working with you again!</p>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
@endsection