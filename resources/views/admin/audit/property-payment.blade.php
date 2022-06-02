@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>
@endpush
@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="">
                        <h2>
                            Property Payment Audit filter
                        </h2>
                    </div>
                </div>
                <div class="body">
                    {!! Form::open(['method' => 'get', 'id' => 'filter-form']) !!}
                    <div class="row row-flex">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Last At</label>
                                    <div class="form-line">
                                        {!! Form::select('last_at', $lastday, request()->last_at, ['class' => 'form-control']) !!}
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                <input type="submit" value="Filter" id="filter-button" class="btn btn-success"
                                           style="width: 100%;">
                                </div>
                            </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>



            @if($activity->count())
                <div class="card">
                    <div class="header">
                        <h2>
                            Property Payment Audit Log
                        </h2>
                    </div>
                    <div class="row">
                        <div class="body table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Property ID</th>
                                    <th>Payment ID</th>
                                    <th>Description</th>
                                    <th>By User Name</th>
                                    {{-- <th>Properties</th> --}}
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($activity as $amd)

                                    <tr>
                                        <td>{{ optional($amd->subject)->property_id }}</td>
                                        <td>{{ $amd->subject_id  }}</td>
                                        <td class="{{$amd->description}}">{{$amd->description}}</td>
                                        <td>{{ $amd->causer->getName() }}</td>
                                        {{-- <th>{{$amd->properties}}</th> --}}
                                        <td>{{ $amd->created_at }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info modal-btn" data-toggle="modal"
                                        data-target="#myModal-{{$amd->id}}">View
                                            </button>
                                            <div class="modal fade report" id="myModal-{{$amd->id}}" role="dialog">
                                                <div class="modal-dialog">

                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Property Payment</h4>

                                                        </div>
                                                        <div class="modal-body" style="color: black;">
                                                            @foreach (json_decode($amd->properties) as $key => $properties)
                                                                 <h4>{{$key}}</h4>
                                                                  @foreach ($properties as $key => $value)
                                                                    <p><Strong>{{$key}}</Strong>: {{$value}}</p>
                                                                  @endforeach
                                                            @endforeach


                                                        </div>
                                                        <div class="modal-footer">
                                                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                            {{ $activity->appends(request()->all())->links() }}
                        </div>

                    </div>
                </div>

            @endif

        </div>
    </div>



@endsection

@push('scripts')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="{{ url('admin/js/dhtmlxcombo.js') }}"></script>


@endpush
