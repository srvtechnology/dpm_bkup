@extends('admin.layout.main')

@section('content')

    @include('admin.layout.partial.alert')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Edit Payment</h2>

                </div>
                <div class="body">
                    {!! Form::open(['id' => 'forgot_password', 'route' => ['admin.payment.update', $payment->id]]) !!}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="amount">Assessment Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="assessment"
                                                   value="{{ old('assessment') ? old('assessment') : number_format($payment->assessment,0,'',',') }}"
                                                   name="assessment" class="form-control"
                                                   placeholder="Enter assessment Amount"
                                                   onkeyup = "javascript:this.value=Comma(this.value);"
                                            >
                                        </div>
                                        {!! $errors->first('assessment', '<span class="error">:message</span>') !!}
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <label for="amount">Paying Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="amount"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->amount,0,'',',') }}"
                                                   name="amount" class="form-control"
                                                   placeholder="Enter Amount Paying"
                                                   onkeyup = "javascript:this.value=Comma(this.value);"
                                            >
                                        </div>
                                        {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                    </div>

                                </div>

                                <div class="col-md-3">
                                    <label for="amount">Penalty</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="penalty"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->penalty,0,'',',') }}"
                                                   name="penalty" class="form-control"
                                                   placeholder="Enter Amount Paying"  onkeyup = "javascript:this.value=Comma(this.value);">
                                        </div>
                                        {!! $errors->first('penalty', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="amount">Total Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="total"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->total,0,'',',') }}" disabled
                                                   class="form-control" style="background-color: #eee;padding-left: 5px;" placeholder="">
                                        </div>
                                        {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">


                                <div class="col-md-3">
                                    <label for="payment_type">Payment Type</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::select('payment_type', ['' => 'Select', 'cash' => 'Cash', 'cheque'=> 'Cheque', 'online'=> 'Online'], old('payment_type', $payment->payment_type), ['class' => 'form-control']) !!}
                                        </div>
                                        {!! $errors->first('payment_type', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label for="email_address">Cheque No</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="cheque_number" value="{{ old('cheque_number', $payment->cheque_number) }}"
                                                   name="cheque_number" class="form-control"
                                                   placeholder="Cheque Number">
                                        </div>
                                        {!! $errors->first('cheque_number', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>



                        <div class="col-sm-3">
                            <label for="email_address">Payee Name</label>
                            <div class="form-group">
                                <div class="form-line" id="payee_name">
                                    <input type="text" id="payee_name" value="{{ old('payee_name', $payment->payee_name) }}"
                                           name="payee_name" class="form-control"
                                           placeholder="Payee Name">
                                </div>
                                {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">

                            <div class="form-group">
                                <div class="form-line" id="created_at">
                                    <label>Created At</label>
                                    {!! Form::text('created_at', \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'), ['class' => 'form-control datepicker']) !!}

                                </div>
                                {!! $errors->first('created_at', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="col-sm-6 text-left">
                            <button type="submit" class="btn btn-primary m-t-15 waves-effect btn-lg">Save</button>
                        </div>
                    </div>


                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>

    <script>
        jQuery("#amount, #penalty").on('keyup', function () {
            var amount = jQuery("#amount").val();
            var penalty = jQuery("#penalty").val();

            amount = amount.replace(/,/g, '');
            penalty = penalty.replace(/,/g, '');

            if(amount=='')
            {
                amount = 0;
            }
            if(penalty=='')
            {
                penalty = 0;
            }
            var total = parseInt(amount) + parseInt(penalty);

            jQuery("#total").val(Comma(total));

        });


        function Comma(Num) { //function to add commas to textboxes
            Num += '';
            Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
            Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
            x = Num.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1))
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            return x1 + x2;
        }

    </script>
@endpush
