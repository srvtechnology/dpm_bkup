<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PropertyExport;
use App\Grids\PropertiesGrid;
use App\Http\Controllers\Controller;
use App\Jobs\PropertyInBulk;
use App\Jobs\PropertyNotice;
use App\Jobs\PropertyStickers;
use App\Logic\SystemConfig;
use App\Models\BoundaryDelimitation;
use App\Models\Property;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\Swimming;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    private $properties;

    public function list(PropertiesGrid $usersGrid, Request $request)
    {
        $organizationTypes = collect(json_decode(file_get_contents(storage_path('data/organizationTypes.json')), true))->pluck('label', 'value');

        $this->properties = Property::with([
            'user',
            'landlord',
            'assessment' => function ($query) use ($request) {
                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                }
            },
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments'
        ])
            ->whereHas('assessment', function ($query) use ($request) {

                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                }

                if ($request->filled('is_printed')) {

                    if ($request->input('is_printed') === '1') {
                        $query->whereNotNull('last_printed_at');
                    }

                    if ($request->input('is_printed') === '0') {
                        $query->whereNull('last_printed_at');
                    }
                }
            });

        if ($request->start_date && $request->end_date) {
            $this->properties->whereBetween('properties.created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            !$request->start_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::parse($request->start_date), Carbon::now()]);
            !$request->end_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::now()->subYear(5), Carbon::parse($request->end_date)->endOfDay()]);
        }

        if ($request->unpaid_start_date && $request->unpaid_end_date) {

            $year = date('Y', strtotime($request->unpaid_start_date));
            //$this->properties->whereYear('properties.created_at', $year);

            $this->properties->doesntHave('payments');
        }

        if ($payment_status = $request->input('paid')) {
            if ($payment_status == 'paid') {
                $this->properties->whereHas('payments');
            } else {
                $this->properties->doesntHave('payments');
            }
        }

        if ($request->paid_start_date && $request->paid_end_date) {
            $year = date('Y', strtotime($request->paid_start_date));
            //$this->properties->whereYear('property_payments.created_at', $year);

            $this->properties->whereHas('payments', function ($query) use ($request) {
                return $query->whereBetween('property_payments.created_at', [Carbon::parse($request->paid_start_date)->startOfDay(), Carbon::parse($request->paid_end_date)->endOfDay()]);
            });
        }

        !$request->occupancy_type ?: $this->properties->whereHas('occupancy', function ($query) use ($request) {
            return $query->where('type', $request->occupancy_type);
        });

        $request->filled('property_id') && $this->properties->where('properties.id', $request->input('property_id'));

        !$request->town ?: $this->properties->where('properties.section', $request->town);
        !$request->street_name ?: $this->properties->where('properties.street_name', 'like', "%{$request->street_name}%");
        !$request->street_number ?: $this->properties->where('properties.street_number', $request->street_number);
        !$request->postcode ?: $this->properties->where('properties.postcode', $request->postcode);
        !$request->ward ?: $this->properties->where('properties.ward', $request->ward);
        !$request->district ?: $this->properties->where('properties.district', $request->district);
        !$request->province ?: $this->properties->where('properties.province', $request->province);
        !$request->chiefdom ?: $this->properties->where('properties.chiefdom', $request->chiefdom);
        !$request->constituency ?: $this->properties->where('properties.constituency', $request->constituency);

        $request->is_accessible == "0" ? $this->properties->where('is_property_inaccessible', 0) : null;
        $request->is_accessible == "1" ? $this->properties->where('is_property_inaccessible', 1) : null;

        $request->is_draft_delivered == "0" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNull('demand_note_delivered_at');
        }) : null;
        $request->is_draft_delivered == "1" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            if ($request->dd_start_date && $request->dd_end_date) {
                $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::parse($request->dd_end_date)]);
            } else {
                if ($request->dd_start_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::now()]);
                } else if ($request->dd_end_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::now()->subYear(5),  Carbon::parse($request->dd_end_date)]);
                } else {
                    $query->whereYear('created_at', now()->format('Y'))->whereNotNull('demand_note_delivered_at');
                }
            }
        }) : null;


        !$request->digital_address ?: $this->properties->where('properties.id', $request->digital_address);

        !$request->old_digital_address ?: $this->properties->where('properties.id', $request->old_digital_address);

        !$request->is_completed ?: $this->properties->where('properties.is_completed', ($request->is_completed == 'yes' ? true : false));

        !$request->type ?: $this->properties->whereHas('types', function ($query) use ($request) {
            return $query->where('id', $request->type);
        });

        !$request->wall_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_wall_materials', $request->wall_material);
        });

        !$request->compound_name ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('compound_name', 'like', "%$request->compound_name%");
        });

        !$request->roof_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('roofs_materials', $request->roof_material);
        });

        !$request->property_dimension ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_dimension', $request->property_dimension);
        });

        !$request->value_added ?: $this->properties->whereHas('valueAdded', function ($query) use ($request) {
            return $query->where('id', $request->value_added);
        });

        !$request->property_inaccessible ?: $this->properties->whereHas('propertyInaccessible', function ($query) use ($request) {
            return $query->where('id', $request->property_inaccessible);
        });

        $this->properties->whereHas('landlord', function ($query) use ($request) {
            //
            if ($request->owner_first_name)
                $query = $query->where('first_name', 'like', "%{$request->owner_first_name}%");

            if ($request->owner_last_name)
                $query = $query->where('surname', 'like', "%{$request->owner_last_name}%");

            if ($request->input('mobile')) {
                $query->where('mobile_1', $request->input('mobile'));
            }

            return $query;
        });

        //!$request->landloard_name || $this->properties->orWhere('organization_name', 'like', '%' . $request->landloard_name . '%');

        !$request->telephone_number || $this->properties->whereHas('landlord', function ($query) use ($request) {
            return $query->where('mobile_1', 'like', '%' . $request->telephone_number . '%');
        });

        !$request->name ?: $this->properties->whereHas('user', function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
        });

        if ($request->input('is_organization') == 1 && $request->input('organization_type')) {
            $this->properties->where('organization_type', $request->input('organization_type'))->where('is_organization', true);
        }

        if ($request->input('is_organization') && $request->input('is_organization') == 0) {
            $this->properties->where('is_organization', false);
        }


        $data['types'] = PropertyType::pluck('label', 'id')->prepend('Property Type', '');
        $data['wallMaterial'] = PropertyWallMaterials::pluck('label', 'id')->prepend('Wall Material', '');
        $data['roofMaterial'] = PropertyRoofsMaterials::pluck('label', 'id')->prepend('Roof Material', '');
        $data['propertyDimension'] = PropertyDimension::pluck('label', 'id')->prepend('Dimensions', '');
        $data['valueAdded'] = PropertyValueAdded::where('is_active', true)->pluck('label', 'id')->prepend('Value Added', '');
        $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section')->prepend('Select Town', '');;

        $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
        $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');;
        $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select Ward', '');
        $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
        $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');

        $data['digital_address'] = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select Digital Address', '');

        $data['request'] = $request;

        $data['property_inaccessibles'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->prepend('Select Property Inaccessible');

        $data['street_names'] = Property::distinct('street_name')->orderBy('street_name')->pluck('street_name', 'street_name');
        $data['street_numbers'] = Property::distinct('street_number')->orderBy('street_number')->pluck('street_number', 'street_number');
        $data['postcodes'] = Property::distinct('postcode')->orderBy('postcode')->pluck('postcode', 'postcode');
        $data['organizationTypes'] = $organizationTypes;

        //return view('admin.payments.bulk-receipt')->with(['properties' => $this->properties->latest()->get()]);

        if ($request->download_pdf_in_bulk && $request->download_pdf_in_bulk == 1) {
            $bulkDemand = new PropertyInBulk();
            return $bulkDemand->handle($this->properties, $request->demand_draft_year);
        }

        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();
            return $stickers->handle($nProperty, $request);
        }

        if ($request->download_notice && $request->download_notice == 1) {

            $notices = new PropertyNotice();

            return $notices->handle($this->properties->latest()->get());
        }

        if ($request->download_excel_in_bulk && $request->download_excel_in_bulk == 1) {
            return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-properties.xlsx');
        }

        if ($request->bulk_demand && $request->bulk_demand == 2 && $this->properties->count() > 0) {

            $coordinates = $this->getMapCoordinates();
            $points = $coordinates[0];
            $center = $coordinates[1];

            return view('admin.properties.poly-map', compact('points', 'center'));

            $polygons = $this->properties->latest()->get();
        }

        if (!isset($request->sort_by)) {
            $this->properties = $this->properties->latest('properties.updated_at');
        }
        if ($request->sort_by == "is_completed") {
            $this->properties = $this->properties->orderBy('is_completed', $request->sort_dir)->orderBy('is_draft_delivered', $request->sort_dir);
        }
        // $data['list_user'] = User::pluck('name', 'name')->toArray();
        //dd($this->properties->toSql());
        return $usersGrid
            ->create(['query' => $this->properties, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.properties.list', $data);
        //return view('admin.properties.list',$data);

    }

    public function deleteMeter($id)
    {
        $registryMeter = RegistryMeter::findOrFail($id);

        if ($registryMeter->hasImage()) {
            unlink($registryMeter->getImage());
        }

        $registryMeter->delete();

        return response()->json(['success' => true]);
    }

    public function getMapCoordinates()
    {
        $properties = $this->properties->latest()->get();
        $points = [];
        $center = null;

        if ($properties->count()) {
            foreach ($properties as $key => $property) {

                if (optional($property->geoRegistry)->dor_lat_long) {
                    $point = explode(', ', $property->geoRegistry->dor_lat_long);
                } else {
                    continue;
                }

                if ($property->is_admin_created == 1) {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                } else if ($property->assessment->getCurrentYearTotalDue() < 1) {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                } else {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                }

                if (count($point) == 2) {
                    $points[] = [$property->getAddress(), $point[0], $point[1], $key++, $icon];
                }

                if ($property->geoRegistry->dor_lat_long) {
                    $center = $property->geoRegistry->dor_lat_long;
                }
            }
        }

        return [json_encode($points), $center];
    }

    public function downloadPdf(Request $request)
    {
        $this->validate($request, [
            'properties' => 'required',

        ], [
            'properties.required' => 'Select at least one property'
        ]);

        $this->properties = Property::with([
            'landlord',
            'geoRegistry',
            'user'
        ]);

        $this->properties = $this->properties->whereIn('properties.id', explode(',', $request->properties));

        if ($request->download_excel) {

            $this->properties->with([
                'assessment' => function ($query) use ($request) {
                    $query->whereYear('created_at', $request->input('demand_draft_year'))
                        ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
                },
            ]);

            $this->properties->whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', $request->input('demand_draft_year'));
            });

            return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-properties.xlsx');
        }

        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();

            return $stickers->handle($nProperty, $request);
        }

        $bulkDemand = new PropertyInBulk();

        return $bulkDemand->handle($this->properties, $request->demand_draft_year);
    }

    public function show(Request $request)
    {
        /* @var $property Property */
        $property = Property::findOrFail($request->property);

        // Generate current year assessment if missing
        $property->generateAssessments();

        // load sub modals
        $property->load([
            'images',
            'occupancy',
            'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
            'geoRegistry',
            'payments',
            'landlord',
            'propertyInaccessible'
        ]);

        $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section');
        $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort();
        $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort();
        $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort();
        $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort();

        $data['categories'] = PropertyCategory::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['types'] = PropertyType::distinct()->where('is_active', 1)->pluck('label', 'id');

        $data['wall_materials'] = PropertyWallMaterials::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['roofs_materials'] = PropertyRoofsMaterials::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['property_dimension'] = PropertyDimension::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['value_added'] = PropertyValueAdded::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['property_use'] = PropertyUse::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['zone'] = PropertyZones::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['occupancy_type'] = ['Owned Tenancy' => 'Owned Tenancy', 'Rented House' => 'Rented House', 'Unoccupied House' => 'Unoccupied House'];
        $data['id_type'] = ['National ID' => 'National ID', 'Passport' => 'Passport', 'Driver’s License' => 'Driver’s License', 'Voter ID' => 'Voter ID', 'other' => 'Other'];
        $data['org_type'] = ['Government' => 'Government', 'NGO' => 'NGO', 'Business' => 'Business', 'School' => 'School', 'Religious' => 'Religious', 'Diplomatic Mission' => 'Diplomatic Mission', 'Hospital' => 'Hospital', 'Other' => 'Other'];
        $data['gender'] = ['m' => 'Male', 'f' => 'Female'];
        $data['title'] = 'Details';
        $data['property'] = $property;
        $data['selected_occupancies'] = $property->occupancies->pluck('occupancy_type')->toArray();

        $data['property_inaccessable'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->toArray();
        $data['selected_property_inaccessable'] = $property->propertyInaccessible()->pluck('id')->toArray();
        $data['swimmings'] = Swimming::where('is_active', 1)->pluck('label', 'id')->prepend('Select', '')->toArray();

        return view('admin.properties.view', $data);
    }


    public function create()
    {
    }

    public function assignProperty(Request $request)
    {

        $data['title'] = 'Details';
        $data['request'] = $request;
        $data['assessmentOfficer'] = $assessmentUser = User::pluck('name', 'id')->prepend('Select Officer', '');

        return view('admin.properties.assign', $data);
    }

    public function saveAssignProperty(Request $request)
    {
        $this->validate($request, [
            'assessment_officer' => 'required|exists:users,id',
            'dor_lat_long' => ['required', 'regex:/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/']
        ]);

        $assessmentOfficer = User::findOrFail($request->assessment_officer);
        $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
        $property->is_admin_created = 1;
        $property->is_organization = 0;
        $property->is_completed = 0;
        $property->is_draft_delivered = 0;
        $property->is_property_inaccessible = 0;
        $property->save();
        $property->landlord()->firstOrCreate(["property_id" => $property->id, "mobile_1" => "+232", "mobile_2" => "+232"]);
        $property->occupancy()->firstOrCreate(["property_id" => $property->id, "mobile_1" => "+232", "mobile_2" => "+232"]);
        if ($property->assessment()->exists()) {

            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
        }

        $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
        $geoRegistry->fill(['dor_lat_long' => $request->dor_lat_long]);
        $geoRegistry->save();

        return redirect()->back()->with('success', 'New Property Assigned Successfully!');
    }

    public function destroy(Request $request)
    {
        /* @var $property Property */
        $property = Property::findOrFail($request->property);

        $property->landlord()->delete();
        $property->occupancy()->delete();
        $property->assessments()->delete();
        $property->geoRegistry()->delete();
        $property->categories()->detach();
        $property->types()->detach();
        $property->occupancies()->delete();
        $property->payments()->delete();
        $property->registryMeters()->delete();
        $property->propertyInaccessible()->detach();

        $property->delete();

        return redirect()->back()->with($this->setMessage('Property successfully deleted', true));
    }


    public function saveLandlord(Request $request)
    {
        //dd($request->all());
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'landlord_id' => "required|integer",
            'is_organization' => 'required|boolean',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_addresss' => 'nullable|string|max:255',
            'first_name' => 'required_if:is_organization,0|nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required_if:is_organization,0|nullable|string|max:255',
            'sex' => 'required_if:is_organization,0|nullable|string|max:255',
            'street_number' => 'required|string',
            'email' => "nullable|email",
            'street_name' => 'required|string|max:255|nullable',
            'tin' => 'nullable|string|max:255',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput()->with('id', 'landloard');
        }
        $data = $request->all();
        unset($data['landlord_id']);
        unset($data['property_id']);
        unset($data['organization_name']);
        unset($data['organization_tin']);
        unset($data['organization_type']);
        unset($data['organization_addresss']);


        $property = Property::findorFail($request->property_id);

        $property->organization_name = $request->organization_name;
        $property->organization_type = $request->organization_type;
        $property->organization_tin = $request->organization_tin;
        $property->organization_addresss = $request->organization_addresss;
        $property->is_organization = $request->is_organization;
        $property->save();

        $landlord = $property->landlord()->first();

        if ($request->hasFile('image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $data['image'] = $request->image->store(Property::ASSESSMENT_IMAGE);
        }

        $landlord->fill($data);

        $landlord->save();


        return redirect()->back()->with('success', 'Landlord details Updated Successfully !');
    }

    public function saveProperty(Request $request)
    {
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'street_number' => 'required|string',
            'street_name' => 'required|string|max:255|nullable',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->with('id', 'property');
        }

        $data = $request->all();

        $property = Property::findorFail($request->property_id);
        $property->fill($data);
        $property->is_property_inaccessible = ($request->property_inaccessable && count($request->property_inaccessable)) ? true : false;
        $property->is_draft_delivered = $request->is_draft_delivered ? $request->is_draft_delivered : 0;
        $property->delivered_name = $request->delivered_name;
        $property->delivered_number = $request->delivered_number;

        if ($request->hasFile('delivered_image')) {
            $property->delivered_image = $request->delivered_image->store(Property::DELIVERED_IMAGE);
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessable);

        return redirect()->back()->with('success', 'Property details Updated Successfully !');
    }

    public function saveOccupancy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "occupancy_id" => "required|integer",
            "property_id" => "required|integer",
            'occupancy_type' => 'nullable|array',
            'occupancy_type.*' => 'nullable|in:Owned Tenancy,Rented House,Unoccupied House',
            "tenant_first_name" => "nullable|string|max:50",
            "middle_name" => "nullable|string|max:40",
            "surname" => "nullable|string|max:30",
            "mobile_1" => "nullable|string|max:15",
            "mobile_2" => "nullable|string|max:15"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->with('id', 'occupancy');
        }

        $data = $request->all();

        $property = Property::findorFail($request->input('property_id'));

        $occupancy = $property->occupancy()->first();
        $occupancy->fill($data);
        $occupancy->save();

        if (count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        return redirect()->back()->with('success', 'Occupancy details Updated Successfully !');
    }

    public function saveAssessment(Request $request)
    {
        $request->validate([
            "assessment_id" => "required|integer",
            "property_id" => "required|integer",
            'property_categories' => 'nullable|array',
            'property_categories.*' => 'nullable|exists:property_categories,id',
            "property_types" => "required|array|max:2",
            "property_types.*" => 'required|exists:property_types,id',
            "property_types_total" => "nullable|array|max:2",
            "property_types_total.*" => 'nullable|exists:property_types,id',
            "property_wall_materials" => "required|integer",
            "roofs_materials" => "required|integer",
            "property_dimension" => "required|integer",
            "property_value_added.*" => "required|exists:property_value_added,id",
            "property_use" => "required|integer",
            "zone" => "required|integer",
            'assessment_images_1' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'assessment_images_2' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $data = $request->except(['property_types', 'property_types_total', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        //$data = $request->except(['property_types', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        /* @var $property Property */

        $property = Property::findorFail($request->input('property_id'));

        /* @var $assessment PropertyAssessmentDetail */
        $assessment = $property->assessment()->findOrFail($request->input('assessment_id'));

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment->hasImageOne()) {
                unlink($assessment->getImageOne());
            }
            $data['assessment_images_1'] = $request->file('assessment_images_1')->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment->hasImageTwo()) {
                unlink($assessment->getImageTwo());
            }
            $data['assessment_images_2'] = $request->file('assessment_images_2')->store(Property::ASSESSMENT_IMAGE);
        }

        /* @var $assessment PropertyAssessmentDetail */
        $data['gated_community'] = $data['gated_community'] ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null;

        $assessment->fill($data);
        $assessment->swimming()->associate($request->input('swimming_pool'));
        $assessment->save();

        $categories = getSyncArray($request->input('property_categories'), ['property_id' => $property->id]);

        $assessment->categories()->sync($categories);

        /* Property type (Habitat) multiple value */
        $types = getSyncArray($request->input('property_types'), ['property_id' => $property->id]);
        $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        $typesTotal = getSyncArray($request->input('property_types_total'), ['property_id' => $property->id]);
        $assessment->typesTotal()->sync($typesTotal);

        /* Property value added multiple value */
        $valuesAdded = getSyncArray($request->input('property_value_added'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);

        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }

    public function saveGeoRegistry(Request $request)
    {
        /* @var $geoRegistry PropertyGeoRegistry */
        $geoRegistry = PropertyGeoRegistry::findOrFail($request->input('property_geo_registry_id'));

        $this->validate($request, [
            'digital_address' => 'required|unique:property_geo_registry,digital_address,' . $geoRegistry->id,
            'dor_lat_long' => 'required'
        ]);

        $geoRegistry->fill($request->all());

        $geoRegistry->save();

        /*$v = Validator::make($request->all(), [
            "georegistry_id" => "required|integer",
            "property_id" => "required|integer",
            'registry' => 'required|array',
            'registry.*.meter_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png','max:5120'] ,
            'registry.*.meter_number' => 'nullable|string|max:255',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->with('id','geo-registry');
        }*/

        /* @var $property Property */
        $property = Property::findOrFail($request->property_id);

        if (count($request->registry) and is_array($request->registry)) {

            foreach (array_filter($request->registry) as $key => $registry) {

                if (isset($registry['id']) && $registry['id'] != null) {
                    $registryImageId[] = $registry['id'];
                    $regdata['number'] = $registry['meter_number'];

                    $registryMeters = $property->registryMeters()->where('id', $registry['id'])->first();
                    $regdata['image'] = $registryMeters->image;
                    if ($request->hasFile('registry.' . $key . '.meter_image')) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        $regdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                    }

                    $property->registryMeters()->where('id', $registry['id'])->update($regdata);
                } else {
                    if ($registry['meter_number'] != null) {

                        $Cregdata['number'] = $registry['meter_number'];
                        if ($request->hasFile('registry.' . $key . '.meter_image')) {

                            $Cregdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                        }
                        $property->registryMeters()->create($Cregdata);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }
}
