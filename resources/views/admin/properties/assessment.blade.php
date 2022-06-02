{!! Form::open(['id'=>'assessment-form','route'=>'admin.properties.assessment.save','files' => true]) !!}
{!! Form::hidden('assessment_id',$assessment->id) !!}
{!! Form::hidden('property_id',$property->id) !!}


<h2 class="card-inside-title">Assessment - {{ $assessment->created_at->format('Y') }}

    @hasanyrole('Super Admin|manager')

    <div class="pull-right">
        <button type="button" id="assessment-button" class="btn btn-primary">
            Edit
        </button>
        @if($assessment->getBalanceAttribute() < 1)
        <a class="btn btn-default sticker-btn" id="sticker-btn"
            data-content="{{ route('admin.stickers', [$property->id,$assessment->created_at->format('Y')]) }}"
            href="javascript: return false;">Sticker
        </a>
        @endif
        <button style="display: none" type="submit" id="assessment-save" class="btn btn-primary"> Save</button>
        <button style="display: none" type="button" id="assessment-cancel" class="btn btn-primary"> Cancel</button>
    </div>
    @endhasanyrole
</h2>
<p class="text-muted">
    Last Printed: {{ $assessment->isPrinted() ? $assessment->last_printed_at->toDayDateTimeString() : 'Never' }}<br/>

    @php $lastPayment = $property->recentPayment()->whereYear('created_at', $assessment->created_at->format('Y'))->first() @endphp

    Last Payment: {{ $lastPayment ? $lastPayment->created_at->toDayDateTimeString() : 'Never' }} <br/>
</p>

<h4 class="card-inside-title">Demand Note Delivery</h4>
<p>{{ $assessment->isDelivered() ? 'Delivered' : 'Not Delivered' }}</p>

@if($assessment->isDelivered())
    <div class="row">
        <div class="col-sm-3">
            <h6>Recipient Name</h6>
            <p>{{ $assessment->demand_note_recipient_name }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Recipient Contact Number</h6>
            <p>{{ $assessment->demand_note_recipient_mobile }}</p>
        </div>
    </div>

    <a href="{{ $assessment->getRecipientPhoto(100,100) }}" data-sub-html="">
        <img style="max-width: 100px" class="img-responsive thumbnail" src="{{ $assessment->getRecipientPhoto(100,100) }}">
    </a>
@endif

<h4 class="card-inside-title">Assessment Details</h4>

<div class="assessment-view">
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Category</h6>
            <p>{{ $assessment->categories->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Types(Total)</h6>
            <p>{{ $assessment->typesTotal->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Types(Habitat)</h6>
            <p>{{ $assessment->types->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Wall Materials</h6>
            <p>{{ optional(App\Models\PropertyWallMaterials::find($assessment->property_wall_materials))->label}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Roofs Materials</h6>
            <p>{{ optional(App\Models\PropertyRoofsMaterials::find($assessment->roofs_materials))->label}}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Dimension</h6>
            <p>{{ optional(App\Models\PropertyDimension::find($assessment->property_dimension))->label}}
                {{optional(App\Models\PropertyDimension::find($assessment->property_dimension))->id == 1 ?"":"Sq. Meters"}}
                </p>
        </div>
        <div class="col-sm-3">
            <h6>Value Added </h6>
            <p>{{ $assessment->valuesAdded->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Use</h6>
            <p>{{optional(App\Models\PropertyUse::find($assessment->property_use))->label }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Zone </h6>
            <p>{{ optional(App\Models\PropertyZones::find($assessment->zone))->label }} </p>
        </div>
    </div>
    <div class="row">
        @if($assessment->no_of_shop!=null)
            <div class="col-sm-3">
                <h6> Number Of Shops</h6>
                <p>{{ $assessment->no_of_shop }} </p>
            </div>
        @endif
        @if($assessment->no_of_mast!=null)
            <div class="col-sm-3">
                <h6> Number Of Mast</h6>
                <p>{{ $assessment->no_of_mast }} </p>
            </div>
        @endif
        @if($assessment->no_of_compound_house!=null)
            <div class="col-sm-3">
                <h6> Number Of Compound House</h6>
                <p>{{ $assessment->no_of_compound_house }} </p>
            </div>
        @endif
        @if($assessment->compound_name!=null)
            <div class="col-sm-3">
                <h6> Compound Name</h6>
                <p>{{ $assessment->compound_name }} </p>
            </div>
        @endif
    </div>
    <div class="row">

        <div class="col-sm-3">
            <h6>Swimming Pool</h6>
            <p> {!! optional(optional($assessment)->swimming)->label !!}</p>
        </div>

        <div class="col-sm-3">
            <h6>Gated Community</h6>
            <p> {{ optional($assessment)->gated_community ? 'Yes' : 'No' }}</p>
        </div>

        <div class="col-sm-3">
            <h6>Calculated Property Rate</h6>
            <p>Le {{number_format($assessment->property_rate_without_gst,0,'',',')}}</p>
        </div>
        {{--                        <div class="col-sm-3">--}}
        {{--                            <h6>GST Calculation</h6>--}}
        {{--                            <p>Le {{number_format($assessment->property_gst,0,'',',')}}</p>--}}
        {{--                        </div>--}}
        {{--                        <div class="col-sm-3">--}}
        {{--                            <h6>Property Calculation With GST</h6>--}}
        {{--                            <p>Le {{number_format($assessment->property_rate_with_gst,0,'',',')}}</p>--}}
        {{--                        </div>--}}

    </div>


    <h6>Assessment Images</h6>
    <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <a href="{{$assessment->getAdminImageOneUrl(800,800)}}" data-sub-html="">
                <img class="img-responsive thumbnail"
                     src="{{$assessment->getAdminImageOneUrl(100,100)}}">
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <a href="{{$assessment->getAdminImageTwoUrl(800,800)}}" data-sub-html="">
                <img class="img-responsive thumbnail"
                     src="{{$assessment->getAdminImageTwoUrl(100,100)}}">
            </a>
        </div>
    </div>
</div>

<div style="display: none" class="body assessment-edit">
    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-sm-3">
            <h6>Property Category</h6>
            <p>
                {!! Form::select('property_categories[]', $categories, old('property_categories', $assessment->categories()->pluck('id')), ['class' => 'form-control', 'data-live-search' => 'true', 'id' => 'property_categories', 'multiple' => 'multiple']) !!}

            </p>
            @if ($errors->has('property_categories'))
                <label class="error">{{ $errors->first('property_categories') }}</label>
            @endif
        </div>
            <div class="col-sm-3">
                <h6>Property Types(Total)</h6>
                <p>
                    {!! Form::select('property_types_total[]', $types , $assessment->typesTotal()->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_types_total','multiple']) !!}
                </p>
                @if ($errors->has('property_types_total'))
                    <label class="error">{{ $errors->first('property_types_total') }}</label>
                @endif
            </div>
        <div class="col-sm-3">
            <h6>Property Types(Habitat)</h6>
            <p>
                {!! Form::select('property_types[]', $types , $assessment->types()->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_types','multiple']) !!}
            </p>
            @if ($errors->has('property_types'))
                <label class="error">{{ $errors->first('property_types') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Wall Materials</h6>
            <p>{!! Form::select('property_wall_materials', $wall_materials , $assessment->property_wall_materials, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_wall_materials']) !!}</p>
            @if ($errors->has('property_wall_materials'))
                <label class="error">{{ $errors->first('property_wall_materials') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Roofs Materials</h6>
            <p>{!! Form::select('roofs_materials', $roofs_materials , $assessment->roofs_materials, ['class' => 'form-control','data-live-search'=>'true','id'=>'roofs_materials']) !!}</p>
            @if ($errors->has('roofs_materials'))
                <label class="error">{{ $errors->first('roofs_materials') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Dimension(Sq. Meters)</h6>
            <p>{!! Form::select('property_dimension', $property_dimension , $assessment->property_dimension, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_dimension']) !!}</p>
            @if ($errors->has('property_dimension'))
                <label class="error">{{ $errors->first('property_dimension') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Value Added</h6>
            <p>
                {!! Form::select('property_value_added[]', $value_added , $assessment->valuesAdded->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_value_added','multiple']) !!}</p>
            @if ($errors->has('property_value_added'))
                <label class="error">{{ $errors->first('property_value_added') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Swimming Pool</h6>
            <p>
                {!! Form::select('swimming_pool', $swimmings , $assessment->swimming_id, ['class' => 'form-control','data-live-search'=>'true','id'=>'swimming_pool']) !!}</p>
            @if ($errors->has('swimming_pool'))
                <label class="error">{{ $errors->first('swimming_pool') }}</label>
            @endif
        </div>

        <div class="col-sm-3">
            <h6>Gatted Community</h6>
            <p>
                {!! Form::select('gated_community', [1 => 'Yes', 0 => 'No'] , $assessment->gated_community ? 1 : 0, ['class' => 'form-control','data-live-search'=>'true','id'=>'gated_community']) !!}</p>
            @if ($errors->has('gated_community'))
                <label class="error">{{ $errors->first('gated_community') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Use</h6>
            <p>{!! Form::select('property_use', $property_use , $assessment->property_use, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_use']) !!}</p>
            @if ($errors->has('property_use'))
                <label class="error">{{ $errors->first('property_use') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property Zone </h6>
            <p>{!! Form::select('zone', $zone , $assessment->zone, ['class' => 'form-control','data-live-search'=>'true','id'=>'zone']) !!} </p>
            @if ($errors->has('zone'))
                <label class="error">{{ $errors->first('zone') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 {{$assessment->no_of_shop==null?'hidden':''}}"
             id="div_no_of_shop">
            <h6> Number Of Shops</h6>
            <p>{!! Form::number('no_of_shop',$assessment->no_of_shop,['class'=>'form-control','id'=>'no_of_shop','max'=>20,'min'=>0]) !!} </p>
            @if ($errors->has('no_of_shop'))
                <label class="error">{{ $errors->first('no_of_shop') }}</label>
            @endif
        </div>

        <div class="col-sm-3 {{$assessment->no_of_mast==null?'hidden':''}}"
             id="div_no_of_mast">
            <h6> Number Of Mast</h6>
            <p>{!! Form::number('no_of_mast',$assessment->no_of_mast,['class'=>'form-control','id'=>'no_of_mast','max'=>20,'min'=>0]) !!} </p>
            @if ($errors->has('no_of_mast'))
                <label class="error">{{ $errors->first('no_of_mast') }}</label>
            @endif
        </div>
        <div class="col-sm-3 {{$assessment->no_of_compound_house==null?'hidden':''}}"
             id="div_no_of_compound_house">
            <h6> Number Of Compound House</h6>
            <p>{!! Form::number('no_of_compound_house',$assessment->no_of_compound_house,['class'=>'form-control','id'=>'no_of_compound_house','max'=>20,'min'=>0]) !!}</p>
            @if ($errors->has('no_of_compound_house'))
                <label class="error">{{ $errors->first('no_of_compound_house') }}</label>
            @endif
        </div>
        <div class="col-sm-3 {{$assessment->compound_name==null?'hidden':''}}"
             id="div_compound_name">
            <h6> Compound Name</h6>
            <p>{!! Form::text('compound_name',$assessment->compound_name,['class'=>'form-control','id'=>'compound_name']) !!}</p>
            @if ($errors->has('compound_name'))
                <label class="error">{{ $errors->first('compound_name') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Calculated Property Rate</h6>
            <p class="property_rate_without_gst">
                Le {{number_format($assessment->property_rate_without_gst,0,'',',')}}</p>
            {!! Form::hidden('property_rate_without_gst',$assessment->property_rate_without_gst) !!}
            {!! Form::hidden('property_rate_with_gst',$assessment->property_rate_with_gst) !!}
            {!! Form::hidden('property_gst',$assessment->property_gst) !!}
            @if ($errors->has('property_rate_without_gst'))
                <label class="error">{{ $errors->first('property_rate_without_gst') }}</label>
            @endif
        </div>
        {{--                            <div class="col-sm-3">--}}
        {{--                                <h6>GST Calculation</h6>--}}
        {{--                                <p class="property_gst">--}}
        {{--                                    Le {{number_format($assessment->property_gst,0,'',',')}}</p>--}}
        {{--                                @if ($errors->has('property_gst'))--}}
        {{--                                    <label class="error">{{ $errors->first('property_gst') }}</label>--}}
        {{--                                @endif--}}
        {{--                            </div>--}}
        {{--                            <div class="col-sm-3">--}}
        {{--                                <h6>Property Calculation With GST</h6>--}}
        {{--                                <p class="property_rate_with_gst">--}}
        {{--                                    Le {{number_format($assessment->property_rate_with_gst,0,'',',')}}</p>--}}
        {{--                                @if ($errors->has('property_rate_with_gst'))--}}
        {{--                                    <label class="error">{{ $errors->first('property_rate_with_gst') }}</label>--}}
        {{--                                @endif--}}
        {{--                            </div>--}}

    </div>
    <div class="row">
        <h6>Assessment Images</h6>
        <div class="col-sm-6">
            @if($assessment->getAdminImageOneUrl(100,100))
            <img src="{{$assessment->getAdminImageOneUrl(100,100)}}"/>
            @endif
            {!! Form::file('assessment_images_1',['class'=>'form-control']) !!}
            @if ($errors->has('assessment_images_1'))
                <label class="error">{{ $errors->first('assessment_images_1') }}</label>
            @endif
            <p>*JPG,JPEG and PNG File Allow Only</p>
        </div>
        <div class="col-sm-6">
            @if($assessment->getAdminImageTwoUrl(100,100))
            <img src="{{$assessment->getAdminImageTwoUrl(100,100)}}"/>
            @endif
            {!! Form::file('assessment_images_2',['class'=>'form-control']) !!}
            @if ($errors->has('assessment_images_2'))
                <label class="error">{{ $errors->first('assessment_images_2') }}</label>
            @endif
            <p>*JPG,JPEG and PNG File Allow Only</p>
        </div>
    </div>
</div>
{!! Form::close() !!}
