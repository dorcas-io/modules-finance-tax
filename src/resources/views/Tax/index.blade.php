@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')

    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
            <div class="col-md-9 col-xl-9">
                <div class="row row-cards row-deck " >
                        <div class="col-sm-6">
                                    @component('layouts.blocks.tabler.empty-fullpage')
                                        @slot('title')
                                         Tax Authorities
                                        @endslot
                                        <a href="{{route('tax-authorities')}}" class="btn btn-primary">View </a>
                                        &nbsp;
                                        @slot('buttons')
                                        @endslot
                                    @endcomponent

                        </div>
        </div>

    </div>
    </div>

@endsection
@section('body_js')

@endsection
