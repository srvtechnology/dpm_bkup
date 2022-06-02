<?php

namespace App\Http\Controllers\Admin;


use App\Models\BoundaryDelimitation;
use App\Models\LandlordDetail;
use App\Models\MetaValue;
use App\Models\OccupancyDetail;
use App\Models\PasswordResetRequest;
use App\Models\Property;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyGeoRegistry;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    public function __invoke(Request $request)
    {

        //dd(now()->subYear()->format('Y'));
        $data['total'] = Property::all()->count();
        // $data['complete'] = Property::where('is_completed',1)->where('is_draft_delivered',1)->count();
        $data['complete'] = Property::with('assessment')->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNotNull('demand_note_delivered_at');
        })->where('is_completed', 1)->count();

        $data['Incomplete_draft_delivered'] = Property::with('assessment')->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNotNull('demand_note_delivered_at');
        })->where('is_completed', 0)->count();

        $data['complete_not_delivered'] = Property::with('assessment')->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNull('demand_note_delivered_at');
        })->where('is_completed', 1)->count();

        $data['in_complete'] = Property::with('assessment')->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNull('demand_note_delivered_at');
        })->where('is_completed', 0)->count();

        $data['app_user'] = User::all()->count();

        $data['unique_property_owners'] = LandlordDetail::whereHas('property')->groupBy('mobile_1')
            ->join('properties', 'properties.id', 'landlord_details.property_id')
            ->select()
            ->addSelect(\DB::raw('COUNT(properties.id) as properties_count'))
            ->whereNotNull('mobile_1')->get()->count();

        return view('admin.dashboard', $data);
    }

    public function autoCompleteDigitaladress(Request $request)
    {
        //dd($request);

        $digital = PropertyGeoRegistry::select('digital_address', 'property_id')->with('property')->where('digital_address', 'like', '' . strtolower($request->mask) . '%')->get();
        //dd($digital);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($digital as $dg) {
            $landlord = LandlordDetail::select('first_name', 'surname')->where('property_id', $dg->property_id)->first();
            $xml .= '<option value="' . $dg->property_id . '%' . $dg->digital_address . '"><![CDATA[' . $dg->digital_address . '_' . (optional($dg->property)->organization_name ? optional($dg->property)->organization_name : ($landlord->first_name . ' ' . $landlord->surname)) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteDigitaladressOld(Request $request)
    {
        //dd($request);

        $digital = PropertyGeoRegistry::select('old_digital_address', 'property_id')->with('property')->where('old_digital_address', 'like', '' . strtolower($request->mask) . '%')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';

        foreach ($digital as $dg) {
            $landlord = LandlordDetail::select('first_name', 'surname')->where('property_id', $dg->property_id)->first();
            $xml .= '<option value="' . $dg->property_id . '%' . $dg->old_digital_address . '"><![CDATA[' . $dg->old_digital_address . '_' . (optional($dg->property)->organization_name ? optional($dg->property)->organization_name : ($landlord->first_name . ' ' . $landlord->surname)) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }


    public function autoCompleteTown(Request $request)
    {
        //dd($request);

        $town = BoundaryDelimitation::distinct('section')->select('section')->where('section', 'like', '%' . strtolower($request->mask) . '%')->pluck('section')->toArray();


        $town = array_map('trim', $town);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach (array_unique($town) as $tw) {
            $xml .= '<option value="' . trim($tw) . '"><![CDATA[' . trim($tw) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteFirstName(Request $request)
    {
        $result = LandlordDetail::select('first_name as label', 'first_name as value')->whereNotNull('first_name')->union(OccupancyDetail::select('tenant_first_name as label', 'tenant_first_name as value')->whereNotNull('tenant_first_name'))->union(MetaValue::select('value as label', 'value')->where('name', 'first_name'))->where('first_name', 'like', "%$request->mask%")->distinct()->get()->each->setVisible(['label', 'value']);
        //$result['lastNames'] = LandlordDetail::select('surname as label','surname as value')->whereNotNull('surname')->union(OccupancyDetail::select('surname as label','surname as value')->whereNotNull('surname'))->union( MetaValue::select('value as label','value')->where('name','surname') )->where('surname', 'like', "%$request->mask%")->distinct()->get()->each->setVisible(['label', 'value']);;
        //$result['streetNames'] = LandlordDetail::select('street_name as label','street_name as value')->whereNotNull('street_name')->union( MetaValue::select('value as label','value')->whereNotNull('value')->where('name','street_name') )->where('street_name', 'like', "%$request->mask%")->distinct()->get()->each->setVisible(['label', 'value']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($result as $tw) {
            $xml .= '<option value="' . trim($tw->value) . '"><![CDATA[' . trim($tw->value) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteSurname(Request $request)
    {
        $result = LandlordDetail::select('surname as label', 'surname as value')->whereNotNull('surname')->union(OccupancyDetail::select('surname as label', 'surname as value')->whereNotNull('surname'))->union(MetaValue::select('value as label', 'value')->where('name', 'surname'))->where('surname', 'like', "%$request->mask%")->distinct()->get()->each->setVisible(['label', 'value']);;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($result as $tw) {
            $xml .= '<option value="' . trim($tw->value) . '"><![CDATA[' . trim($tw->value) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteMiddleName(Request $request)
    {
        $result = LandlordDetail::select('middle_name as label', 'middle_name as value')->whereNotNull('middle_name')->where('middle_name', 'like', "%$request->mask%")->distinct()->get()->each->setVisible(['label', 'value']);;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($result as $tw) {
            $xml .= '<option value="' . trim($tw->value) . '"><![CDATA[' . trim($tw->value) . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompletePostcode(Request $request)
    {
        //dd($request);

        $postcode = Property::select('postcode')->where('postcode', 'like', '' . strtolower($request->mask) . '%')->get();
        //dd($digital);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($postcode as $pd) {
            $xml .= '<option value="' . $pd->postcode . '"><![CDATA[' . $pd->postcode . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteUserName(Request $request)
    {
        //dd($request);

        $assessmentUser = User::select('name')->where('name', 'like', '' . strtolower($request->mask) . '%')->get();
        //dd($digital);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($assessmentUser as $au) {
            $xml .= '<option value="' . $au->name . '"><![CDATA[' . $au->name . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function autoCompleteCompoundName(Request $request)
    {
        //dd($request);

        $assessmentUser = PropertyAssessmentDetail::select('compound_name')->distinct('compound_name')->where('compound_name', 'like', '%' . strtolower($request->mask) . '%')->get();
        //dd($digital);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<complete>';


        foreach ($assessmentUser as $au) {
            $xml .= '<option value="' . $au->compound_name . '"><![CDATA[' . $au->compound_name . ']]></option>';
        }

        $xml .= '</complete>';

        print_r($xml);
    }

    public function forgotRequest(Request $request)
    {
        $query = PasswordResetRequest::with('user')->orderBy('created_at', 'DESC');
        $query->whereHas('user', function ($query) use ($request) {
            return $query->where('id', '!=', 0);
        });
        /*if(!empty($request->all()))
        {
            if(!empty($request->user)) {
                $data['forgot_password_request'] = PasswordResetRequest::whereHas('user', function ($query) use ($request) {
                    return $query->where('email', $request->user);
                })->where('process', $request->process)->paginate(5);
            }else
            {
                $data['forgot_password_request'] = PasswordResetRequest::with('user')->where('process', $request->process)->paginate(5);
            }
        }*/
        if ($request->user != '') {
            $query->whereHas('user', function ($query) use ($request) {
                return $query->where('email', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->username != '') {
            $query->whereHas('user', function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->username . '%');
            });
        }


        if ($request->process != '') {
            $query->where('process', $request->process);
        }

        $data['forgot_password_request'] = $query->paginate(5);
        $data['request'] = $request;

        return view('admin.forgot-request', $data);
    }
}
