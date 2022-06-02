<div class="receipt-second">
    <div class="container">
        <div class="receipt-content">
            <div class="row">
                <div class="col-lg-12">
                    <p class="font-weight-bold" style="font-size: 12px;">THE WESTERN AREA RURAL DISTRICT COUNCIL DEMANDS
                        PAYMENT OF MUNICIPAL RATE IN
                        RESPECT OF THE PERIOD COMMENCING 1ST JANUARY TO 31ST DECEMBER {{ $assessment->created_at->year }} IN 4
                        INSTALLMENTS ON OR
                        BEFORE THE FOLLOWING DATES</p>

                    <table width="100%">
                        <tr>
                            <td align="left" style="text-align: left; font-weight:bold;" class="installment-section">
                                <ul>
                                    <li><span>FIRST INSTALLMENT</span></li>
                                    <li><span>SECOND INSTALLMENT</span></li>
                                    <li><span>THIRD INSTALLMENT</span></li>
                                    <li><span>FINAL INSTALLMENT</span></li>
                                </ul>
                            </td>
                            <td align="left" style="text-align: left; font-weight:bold;" class="installment-section">
                                <ul>
                                    <li><span>- 31-03-{{ $assessment->created_at->year }}</span></li>
                                    <li><span>- 30-06-{{ $assessment->created_at->year }}</span></li>
                                    <li><span>- 30-09-{{ $assessment->created_at->year }}</span></li>
                                    <li><span>- 31-12-{{ $assessment->created_at->year }}</span></li>
                                </ul>
                            </td>
                            <td width="60%" align="right" class="qr-code-wrapper" style="text-align: right;">
                                <img style="margin-right: 20px;" src="data:image/png;base64,{!! base64_encode(
                                        \QrCode::format('png')->size(140)->generate(
                                        "ON : ". ($property->is_organization ?  $property->organization_name :  (optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname)).
                                        ",\n DA : ". (optional($property->geoRegistry)->digital_address).
                                        ",\n RD : ". (number_format($assessment->getCurrentYearAssessmentAmount())).
                                        ",\n ARR : ". (number_format($assessment->getCurrentYearTotalDue())).
                                        ",\n PT : ". ($assessment->types->pluck('label')->implode(', ')).
                                        ",\n PD : ". ((optional(optional($assessment)->dimension)->label) . ' ' . (optional(optional($assessment)->dimension)->id == 1 ? '' : ' SQ METERS')) .
                                       ",\n MOW : ". (optional(optional($assessment)->wallMaterial)->label) .
                                        ",\n MOR : ". (optional(optional($assessment)->roofMaterial)->label) .
                                       // ",\n VA : ". ($property->valueAdded->pluck('label')->implode(', ')) .
                                        ",\n OT : ". (optional($property->occupancy)->type) .
                                        ",\n ADD : ". ($property->street_number . ', ' . $property->street_name . ', ' . $property->ward . ', ' . $property->constituency . ', '. $property->section . ', ' . $property->district . ', ' . $property->province)

                                        ))!!}">
                            </td>
                        </tr>
                    </table>

                    <p class="mb-0 special-text font-weight-bold">
                        WARNING: A SURCHARGE OF 25% WILL BE LEVIED ON THE TOTAL AMOUNT DUE AFTER EACH INSTALLMENT PERIOD
                        ELAPSES WITHOUT FULL PAYMENT FOR THAT PERIOD.
                    </p>
                    <p class="font-weight-bold">BANK ACCOUNTS FOR COLLECTION OF MUNICIPAL RATE REVENUE</p>

                    <table width="100%">
                        <tr style="vertical-align: top;">
                            <td>
                                <table class="mb-4 table-bordered table" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th style="border:1px solid lightgray;"></th>
                                        <th style="border:1px solid lightgray;">LOCATION</th>
                                        <th style="border:1px solid lightgray;">BANK</th>
                                        <th style="border:1px solid lightgray;">ACCOUNT NAME</th>
                                        <th style="border:1px solid lightgray;">ACCOUNT NUMBER</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{--<tr>
                                        <td style="border:1px solid lightgray;">1</td>
                                        <td style="border:1px solid lightgray;">SL COMMERCIAL BANK </td>
                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD.</td>
                                        <td style="border:1px solid lightgray;">003003041244112110 </td>
                                    </tr>--}}
                                    {{--                                    <tr>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">1</td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">COMMERCE & MORTGAGE BANK </td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD. SUB ACC.</td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">0021074481377</td>--}}
                                    {{--                                    </tr>--}}
                                    {{--                                    <tr>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">2</td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">UNITED BANK FOR AFRICA </td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD. ACC.  2</td>--}}
                                    {{--                                        <td style="border:1px solid lightgray;">540710030001098</td>--}}
                                    {{--                                    </tr>--}}
                                    <tr>
                                        <td style="border:1px solid lightgray;">1</td>
                                        <td class="font-weight-bold" style="border:1px solid lightgray;">LEVUMA BEACH - KENT JUNCTION</td>
                                        <td style="border:1px solid lightgray;">ZENITH BANK</td>
                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD. ACC. 2</td>
                                        <td style="border:1px solid lightgray;">6010173957  (BBAN - 012001601017395714)</td>
                                    </tr>
                                    {{--<tr>
                                        <td style="border:1px solid lightgray;">5</td>
                                        <td style="border:1px solid lightgray;">GTBANK</td>
                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD.</td>
                                        <td style="border:1px solid lightgray;">2043413675110</td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid lightgray;">6</td>
                                        <td style="border:1px solid lightgray;">KEYSTONE BANK</td>
                                        <td style="border:1px solid lightgray;">SIGMA VENTURES LTD.</td>
                                        <td style="border:1px solid lightgray;">2102000080</td>
                                    </tr>--}}
                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    </table>

                    <p class="font-weight-bold">CONTACT CENTERS</p>

                    <table class="table-bordered table" style="width:100%;">
                        <tbody>
                        <tr>
                            <td class="font-weight-bold" style="border:1px solid lightgray;">34 JONES STREET, FREETOWN</td>
                            <td style="border:1px solid lightgray;">WARDC OFFICE - FUNKIYA (BY COMMUNITY CENTER)</td>
                            <td style="border:1px solid lightgray;">WARDC OFFICE - OGOO FARM (BY REC SCHOOL)</td>
                        </tr>
                        </tbody>
                    </table>
                    <p class="mb-2 special-text font-weight-bold">PLEASE QUOTE ACCOUNT NAME AND NUMBER ABOVE ON BANK
                        SLIPS WHEN MAKING CHEQUE AND CASH PAYMENTS.</p>
                    <p class="mb-0 special-text font-weight-bold">PAYMENT IS DUE 4 WEEKS AFTER RECIEPT OF THIS NOTICE AND MUST COVER PREVIOUS/PAST INSTALLMENT DATES SHOWN ABOVE.</p>
                </div>
            </div>

            <table width="100%">
                <tr>
                    <td style="text-align: left; width: 40%">
                        <table style="width: 100%; text-align: left;">
                            <tr>
                                <td style="text-align: left;padding-left: 35px"><img src="{{ asset('images/chief_new.png') }}" alt=""
                                                                   style="width: auto; height: 60px;"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;margin-top:10%">....................................................</td>
                            </tr>
                            <tr>
                                <td style="text-align: left; font-size: 12px">CHIEF ADMINISTRATOR (WARDC)</td>
                            </tr>
                        </table>
                    </td>
                    <td style=" width: 40%;">
                        <table style="width: 100%;   ">
                            <tr>
                                <td align="center" style=" text-align: right; padding-right: 30px;"><img
                                        src="{{ asset('images/chairman.png') }}" alt="" style="width: auto; height: 60px;"></td>
                            </tr>
                            <tr>
                                <td style=" text-align: right; padding-right: 5px;">
                                    ...............................................
                                </td>
                            </tr>
                            <tr>
                                <td style=" text-align: right;padding-right: 37px;font-size: 12px">CHAIRMAN (WARDC)</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table width="100%">
                <tr>
                    <td>
                        <p style="text-align: left;font-size: 13px;">

                            <span style="display: block; margin-bottom: 4px">Enquiries: Send E-mails to <a href="mailto:mrms@sigmaventuressl.com"> mrms@sigmaventuressl.com</a></span>

                            <span style="color: red; font-weight: bold;">FOR TELEPHONE CONTACT PLEASE CALL</span>
                            <span style="font-size: 14px; font-weight: bold">+23276864861</span>
                        </p>
                    </td>
                    <td>
                        <p style="text-align: right;" class="officer-text">
                            Enumerator: {{$property->user->getName()}}</p>
                    </td>
                </tr>
            </table>

            <div class="row">
                <p class="footer-text">MUNICIPAL RATE MANAGEMENT SYSTEM - POWERED BY SIGMA VENTURES LTD. </p>
            </div>
        </div>
    </div>
</div>
