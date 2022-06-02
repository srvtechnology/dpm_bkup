@extends('admin.layout.main')


@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Enter Details</h2>

                </div>
                <div class="body">
                    {!! Form::open(['id' => 'create_user']) !!}
                        <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" value="{{ old('name') }}" name="name" required>
                                <label class="form-label">Full Name</label>
                                @if ($errors->has('name'))
                                    <label class="error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="radio" name="gender" value="M" id="male" {{ old('gender')=='M'?'checked="checked"': ''}} class="with-gap radio-col-blue" required>
                                    <label for="male">Male</label>

                                    <input type="radio" name="gender" value="F" id="female" {{ old('gender')=='F'?'checked="checked"': ''}} class="with-gap radio-col-blue" required>
                                    <label for="female" class="m-l-20">Female</label>
                                    @if ($errors->has('gender'))
                                        <label class="error">{{ $errors->first('gender') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="email" class="form-control" value="{{ old('email') }}" name="email" required>
                                        <label class="form-label">Email</label>
                                        @if ($errors->has('email'))
                                            <label class="error">{{ $errors->first('email') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="password" class="form-control" minlength="6" name="password" required>
                                <label class="form-label">Password</label>
                                @if ($errors->has('password'))
                                    <label class="error">{{ $errors->first('password') }}</label>
                                @endif
                            </div>
                        </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{old('street_number')}}" name="street_number">
                                    <label class="form-label">Street Number</label>
                                    @if ($errors->has('street_number'))
                                        <label class="error">{{ $errors->first('street_number') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" name="street_name" class="form-control"  value="{{old('street_name')}}">
                                    <label class="form-label">Street Name</label>
                                    @if ($errors->has('street_name'))
                                        <label class="error">{{ $errors->first('street_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="form-label">Ward</label>
                                <select name="ward" id="ward" class="form-control show-tick">
                                    <option value="">-- Please Select Ward --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('ward')->distinct()->get() as $ward)
                                        <option value="{{$ward['ward']}}" {{ old('ward')==$ward['ward']?'selected': ''}}>{{$ward['ward']}}</option>
                                        @endforeach
                                </select>
                                @if ($errors->has('ward'))
                                    <label class="error">{{ $errors->first('ward') }}</label>
                                @endif

                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Constituency</label>
                                <select name="constituency" id="constituency" class="form-control show-tick">
                                    <option value="">-- Select Constituency --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('constituency')->distinct()->get() as $ward)
                                        <option value="{{$ward['constituency']}}" {{ old('constituency')==$ward['constituency']?'selected': ''}}>{{$ward['constituency']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('constituency'))
                                    <label class="error">{{ $errors->first('constituency') }}</label>
                                @endif

                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Section</label>
                                <select name="section" id="section" class="form-control show-tick">
                                    <option value="">-- Select Section --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('section')->distinct()->get() as $ward)
                                        <option value="{{$ward['section']}}" {{ old('section')==$ward['section']?'selected': ''}}>{{$ward['section']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('section'))
                                    <label class="error">{{ $errors->first('section') }}</label>
                                @endif

                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Chiefdom</label>
                                <select name="chiefdom" id="chiefdom" class="form-control show-tick">
                                    <option value="">-- Select Chiefdom --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('chiefdom')->distinct()->get() as $ward)
                                        <option value="{{$ward['chiefdom']}}" {{ old('chiefdom')==$ward['chiefdom']?'selected': ''}}>{{$ward['chiefdom']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('chiefdom'))
                                    <label class="error">{{ $errors->first('chiefdom') }}</label>
                                @endif

                            </div>


                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="form-label">District</label>
                                <select name="district" id="district" class="form-control show-tick">
                                    <option value="">-- Select District --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('district')->distinct()->get() as $ward)
                                        <option value="{{$ward['district']}}" {{ old('district')==$ward['district']?'selected': ''}}>{{$ward['district']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('district'))
                                    <label class="error">{{ $errors->first('district') }}</label>
                                @endif

                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Province</label>
                                <select name="province" id="province" class="form-control show-tick">
                                    <option value="">-- Select Province --</option>
                                    @foreach(App\Models\BoundaryDelimitation::select('province')->distinct()->get() as $ward)
                                        <option value="{{$ward['province']}}" {{ old('province')==$ward['province']?'selected': ''}}>{{$ward['province']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('province'))
                                    <label class="error">{{ $errors->first('province') }}</label>
                                @endif

                            </div>

                            <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::checkbox('is_active', 1, old('is_active'), ['class' => 'filled-in chk-col-blue', 'id' => 'is_active']) !!}
                                        <label for="is_active">Status</label>
                                    </div>
                            </div>

                        </div>
                        <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>

    @endpush
