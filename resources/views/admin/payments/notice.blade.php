<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Receipt</title>

    <style type="text/css"  media="all">

        body img {
            max-width: 100%;
        }

        body h6 {
            font-size: 1.2em;
        }

        h2{
            margin: 8px;
        }

        body h4 {
            font-size: 1.3em;
        }

        body .table td,
        body .table th {
            text-align: center;
            vertical-align: top;
            padding: .15rem !important;
            font-size: 0.75rem;
        }

        body .table td {
            height: 20px;
        }

        body .table th {
            text-transform: uppercase;
        }

        body{
            font-size: 15px;
            font-family: Calibri;
            line-height: 0.9;
        }


        .footer-text {
            width: 550px;
            margin: auto;
            color: gray;
            font-weight: bold;
            margin-top: 15px;
        }


        .page {
            width: 1000px;
            min-height: 300mm;
            padding: 1mm;
            border-radius: 5px;

            font-size: 18px;
            color: #333333;

        }

        .special-text {
            font-size: 0.85rem;
        }

        .h-warning {
            font-size: 0.85rem !important;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            size: A4;
            margin: 10px;
        }

        .print_row {
            float: right;
            position: absolute;
            top: 115px;
            right: 100px;
            padding: 18px;
            border: 1px solid #ccc;
            border-radius: 0px;
            background: #eee;
            font-size: 24px;
            font-weight: 600;
            /* box-shadow: 0px 2px 2px #ccc; */
        }

        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;

            }
        }

        .receipt-description p {
            font-weight: 400 !important;
        }

    </style>
</head>
<body>



    @foreach($properties as $key => $property)
        <div class="page">
        <div style="width: 94%;border: 1px solid;padding: 20px; margin: 10px;  ">
            <div>
                <div style="width: 17%; float: left; padding-left: 0px; height: 70px;" >
                    <img src="{{ public_path('images/logo1.png') }}" style="height: 80px;" alt="">
                </div>

                <div style="width: 70%;  float: left; height: 80px;" >
                    <div style="text-align: center; margin-bottom: 15px;" >
                        <h2 style="margin-bottom: 10px; font-size: 20px;">WESTERN AREA RURAL DISTRICT COUNCIL (WARDC)</h2>
                        <h3 style="margin-bottom: 10px; font-size: 18px; margin-top: 0;">OLD YORK ROAD WATERLOO</h3>
                        <h4 style="margin-bottom: 10px; font-size: 16px; margin-top: 0;">SIERRA LEONE</h4>
                    </div>
                </div>

                <div style="float: right;text-align: right;padding-right: 0px; height: 70px;">
                    <img src="{{ public_path('images/logo2.jpg') }}" style="height: 80px;">
                </div>

            </div>

            <div style="clear: both; border-bottom: 1px solid #eee; padding-top: 10px;">&nbsp;</div>

            <div style="display: block; width: 100%;">
                <div style="width: 30%; float: left">
                    <h4 style="font-size: 12px;margin-bottom: 0;">PROPERTY {{ $property->is_organization ? 'ORGANIZATION' : 'OWNER' }} NAME:</h4>
                </div>
                <div style="width: 70%; float: left;">
                    <h4 style="font-size: 12px;margin-bottom: 0;border: 1px solid;padding: 3px;border-color: #999;">{{ $property->is_organization ?  $property->organization_name : $property->landlord->getName() }}&nbsp;</h4>
                </div>
            </div>

            <div style="clear: both; border-bottom: 1px solid #eee; padding-top: 10px;">&nbsp;</div>

            <div style="display: block; width: 100%;">
                <div style="width: 30%; float: left">
                    <h4 style="font-size: 12px;margin-bottom: 0;">PROPERTY ADDRESS:</h4>
                </div>
                <div style="width: 70%; float: left;">
                    <h4 style="font-size: 12px;margin-bottom: 0;border: 1px solid;padding: 3px; border-color: #999;">{{ $property->getOnlyAddress() }}&nbsp;</h4>
                </div>
            </div>

            <div style="clear: both; border-bottom: 1px solid #eee; padding-top: 10px;">&nbsp;</div>

            <div style="display: block; width: 100%;">
                <div style="width: 30%; float: left">
                    &nbsp;
                </div>
                    <div style="width: 30%; float: left">
                        <h4 style="font-size: 12px;margin-bottom: 0;"> PROPERTY DIGITAL ADDRESS:</h4>
                    </div>
                    <div style="width: 40%; float: left;">
                        <h4 style="font-size: 12px;margin-bottom: 0;border: 1px solid;padding: 3px; border-color: #999;">{{ $property->geoRegistry->digital_address }}&nbsp;</h4>
                    </div>

            </div>

            <div style="clear: both; border-bottom: 1px solid #eee; padding-top: 10px;">&nbsp;</div>

            <div>

                <p>Dear Sir/Madam,</p>
                <p><strong style="border-bottom: 1px solid;">WARNING NOTICE: NON-PAYMENT OF 2019 PROPERTY TAX OWED TO WESTERN AREA RURAL DISTRICT COUNCIL (WARDC) </strong></p>
                <p>Our records show the property rates payable to Western Area Rural District Council (WARDC) Pursuant to section 69(2) of the Local Government Act 2004 (the Act) has not been paid after the initial 2-Weeks payment period (for at least the previous quarters) has elapsed. </p>
                <p>The Council relies primarily on its property rate revenue to fund the provision of essential services and failure to pay these rates severely impacts on the Council’s ability to provide such services, </p>
                <p>Section 79(3) and (4) of the Act make clear that the Council is entitled to proceed directly against the occupier, and the occupier and the owner must then resolve any dispute as to which of is liable for property rates between themselves. </p>

                <p><strong style="border-bottom: 1px solid;">EXCERPT OF COUNCIL’S POWERS</strong></p>
                <p><strong style="border-bottom: 1px solid;">Step 1: WARDC Rights against Owners</strong></p>
                <p><strong>Section 78</strong> of the Act provides for the Council is entitled to empower its bailiff by a warrant to seize the moveable property of the owner, and sell that property by public auction to the highest bidder (within 20 days of seizure). The money raised is applied to the outstanding rates and the expenses of the sale, and any surplus returned to the owner.</p>

                <p><strong style="border-bottom: 1px solid;">Step 2: WARDC Rights against Occupiers</strong></p>
                <p><strong>Section 79</strong> of the Act Provides that if the bailiff finds no moveable property or if the amount realized from the sale of owners moveable properties is insufficient, the Council shall issue another warrant requiring its bailiff to demand payment of the amount stated in the warrant from the occupier of the building; and if payment is not made by the occupier within 20 days of the demand, then the bailiff can seize the moveable property of the occupier. Again, such property is sold at public auction and money raised applied to outstanding rates and the cost of the sale.</p>

                <p><strong style="border-bottom: 1px solid;">Step 3: WARDC’S Ultimate Rights against Owners </strong></p>
                <p><strong>Section 80</strong> of the Act Provides that if the money raised via step 1 and step 2 is not sufficient to discharge the debt, and it has been outstanding for 6 months or more (this condition having already been fulfilled), Council is entitled to apply to Court for an order for sale of the building.</p>

                <p><strong>NOTE:</strong>  This letter is a WARNING NOTICE to both owners and occupiers that unless the outstanding property tax arrears are paid in full, or a payment plan agreed with Council within 14 days of receipt of this notice, Council intends to pursue its rights to the full extent of the law.  </p>

                <p>To avoid the actions set out above, please ensure your Property Rate is paid. </p>
                <p>Yours Sincerely,</p>
                <img style="width: 120px;border-bottom: 1px dotted;" src="{{ public_path('pdf-sign.jpg') }}">
                <p style="margin-bottom: 2px;"><strong>Chief Administrator</strong></p>
                <p style="margin-top: 0;"><strong>Western Area Rural District Council - WARDC</strong></p>
            </div>

            <div></div>
        </div>
        <p style="text-align: center; margin-top: 0;">CONTACTS – Telephone: +23276864861 | E-Mail: <a href="mailto:wardcmrc@sigmaventuressl.com">wardcmrc@sigmaventuressl.com</a> </p>

        </div>
    @endforeach

</body>
</html>
