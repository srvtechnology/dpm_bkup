<div class="receipt">
    <div class="container">
        <div class="receipt-content">
            <div class="row">
                <div class="col-lg-12">
                    <p class="text-uppercase text-center" style="text-align:center; font-size: 12px; margin-bottom: 5px;"><strong>PLEASE
                            BRING THIS DEMAND NOTE
                            TOGETHER
                            WITH
                            RECEIPTS FOR ALL PREVIOUS INSTALLMENTS AT TIME OF PAYMENT</strong></p>
                </div>

                <table class="" width="100%">
                    <tr>
                        <td align="left" style="width: 18%;">
                            <img style="height: 105px;width: auto;" src="{{ asset('images/LOGO2.png') }}" width="150px" height="1200px" alt="">
                        </td>
                        <td style="text-align:center;">
                            <h1 class="text-center" style="font-weight:300;">WESTERN AREA RURAL DISTRICT COUNCIL -
                                WARDC</h1>
                            <h6 class="text-center mb-4" style="margin:0;font-weight:300;">OLD MOTOR ROAD, WATERLOO</h6>
                            <h4 class="text-center font-weight-400" style="margin-bottom:5px;font-weight:400;">PROPERTY
                                RATE DEMAND NOTE</h4>
                            <h6 class="text-center  mb-3" style="margin:0; font-weight:bold;">JANUARY –
                                DECEMBER {{ $assessment->created_at->year }}</h6>
                        </td>
                        <td align="right" style="width: 18%;">
                            <!-- <img style="padding: 0 5px;" src="{{ asset('images/LOGO.png') }}" alt=""> -->
                            <img style="object-fit: cover; height: 105px; width:  125px;" src="{{ $assessment->getImageAnyUrl(85,85,true) }}" alt="">
                        </td>
                    </tr>
                </table>

                <div class="col-lg-12">
                    <table class="table table-bordered"
                           style=" border:1px solid #eee;width:100%; margin-bottom:5px;margin-top:5px;">
                        <thead>
                        <tr>
                            <th scope="col" colspan="7">ASSESSMENT DETAILS</th>
                        </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:5px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #eee; text-align:left;width: 10%" scope="col"
                                class="text-left">
                                <span style="white-space: pre">{{ $property->is_organization ? 'ORGANIZATION' : 'OWNER' }} NAME</span>
                            </th>
                            <th style="border:1px solid #eee;"
                                scope="col">{{ $property->is_organization ?  $property->organization_name :  (optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname) }}</th>
                            <th style="border:1px solid #eee;width: 5%" scope="col">
                                <span style="white-space: pre">TEL:</span>
                            </th>
                            <th style="border:1px solid #eee; width: 10%"
                                scope="col">
                                <span style="white-space: pre">{{ $property->landlord->mobile_1 }} {{ (strlen( $property->landlord->mobile_2) > 5) ?  ', ' . $property->landlord->mobile_2  : '' }}</span>
                            </th>
                        </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:5px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #eee;text-align:left; width: 10%" scope="col"
                                class="text-left">
                                <span style="white-space: pre">PROPERTY ADDRESS</span>
                            </th>
                            <th style="border:1px solid #eee;" scope="col">{{ $property->street_number }}
                                , {{ $property->street_name }}, Ward:{{ $property->ward }},
                                Constituency:{{ $property->constituency }}, {{ $property->section }}
                                , {{ str_replace('Province','Area',$property->district) }}
                                , {{ str_replace('Province','Area',$property->province) }}</th>
                        </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered" style=" width:100%;margin-bottom:5px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #eee; text-align: left" scope="col">CATEGORY</th>
                            <th style="border:1px solid #eee;" scope="col">PROPERTY TYPE</th>
                            <th style="border:1px solid #eee;white-space: pre" scope="col">PROPERTY AREA (M<sup>2</sup>)</th>
                            <th style="border:1px solid #eee;" scope="col">WALL MATERIAL</th>
                            <th style="border:1px solid #eee;" scope="col">ROOF MATERIAL</th>
                            <th style="border:1px solid #eee;" scope="col" width="20%">PROPERTY USE</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border:1px solid #eee;">{{ strtoupper($assessment->categories->pluck('label')->implode(', ')) }}</td>
                            <td style="border:1px solid #eee;">{{ strtoupper($assessment->types->pluck('label')->implode(', ')) }}</td>
                            <td style="border:1px solid #eee;">{{ strtoupper(optional(optional($assessment)->dimension)->label) }} {{ optional(optional($assessment)->dimension)->id == 1 ? '' : ' SQ METERS' }}</td>
                            <td style="border:1px solid #eee;">{{ strtoupper(optional(optional($assessment)->wallMaterial)->label) }}</td>
                            <td style="border:1px solid #eee;">{{ strtoupper(optional(optional($assessment)->roofMaterial)->label) }}</td>
                            <td style="border:1px solid #eee;">{{ strtoupper($property->occupancies->pluck('occupancy_type')->implode(', ')) }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered" style=" width:100%;margin-bottom:5px;">
                        <tbody>
                        <tr>
                            <th style="border:1px solid #eee; width: 20%;text-align: left" scope="col">VALUE ADDED</th>
                            <td style="border:1px solid #eee; text-align: left; padding-left: 25px !important; text-transform: uppercase;">{{ $assessment->valuesAdded->pluck('label')->implode(', ') }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:5px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #eee;" scope="col">RATE DUE<br/>{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #eee;" scope="col">ARREARS<br/>Past Year(s)</th>
                            <th style="border:1px solid #eee;" scope="col">PENALTY</th>
                            <th style="border:1px solid #eee;" scope="col">1st INSTALLMENT<br/>DUE 31-03-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #eee;" scope="col">2nd INSTALLMENT<br/>DUE 30-06-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #eee;" scope="col">3rd INSTALLMENT<br/>DUE 30-09-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #eee;" scope="col">4th INSTALLMENT<br/>DUE 31-12-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #eee;" scope="col">TOTAL DUE<br/>31-12-{{ $assessment->created_at->year }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border:1px solid #eee;">{!! number_format($assessment->getCurrentYearAssessmentAmount()) !!}</td>

                                    @if ($property->id == 12)
                                    
                                    <td>-4500</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>18,000</td>
                                    @elseif($property->id == 6968)
                                    <td>172,266</td>
                                    <td>38281</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>991,485</td>
                                    @elseif($property->id == 2445)
                                    
                                    <td>980,750	</td>
                                    <td>245,188	</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>2,735,313</td>
                                    
                                    @elseif($property->id == 2518)
                                    
                                    <td>117,000	</td>
                                    <td>29,250	</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>326,250</td>
                                    
                                    @elseif($property->id == 2497)
                                    
                                    <td>-310,139	</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>917,049</td>
                                    
                                    @elseif($property->id == 2448)
                                    
                                    <td>509,687	</td>
                                    <td>127,422</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>1,743,984</td>
                                    @elseif($property->id == 2440)
                                    
                                    <td>779,063</td>
                                    <td>140,625</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>1,785,938</td>
                                    @else
                                    <td>{!! number_format($assessment->getPastPayableDue()) !!}</td>
                                    <td style="border:1px solid #eee;">{!! number_format($assessment->getPenalty()) !!}</td>
                                    <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[1]) ?  number_format($paymentInQuarter[1]) : '-' !!}</td>
                                    <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[2]) ?  number_format($paymentInQuarter[2]) : '-' !!}</td>
                                    <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[3]) ?  number_format($paymentInQuarter[3]) : '-' !!}</td>
                                    <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[4]) ?  number_format($paymentInQuarter[4]) : '-' !!}</td>
                                    <td style="border:1px solid #eee;">{!! number_format($assessment->getCurrentYearTotalDue()) !!}</td>
                                
                            
                                    @endif
                            {{-- <td style="border:1px solid #eee;">{!! number_format($assessment->getPastPayableDue()) !!}</td>
                            <td style="border:1px solid #eee;">{!! number_format($assessment->getPenalty()) !!}</td>
                            <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[1]) ?  number_format($paymentInQuarter[1]) : '-' !!}</td>
                            <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[2]) ?  number_format($paymentInQuarter[2]) : '-' !!}</td>
                            <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[3]) ?  number_format($paymentInQuarter[3]) : '-' !!}</td>
                            <td style="border:1px solid #eee;">{!! isset($paymentInQuarter[4]) ?  number_format($paymentInQuarter[4]) : '-' !!}</td>
                            <td style="border:1px solid #eee;">{!! number_format($assessment->getCurrentYearTotalDue()) !!}</td> --}}
                        </tr>
                        </tbody>
                    </table>

                    <table class="total" style="width: 100%;">
                        <tr>
                            <td style="text-align: left;padding-right: 20px">
                                <h6 class="font-weight-bold" style="white-space: pre">PLEASE DISREGARD ARREARS IF PAID</h6>
                            </td>
                            <td style="border:1px solid #eee; width: 10%">
                                <h5 style="font-size: 14px;margin: 0; white-space: pre" align="center">ID: {{ $property->getPrintableId() }}</h5>
                            </td>
                            <td style="border:1px solid #eee;width: 10%">
                                <h5 class="red" style="font-size: 14px;margin: 0; white-space: pre" align="center">NOTICE NUMBER:</h5>
                            </td>
                            <td style="font-size: 1rem;border:1px solid #eee;width: 10%">
                                <h5 style="font-size: 14px;margin: 0; white-space: pre" align="center">{{ $property->newDigitalAddress() }}</h5>
                            </td>
                        </tr>
                    </table>

                    <div class="clearfix"></div>
                    <p style="font-size: 9px; margin-bottom: 5px; margin-top: 8px">FAILING TO MAKE PAYMENT WITHIN TWO (2) CONSECUTIVE QUARTERS WILL WARRANT ENFORCEMENT ACTIONS WITHIN COUNCILS LEGAL SCOPE AS PRESCRIBED IN THE LOCAL GOVERNMENT ACT 2004.</p>
                </div>
            </div>
        </div>
    </div>
</div>
