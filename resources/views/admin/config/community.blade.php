@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>SYSTEM SETTING</h2>
    </div>
@endsection

@section('form')

    {!! Form::open() !!}

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="body">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">Gated Community</label>
                            {!! Form::text('gated_community', old(\App\Logic\SystemConfig::OPTION_GATED_COMMUNITY, $optionGroup->{\App\Logic\SystemConfig::OPTION_GATED_COMMUNITY}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('gated_community', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">CURRENCY RATE (1 USD IN Le.)</label>
                            {!! Form::text('currency_rate', old(\App\Logic\SystemConfig::CURRENCY_RATE, $optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('currency_rate', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">CURRENCY RATE (1 Pound IN Le.)</label>
                            {!! Form::text('currency_rate_pound', old(\App\Logic\SystemConfig::CURRENCY_RATE_POUND, $optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE_POUND}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('currency_rate_pound', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">Online Changes (In(%))</label>
                            {!! Form::text('online_charge', old(\App\Logic\SystemConfig::ONLINE_CHARGE, $optionGroup->{\App\Logic\SystemConfig::ONLINE_CHARGE}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('online_charge', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection
