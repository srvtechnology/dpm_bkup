<?php

namespace App\Http\Controllers\APIV2\Landlord;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\APIV2\ApiController;
use App\Models\UserVerification;
use App\Notifications\MobileNumberVerification;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Twilio;
use App\Logic\SystemConfig;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        /* Validate Request */
        $request->validate([
            'mobile_number' => 'required',

        ]);


        $mobile_number = $request->input('mobile_number');
        /* Gererate otp from helper*/
        $code = generateOtp();


        //UserVerification::where(['mobile' => $mobile_number])->delete();

        $user = UserVerification::updateOrCreate(['mobile' => $mobile_number], [
            'code' => $code,
            'expired_at' => now()->addMinutes(15),
        ]);

        Passport::personalAccessTokensExpireIn(now()->addHours(1));

        $tokenResult = $user->createToken('Person Access Token');
        /* Otp Send On User's  Mobile */

        //$user->notify(new MobileNumberVerification($code));
        // \Aloha\Twilio\Twilio::message($user->mobile, "Your verification code is:{$code}");
        //$twilio = new Twilio("AC175da4ba4c1bf58ee9fa3b49201af0d2", "6707244e68f108e2351f8bba8cf52121", "+19196263510");
        //$twilio->message($user->mobile, "Your verification code is:{$code}");
        $this->smssend($user->mobile, "{$code} is Your Sigma Pay verification code");
        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::COMMUNITY_GROUP);

        $currency = ["usd" => "1", "le" => ($optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE})];
        $currencyPound = ["pound" => "1", "le" => ($optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE_POUND})];

        return $this->success([
            'token' => $tokenResult->accessToken,
            'auth_type' => "Bearer",
            'expire_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'currency_rate' => $currency,
            'currency_rate_pound' => $currencyPound,
            'online_charge' => $optionGroup->{\App\Logic\SystemConfig::ONLINE_CHARGE},
            'user' => $user->toArray(),

        ]);
    }

    public function smssend($to, $message)
    {
        $id = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/Messages.json";
        $from = "SIGMA-PAY";
        //$from = env('TWILIO_FROM');
        $to = $to; // twilio trial verified number
        $body = $message;
        $data = array(
            'From' => $from,
            'To' => $to,
            'Body' => $body,
        );
        $post = http_build_query($data);
        $x = curl_init($url);
        curl_setopt($x, CURLOPT_POST, true);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
        curl_setopt($x, CURLOPT_POSTFIELDS, $post);
        $y = curl_exec($x);
        curl_close($x);
        // var_dump($post);
        // var_dump($y);

    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $user = $request->user('landlord-api');
        $code = $request->code;


        $isValidVerification = UserVerification::where([
            'mobile' => $user->mobile,
            'code' => $code,

        ])
            ->where('expired_at', '>', now()->subMinutes(15)->format('Y-m-d H:i:s'))
            ->exists();

        if ($isValidVerification) {
            return $this->success(["message" => 'Mobile number verification success.']);
        } else {
            return $this->error(4003, ["message" => 'OTP may expired or invalidated.']);
        }
    }
}
