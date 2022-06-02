<?php

namespace App\Http\Controllers\APIV2\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyPayment;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;

/** All Paypal Details class **/

use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;

class PaymentController extends Controller
{


    public function show(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];

        $landlord = $request->user('landlord-api');


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
        ])->whereHas('landlord', function ($query) use ($landlord) {
            return $query->where('mobile_1', 'like', '%' . $landlord->mobile . '%');
        })->get();

        return response()->json(compact('property', 'paymentInQuarter', 'history'));
    }
}
