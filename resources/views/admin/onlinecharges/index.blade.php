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
                            Online Payments
                        </h2>
                    </div>
                </div>

                <div class="body">

            @if($onlineCharges->count())
                <div class="">

                    <div class="row">
                        <div class="body table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Mobile Number</th>
                                    <th>Payee Name</th>
                                    <th>Payment Mode</th>
                                    <th>Amount[USD]</th>
                                    <th>Amount[Le]</th>
                                    <th>Is Complete</th>
                                    <th>Online Charge[%]</th>
                                    <th>Online Charge Amount</th>
                                    <th>Total Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $online_charge = $total_amount = 0;  @endphp
                                @foreach($onlineCharges as $charges)

                                    @php $total_amount = $total_amount+$charges->total_amount;$online_charge = $online_charge+$charges->online_charge_in_amount;  @endphp
                                    <tr>
                                        <td>{{ $charges->mobile_number }}</td>
                                        <td>{{$charges->payee_name}}</td>
                                        <td>{{$charges->payment_mode}}</td>
                                        <td>USD {{ number_format($charges->amount,2) }}</td>
                                        <td>Le {{ number_format($charges->amount_in_le,2) }}</td>
                                        <td>{{ $charges->is_complete?"Yes":"No" }}</td>
                                        <td>{{ $charges->online_charge_in_percent?:0 }}%</td>
                                        <td>USD {{ number_format($charges->online_charge_in_amount,2) }}</td>
                                        <td>USD {{ number_format($charges->total_amount,2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" style="text-align: right;"><b>Online Charge:</b></td>
                                    <td colspan="1"><b>USD {{number_format($online_charge,2)}}</b></td>
                                    <td colspan="4" style="text-align: right;"><b>Grand Total Amount:</b></td>
                                    <td colspan="1"><b>USD {{number_format($total_amount,2)}}</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            @endif

        </div>
    </div>



@endsection
