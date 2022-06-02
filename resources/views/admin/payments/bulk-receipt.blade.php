<!doctype html>
<html lang="en">
<head>
    @include('admin.payments.print-head')
</head>
<body>

<div class="page" style="width:100%;">
    @foreach($properties as $property)

        @php $property->assessment->setPrinted() @endphp

        @include('admin.payments.receipt-content', ['property' => $property, 'assessment' => $property->assessment, 'paymentInQuarter' => $property->getPaymentsInQuarter($year), 'year' => $year])

        @include('admin.payments.receipt-account-details', ['property' => $property,  'assessment' => $property->assessment, 'paymentInQuarter' => $property->getPaymentsInQuarter($year), 'year' => $year])

        <div class="page-break"></div>
        @include('admin.payments.receipt-policy')

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>

</body>
</html>
