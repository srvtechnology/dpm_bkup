<?php

namespace App\Http\Controllers\APIV2\General;

use Folklore\Image\Facades\Image;
use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\Swimming;
use App\Models\User;
use App\Notifications\DraftDeliveredSMSNotification;
use App\Notifications\PaymentSMSNotification;
use App\Types\ApiStatusCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Property;
use Illuminate\Validation\Rule;

class PropertyController extends ApiController
{
    public function save(Request $request)
    {
        $assessment_images = new PropertyAssessmentDetail();
        \Illuminate\Support\Facades\Log::debug($request->all());
        if ($request->input('property_id') && $property = Property::find($request->property_id)) {
            $assessment_images = $property->assessment()->first();
        }

        /* @var User */
        $user = $request->user();

        // $validator = $this->validator($request, $assessment_images);

        // if ($validator->fails()) {
        //     return $this->error(ApiStatusCode::VALIDATION_ERROR, [
        //         'errors' => $validator->errors()
        //     ]);
        // }

        \DB::beginTransaction();

        $rate = $this->calculateRate($request);

        /* @var $property Property */
        $property = $user->properties()->firstOrNew(['id' => $request->property_id]);

        $property->fill([
            'street_number' => $request->property_street_number,
            'street_name' => $request->property_street_name,
            'ward' => $request->property_ward,
            'constituency' => $request->property_constituency,
            'section' => $request->property_section,
            'chiefdom' => $request->property_chiefdom,
            'district' => $request->property_district,
            'province' => $request->property_province,
            'postcode' => $request->property_postcode,
            'organization_addresss' => $request->organization_address ? $request->organization_address : null,
            'organization_tin' => $request->organization_tin ? $request->organization_tin : null,
            'organization_type' => $request->organization_type ? $request->organization_type : null,
            'organization_name' => $request->organization_name ? $request->organization_name : null,
            'is_organization' => $request->input('is_organization', false),
            'is_completed' => $request->input('is_completed', false),
            'is_property_inaccessible' => $request->input('is_property_inaccessible', false),
            'is_draft_delivered' => $request->input('is_draft_delivered', false),
            'delivered_name' => $request->input('delivered_name'),
            'delivered_number' => $request->input('delivered_number')
        ]);

        $recipient_photo = null;

        if ($request->hasFile('delivered_image')) {
            $recipient_photo = $request->delivered_image->store(Property::DELIVERED_IMAGE);
            $property->delivered_image = $recipient_photo;
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessible);

        $landlord = $property->landlord()->firstOrNew([]);

        /* landlord image */
        $landlord_image = $landlord->image;

        if ($request->hasFile('landlord_image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $landlord_image = $request->landlord_image->store(Property::ASSESSMENT_IMAGE);
        }

        /* Save/Update landlord details*/
        $landlord->fill([
            'first_name' => $request->landlord_first_name,
            'middle_name' => $request->landlord_middle_name,
            'surname' => $request->landlord_surname,
            'sex' => $request->landlord_sex,
            'street_number' => $request->landlord_street_number,
            'street_name' => $request->landlord_street_name,
            'email' => $request->landlord_email,
            'image' => $landlord_image,
            'id_number' => $request->landlord_id_number,
            'id_type' => $request->landlord_id_type,
            'tin' => $request->landlord_tin,
            'ward' => $request->landlord_ward,
            'constituency' => $request->landlord_constituency,
            'section' => $request->landlord_section,
            'chiefdom' => $request->landlord_chiefdom,
            'district' => $request->landlord_district,
            'province' => $request->landlord_province,
            'postcode' => $request->landlord_postcode,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_2' => $request->landlord_mobile_2,
        ]);

        $landlord->save();

        /* Save/Update occupancy details*/

        $occupancy = $property->occupancy()->firstOrNew([]);

        $occupancy->fill([
            'type' => $request->occupancy_type,
            'tenant_first_name' => $request->occupancy_tenant_first_name,
            'middle_name' => $request->occupancy_middle_name,
            'surname' => $request->occupancy_surname,
            'mobile_1' => $request->occupancy_mobile_1,
            'mobile_2' => $request->occupancy_mobile_2
        ]);

        $occupancy->save();

        if ($request->occupancy_type && count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        /* @var $assessment PropertyAssessmentDetail */

        /* Save/Update assessment details*/
        if ($property->assessment()->exists()) {

            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrNew([]);
        }

        $assessment_data = [
            'property_wall_materials' => $request->assessment_wall_materials_id,
            'roofs_materials' => $request->assessment_roofs_materials_id,
            'property_dimension' => $request->assessment_dimension_id,
            'property_rate_without_gst' => $rate['rateWithoutGST'],
            'property_gst' => $rate['GST'],
            'property_rate_with_gst' => $rate['rateWithGST'],
            'property_use' => $request->assessment_use_id,
            'zone' => $request->assessment_zone_id,
            'no_of_mast' => $request->total_mast,
            'no_of_shop' => $request->total_shops,
            'no_of_compound_house' => $request->total_compound_house,
            'compound_name' => $request->compound_name,
            'gated_community' => $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null
        ];

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment_images->hasImageOne()) {
                unlink($assessment_images->getImageOne());
            }
            $assessment_data['assessment_images_1'] = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment_images->hasImageTwo()) {
                unlink($assessment_images->getImageTwo());
            }
            $assessment_data['assessment_images_2'] = $request->assessment_images_2->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->input('is_draft_delivered')) {

            if (!$assessment->demand_note_delivered_at) {

                if ($mobile_number = $property->landlord->mobile_1) {
                    //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
                    $name = $request->input('delivered_name');
                    $year = now()->format('Y');
                    if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                        $property->landlord->notify(new DraftDeliveredSMSNotification($property, $mobile_number, $name, $year));
                    }
                }
            }
            $assessment_data['demand_note_delivered_at'] = now();
            $assessment_data['demand_note_recipient_name'] = $request->input('delivered_name');
            $assessment_data['demand_note_recipient_mobile'] = $request->input('delivered_number');
            $assessment_data['demand_note_recipient_photo'] = $recipient_photo;
        }

        $assessment->fill($assessment_data);

        if ($request->input('swimming_pool')) {
            $assessment->swimming()->associate($request->input('swimming_pool'));
        }

        $assessment->save();

        $categories = getSyncArray($request->input('assessment_categories_id'), ['property_id' => $property->id]);
        $assessment->categories()->sync($categories);

        /* property type (Habitat) multiple value */
        $types = getSyncArray($request->input('assessment_types'), ['property_id' => $property->id]);
        $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        if ($request->input('assessment_types_total')) {
            $typesTotal = getSyncArray($request->input('assessment_types_total'), ['property_id' => $property->id]);
            $assessment->typesTotal()->sync($typesTotal);
        }


        /* property value added multiple value */
        $valuesAdded = getSyncArray($request->input('assessment_value_added_id'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);

        /* Geo Registry Data  */

        $geoData = [
            'point1' => $request->registry_point1,
            'point2' => $request->registry_point2,
            'point3' => $request->registry_point3,
            'point4' => $request->registry_point4,
            'point5' => $request->registry_point5,
            'point6' => $request->registry_point6,
            'point7' => $request->registry_point7,
            'point8' => $request->registry_point8,
            'digital_address' => $request->registry_digital_address,
            'dor_lat_long' => str_replace(',', ', ', $request->dor_lat_long),
        ];

        if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
            list($lat, $lng) = explode(',', $request->dor_lat_long);
            $geoData['open_location_code'] = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
        }

        !$geoData['digital_address'] || $geoData = $this->addIdToDigitalAddress($geoData, $property);

        $geoRegistry = $property->geoRegistry()->firstOrNew([]);

        $geoRegistry->fill($geoData);
        $geoRegistry->save();

        /* save and update Registry Image */
        $registryImageId = [];
        $allregistryImage = $property->registryMeters()->pluck('id')->toArray();
        if ($request->registry && count($request->registry) and is_array($request->registry)) {
            foreach (array_filter($request->registry) as $key => $registry) {
                $image = null;
                $registryImageId[] = isset($registry['id']) ? (int) $registry['id'] : '';
                if ($request->hasFile('registry.' . $key . '.meter_image')) {
                    $registryMeters = $property->registryMeters()->where('id', isset($registry['id']) ? (int) $registry['id'] : '')->first();
                    if ($registryMeters && $registryMeters->image != null) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        // $registryMeters->delete();
                    }
                    $image = $registry['meter_image']->store(Property::METER_IMAGE);
                    $property->registryMeters()
                        ->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number'], 'image' => $image]);
                } else {
                    $property->registryMeters()->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number']]);
                }
            }
        }

        /* delete registry image which not updated*/

        $removeImageId = array_diff($allregistryImage, $registryImageId);
        if (count($removeImageId)) {
            foreach ($removeImageId as $diffId) {
                $registryMetersDelete = $property->registryMeters()->where('id', $diffId)->first();
                if ($registryMetersDelete && $registryMetersDelete->image != null) {
                    if ($registryMetersDelete->hasImage()) {
                        unlink($registryMetersDelete->getImage());
                    }

                    //$registryMetersDelete->delete();
                }
                $registryMetersDelete->delete();
            }
        }

        \DB::commit();

        $getProperty = $property->with('landlord', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'occupancies', 'categories', 'propertyInaccessible')->where('id', $property->id)->get();
        return $this->success([
            'property_id' => $property->id,
            'sink' => 1,
            'is_completed' => $property->is_completed,
            'property' => $getProperty,
        ]);
    }

    protected function addIdToDigitalAddress($geoData, $property)
    {
        $digitalAddress = $property->geoRegistry()->first();

        if (!$digitalAddress) {
            $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;

            return $geoData;
        }
        //
        //        $addresses = explode('-', $digitalAddress->digital_address);
        //
        //        $last = count($addresses) > 1 ? intval(array_last($addresses)) : array_last($addresses);
        //
        //        if($last != $property->id)
        //        {
        //            $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;
        //
        //            return $geoData;
        //        }
        //
        //        $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;

        return $geoData;
    }

    public function validator($request, $assessment_images)
    {
        $registryimage = new RegistryMeter();

        if ($request->input('property_id') && $property = Property::find($request->property_id)) {
            $registryimage = $property->registryMeters()->first();
        }

        $validationRequriedIf = $registryimage && $registryimage->image != null ? '' : 'required_if:is_completed,1';

        if (isset($request->is_completed) && $request->is_completed == 1) {
            $organizationYes = 'required_if:is_organization,1';
            $organizationNo = 'required_if:is_organization,0';
            $registryField = 'required';
        } else {
            $organizationYes = '';
            $organizationNo = '';
            $registryField = 'nullable';
        }

        return Validator::make($request->all(), [
            'is_completed' => 'nullable|boolean',
            'is_organization' => 'required|boolean',
            'organization_name' => '' . $organizationYes . '|string|max:255',
            'organization_type' => '' . $organizationYes . '|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_address' => '' . $organizationYes . '|string|max:255',
            'property_street_number' => 'required_if:is_completed,1|string',
            'property_street_name' => 'required_if:is_completed,1|string|max:255|nullable',
            'property_ward' => 'required_if:is_completed,1|integer',
            'property_constituency' => 'required_if:is_completed,1|integer',
            'property_section' => 'required_if:is_completed,1|string|max:255',
            'property_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'property_district' => 'required_if:is_completed,1|string|max:255',
            'property_province' => 'required_if:is_completed,1|string|max:255',
            'property_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_first_name' => '' . $organizationNo . '|string|max:255',
            'landlord_middle_name' => 'nullable|string|max:255',
            'landlord_surname' => '' . $organizationNo . '|string|max:255',
            'landlord_sex' => '' . $organizationNo . '|string|max:255',
            'landlord_street_number' => 'string',
            'landlord_street_name' => 'string|max:255',
            'landlord_email' => "nullable|email",
            'landlord_tin' => 'nullable|string|max:255',
            'landlord_id_type' => 'nullable|string|max:255',
            'landlord_id_number' => 'nullable|string|max:255',
            'landlord_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240‬',
            'landlord_ward' => 'required_if:is_completed,1|integer',
            'landlord_constituency' => 'required_if:is_completed,1|integer',
            'landlord_section' => 'required_if:is_completed,1|string|max:255',
            'landlord_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'landlord_district' => 'required_if:is_completed,1|string|max:255',
            'landlord_province' => 'required_if:is_completed,1|string|max:255',
            'landlord_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_mobile_1' => 'required_if:is_completed,1|string|max:15',
            'landlord_mobile_2' => 'nullable|string|max:15',
            'occupancy_type' => 'nullable|required_if:is_completed,1|array',
            'occupancy_type.*' => 'nullable|required_if:is_completed,1|in:Owned Tenancy,Rented House,Unoccupied House',
            'occupancy_tenant_first_name' => 'nullable|string|max:255',
            'occupancy_middle_name' => 'nullable|string|max:255',
            'occupancy_surname' => 'nullable|string',
            'occupancy_mobile_1' => 'nullable|string|max:15',
            'occupancy_mobile_2' => 'nullable|string|max:15',
            'assessment_categories_id' => 'nullable|required_if:is_completed,1|array',
            'assessment_categories_id.*' => 'nullable|required_if:is_completed,1|exists:property_categories,id',
            'assessment_images_1' => '' . ($assessment_images->assessment_images_1 == null ? 'required_if:is_completed,1|' : '') . 'image|mimes:jpeg,png,jpg',
            'assessment_images_2' => '' . ($assessment_images->assessment_images_2 == null ? 'required_if:is_completed,1|' : '') . 'image|mimes:jpeg,png,jpg',
            'assessment_types' => 'required_if:is_completed,1|array|max:2',
            'assessment_types.*' => 'required_if:is_completed,1|exists:property_types,id',
            "assessment_types_total" => 'nullable|array|max:2',
            "assessment_types_total.*" => 'nullable|exists:property_types,id',
            'assessment_wall_materials_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_roofs_materials_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_dimension_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_value_added_id' => 'required_if:is_completed,1|array',
            'assessment_value_added_id.*' => 'required_if:is_completed,1|exists:property_value_added,id',
            'assessment_use_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_zone_id' => 'required_if:is_completed,1|string|max:255',
            'compound_name' => 'nullable|string|max:255',
            'total_compound_house' => 'nullable|string|max:255',
            'total_shops' => 'nullable|string|max:255',
            'total_mast' => 'nullable|string|max:255',
            'registry' => 'array',
            'registry.*.meter_image' => [
                'image',
                'mimes:jpg,jpeg,png', 'max:10240‬'
            ],
            'registry.*.meter_number' => 'nullable|string|max:255',
            'registry_point1' => 'required_if:is_completed,1|string|max:255',
            'registry_point2' => 'required_if:is_completed,1|string|max:255',
            'registry_point3' => 'required_if:is_completed,1|string|max:255',
            'registry_point4' => 'nullable|string|max:255',
            'registry_point5' => 'nullable|string|max:255',
            'registry_point6' => 'nullable|string|max:255',
            'registry_point7' => 'nullable|string|max:255',
            'registry_point8' => 'nullable|string|max:255',
            'registry_digital_address' => [
                'required_if:is_completed,1',
                'string',
                'max:159'
            ],
            'dor_lat_long' => 'nullable|required_if:is_completed,1|max:190',
            'gated_community' => 'nullable|required_if:is_completed,1|boolean',
            'swimming_pool' => 'nullable|exists:swimmings,id',
            'is_property_inaccessible' => 'required|boolean',
            'property_inaccessible' => 'nullable|required_if:is_property_inaccessible,1|array',
            'property_inaccessible.*' => 'nullable|required_if:is_property_inaccessible,1|exists:property_inaccessibles,id',
            'is_draft_delivered' => 'nullable|boolean',
            'delivered_name' => 'nullable|max:70',
            'delivered_number' => 'nullable|string|max:55',
            'delivered_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240‬'
        ]);
    }

    public function getIncompleteProperty(Request $request)
    {
        $property = $request->user()->properties()
            ->with('images', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'payments', 'landlord', 'assessment.typesTotal:id,label,value', 'assessment.types:id,label,value', 'assessment.valuesAdded:id,label,value', 'occupancies:id,occupancy_type,property_id', 'assessment.categories:id,label,value', 'propertyInaccessible:id,label')
            ->where('is_draft_delivered', 0)
            ->orderBy('id', 'desc')
            ->get();

        return $this->success([
            'property' => $property,
        ]);
    }

    public function calculateRate($request)
    {
        $property_category = 0;
        $wall_material = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast = $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];

        if (isset($request->assessment_value_added_id) && is_array($request->assessment_value_added_id)) {
            foreach ($valueAdded as $value) {
                if (in_array($value, $request->assessment_value_added_id)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    if ($value == 9) {
                        $shopValue = $amount->value;
                    }
                    if ($value == 8) {
                        $mastValue = $amount->value;
                    }
                }
            }
            $valueAdded = array_diff($request->assessment_value_added_id, $valueAdded);
        }

        if (isset($request->assessment_categories_id) and $request->assessment_categories_id != null)
            $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if (isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if (isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if (is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->assessment_types) and count($request->assessment_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->assessment_types)->sum('value');

        if (isset($request->assessment_dimension_id) and $request->assessment_dimension_id != null)
            $property_dimension = PropertyDimension::select('value')->find($request->assessment_dimension_id);

        if (isset($request->assessment_use_id) and $request->assessment_use_id != null)
            $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if (isset($request->assessment_zone_id) and $request->assessment_zone_id != null)
            $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);

        $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        $step2 = $property_type_val;
        $step3 = $property_dimension['value'];
        $step4 = $property_use['value'];
        $step5 = $zones['value'];
        $step6 = 0;
        $swimming_pool = optional(Swimming::find($request->swimming_pool))->value;

        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }

        $result['rateWithoutGST'] = @(((($step1 * $step2 * $step3 * $step4) * $gated_community) + ($swimming_pool ? $swimming_pool : 0)) / ($step6 > 0 ? $step6 : 1));

        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        return $result;
    }
    public function saveImage(Request $request)
    {

        if ($request->hasFile('assessment_images_1')) {

            $assessment_data = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        return url(Image::url($assessment_data, 200, 200, ['crop']));
    }
}
