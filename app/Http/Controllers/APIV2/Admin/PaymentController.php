<?php

namespace App\Http\Controllers\APIV2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyPayment;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function show(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];

        $this->validate($request, [
            'property_id' => 'nullable|required_without:open_location_code',
            'open_location_code' => 'nullable|required_without:property_id',
        ]);

        if ($request->input('open_location_code')) {

            $PropertyGeoRegistry = PropertyGeoRegistry::with(['property'])->where('open_location_code', "like", $request->input('open_location_code'))->first();

            $propertyId = $PropertyGeoRegistry->property->id;
        }
        if ($request->input('property_id')) {
            $propertyId = $request->input('property_id');
        }


        $property = Property::with([
            'landlord',
            'occupancy',
            'occupancies',
            'assessment.categories',
            'assessment.types',
            'assessment.typesTotal',
            'assessment.wallMaterial',
            'assessment.roofMaterial',
            'assessment.valuesAdded',
            'assessment.dimension',
            'assessment.propertyUse',
            'assessment.zone',
            'assessment.swimming',
            'geoRegistry',
            'registryMeters',
            'payments.admin',
            'assessmentHistory'
        ])->find($propertyId);

        if ($property) {
            $paymentInQuarter = $property->getPaymentsInQuarter();
        }


        //$property['currentYearAssessmentAmount'] = $property->assessment->getCurrentYearAssessmentAmount();
        //$property['arrearDue'] = $property->assessment->getPastPayableDue();
        //$property['penalty'] = $property->assessment->getPenalty();
        //$property['amountPaid'] = $property->assessment->getCurrentYearTotalPayment();
        //$property['balance'] = $property->assessment->getCurrentYearTotalDue();


        return response()->json(compact('property', 'paymentInQuarter', 'history'));
    }

    public function store($id, Request $request)
    {
        $property = Property::with('landlord')->findOrFail($id);
        $history = [];
        $this->validate($request, [
            'amount' => 'required',
            'penalty' => 'nullable',
            'payment_type' => 'required|in:cash,cheque',
            'cheque_number' => 'nullable|required_if:payment_type,cheque|digits_between:5,10',
            'payee_name' => 'required|max:250'
        ]);

        $t_amount = intval(str_replace(',', '', $request->amount));


        $t_penalty = 0;

        $balance = number_format($property->getBalance(), 0, '.', '');


        $admin = $request->user('admin-api');

        $data = $request->only([
            'payment_type',
            'cheque_number',
            'payee_name'
        ]);

        $data['assessment'] = number_format($property->assessment->getCurrentYearTotalDue(), 0, '.', '');
        $data['admin_user_id'] = $admin->id;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        //$data['penalty'] = $t_penalty;

        $payment = $property->payments()->create($data);
        $property2 = Property::with('landlord')->findOrFail($id);
        $t_balance = number_format($property2->assessment->getCurrentYearTotalDue(), 0, '.', '');

        $payment->balance = $t_balance;

        $payment->save();

        if ($mobile_number = $property->landlord->mobile_1) {
            //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                $property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            }
        }

        $property = Property::with([
            'landlord',
            'occupancy',
            'assessment',
            'geoRegistry',
            'payments',
            'assessmentHistory'
        ])->find($id);

        $paymentInQuarter = $property->getPaymentsInQuarter();
        $url = route("payment.pos.receipt",["id"=>$property->id,"payment_id"=>$payment->id]);
        return response()->json(compact('url','property', 'paymentInQuarter', 'history'));
    }
}
