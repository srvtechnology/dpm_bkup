<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Notification;
use App\Notifications\BroadcastNotification;
use App\Notifications\PaymentSMSNotification;
use OpenLocationCode\OpenLocationCode;

Route::get('/', function () {
	 //\DB::statement('');
	//  dd('done');
  return redirect('back-admin/dashboard');

// \Mail::raw('Text to e-mail', function ($message) {
//     $message->to('kingshuk.mat@gmail.com')->subject('Your Test!');
// });

});

Route::get('/image-list', function () {

$propertiesDtls = DB::table('properties')
            ->join('property_assessment_details', 'properties.id', '=', 'property_assessment_details.property_id')
            ->select('property_assessment_details.property_id', 'property_assessment_details.assessment_images_2','property_assessment_details.assessment_images_1','property_assessment_details.demand_note_recipient_photo')
           // ->limit(10)
            ->get();
//dd($propertiesDtls);

   echo'<table>';
   $i = 1;
   foreach ($propertiesDtls as $dtls) {
    if((($dtls->assessment_images_2) && !(file_exists(storage_path().'/app/'.$dtls->assessment_images_2))) || (($dtls->assessment_images_1) && !(file_exists(storage_path().'/app/'.$dtls->assessment_images_1))) || (($dtls->demand_note_recipient_photo) && !(file_exists(storage_path().'/app/'.$dtls->demand_note_recipient_photo))) ){

        echo '<tr>';
        echo '<td>'.$i++.'</td>';
        echo '<td>'.$dtls->property_id.'</td>';
        echo '<td>'.((($dtls->assessment_images_2) && (file_exists(storage_path().'/app/'.$dtls->assessment_images_2)))? '': $dtls->assessment_images_2) .'</td>';

        echo '<td>'.((($dtls->assessment_images_1) && (file_exists(storage_path().'/app/'.$dtls->assessment_images_1)))? '': $dtls->assessment_images_1) .'</td>';

        echo '<td>'.((($dtls->demand_note_recipient_photo) && (file_exists(storage_path().'/app/'.$dtls->demand_note_recipient_photo)))? '': $dtls->demand_note_recipient_photo) .'</td>';
        //echo '<td>'. $dtls->assessment_images_2 .'</td>';
        //echo '<td>'.if (file_exists( ) { $dtls->property_id }.'</td>';
        echo '</tr>';
    }
   }    
   echo'</table>';
});


Route::get('/test', function () {
    return \App\Models\Property::withAssessmentCalculation(2019)->having('total_payable_due', 0)->orderBy('total_payable_due')->get();
});

Route::get('storage/{filename}', function ($filename) {

    return Image::make(storage_path('app/' . $filename))->response();
});

Route::get('paypal', 'PaymentController@index');
Route::post('paypal', 'PaymentController@payWithpaypal');


// route for check status of the payment
Route::get('status', 'PaymentController@getPaymentStatus')->name('status');
Route::get('cancel', 'PaymentController@cancel')->name('cancel');

Route::get('/sms-test', function() {

   /* @var $property \App\Models\Property */

   $property = \App\Models\Property::find(16615);

   //$property->landlord->notify(new PaymentSMSNotification($property, $property->landlord->mobile_1, $property->payments()->first()));
  //$property->landlord->notify(new PaymentSMSNotification($property, $property->landlord->mobile_1, \App\Models\Payment::first()));
   //$property->landlord->notify(new PaymentSMSNotification($property, '7003520826', $property->payments()->first()));

//dd(config('services.mrms.connections.twilio'));
  // Twilio::from(config('services.mrms.connections.twilio'))->message('+917003520826', 'Hello world!!');
   //Twilio::message('+917003520826', 'Hello world!!');

// Twilio::message(
//                 '+91...',
//                 [
//                     "body" => 'test sms 4',
//                     "from" => config('services.twilio.alphanumeric_sender')
//                     //   On US phone numbers, you could send an image as well!
//                     //  'mediaUrl' => $imageUrl
//                 ]
//             );   

   //Notification::send($property->landlord, new BroadcastNotification($property, 'test sms'));
   //$property->landlord->notify(new BroadcastNotification($property, 'hiiii'));
   echo 'sms-test done';
});

//
//Route::get('property/list', 'admin\PropertyController@list');
//Route::get('property/details', 'admin\PropertyController@view');

Route::get('/config-clear', function() {
    $status = Artisan::call('config:clear');
    return '<h1>Configurations cleared</h1>';
});

//Clear cache:
Route::get('/cache-clear', function() {
    $status = Artisan::call('cache:clear');
    return '<h1>Cache cleared</h1>';
});

//Clear configuration cache:
Route::get('/config-cache', function() {
    $status = Artisan::call('config:cache');
    return '<h1>Configurations cache cleared</h1>';
});


//Clear route cache:
Route::get('/route-cache', function() {
    $status = Artisan::call('route:clear');
    return '<h1>route cache cleared</h1>';
});


//Clear view cache:
Route::get('/view-cache', function() {
    $status = Artisan::call('view:clear');
    return '<h1>view cache cleared</h1>';
});


//Clear event cache:
Route::get('/event-cache', function() {
    $status = Artisan::call('event:clear');
    return '<h1>event cache cleared</h1>';
});