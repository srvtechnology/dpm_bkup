<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $properties;
    protected $year;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    //    public function collection()
    //    {
    //        return $this->properties->select(
    //            'users.name',
    //            'properties.street_number',
    //            'properties.street_name',
    //            'properties.ward',
    //            'properties.constituency',
    //            'properties.section',
    //            'properties.chiefdom',
    //            'properties.district',
    //            'properties.province',
    //            'properties.postcode',
    //            'properties.organization_addresss',
    //            'landlord_details.first_name',
    //            'landlord_details.middle_name',
    //            'landlord_details.surname',
    //            'landlord_details.sex',
    //            'landlord_details.street_name as landlord_steet',
    //            'landlord_details.ward as landlord_ward',
    //            'landlord_details.constituency  as landlord_constituency',
    //            'landlord_details.section as landlord_section',
    //            'landlord_details.chiefdom as landlord_chiefdom',
    //            'landlord_details.district as landlord_district',
    //            'landlord_details.province as landlord_province',
    //            'landlord_details.postcode as landlord_postcode',
    //            'landlord_details.mobile_1',
    //            'landlord_details.mobile_2',
    //            'property_assessment_details.property_rate_without_gst',
    //            'property_assessment_details.property_use',
    //            'property_assessment_details.zone',
    //            'property_assessment_details.no_of_mast',
    //            'property_assessment_details.no_of_shop',
    //            'property_assessment_details.no_of_compound_house',
    //            'property_assessment_details.compound_name',
    //            'property_geo_registry.digital_address'
    //        )
    //            ->addSelect(\DB::raw('SUM(property_payments.amount) as paid_amount'))
    //            ->leftJoin('property_assessment_details', 'property_assessment_details.property_id', '=', 'properties.id')
    //            ->leftJoin('landlord_details', 'landlord_details.property_id', '=', 'properties.id')
    //            ->leftJoin('property_geo_registry', 'property_geo_registry.property_id', '=', 'properties.id')
    //            ->leftJoin('users', 'users.id', '=', 'properties.user_id')
    //            ->leftJoin('property_payments', 'property_payments.property_id', '=', 'properties.id')
    //            ->groupBy('properties.id')
    //            ->orderBy('properties.id', 'desc')->get();
    //
    //    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->assessment->created_at->format('Y'),
            $row->user->name,
            $row->street_number,
            $row->street_name,
            $row->ward,
            $row->constituency,
            $row->section,
            $row->chiefdom,
            $row->district,
            $row->province,
            $row->postcode,
            $row->organization_name,
            $row->organization_addresss,
            $row->landlord->first_name,
            $row->landlord->middle_name,
            $row->landlord->surname,
            $row->landlord->sex,
            $row->landlord->street_name,
            $row->landlord->ward,
            $row->landlord->constituency,
            $row->landlord->section,
            $row->landlord->chiefdom,
            $row->landlord->district,
            $row->landlord->province,
            $row->landlord->postcode,
            $row->landlord->mobile_1,
            $row->landlord->mobile_2,
            $row->assessment->property_rate_without_gst,
            $row->assessment->property_use,
            $row->assessment->zone,
            $row->assessment->no_of_mast,
            $row->assessment->no_of_shop,
            $row->assessment->no_of_compound_house,
            $row->assessment->compound_name,
            $row->geoRegistry->digital_address,
            //$row->landlord->postcode ." ". $row->geoRegistry->open_location_code,
            $row->newDigitalAddress(),
            $row->assessment->getTotalPayable(),
            $row->assessment->getCurrentYearTotalPayment(),
            $row->is_draft_delivered,
            $row->assessment->gated_community
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Year',
            'User Name',
            'Property Street Number',
            'Property Street Name',
            'Property Ward',
            'Property Constituency',
            'Property Section',
            'Property Chiefdom',
            'Property District',
            'Property Province',
            'Property Postcode',
            'Property Organization Name',
            'Property Organization Address',
            'Landlord First Name',
            'Landlord Middle Name',
            'Landlord Surname',
            'Landlord Gender',
            'Landlord Street Name',
            'Landlord Ward',
            'Landlord Constituency',
            'Landlord Section',
            'Landlord Chiefdom',
            'Landlord District',
            'Landlord Province',
            'Landlord Postcode',
            'Landlord Mobile 1',
            'Landlord Mobile 2',
            'Assessment Property Rate Without Gst',
            'Assessment Property Use',
            'Assessment Zone',
            'Assessment No Of Mast',
            'Assessment No Of Shop',
            'Assessment No Of Compound House',
            'Assessment Compound Name',
            'Digital Address',
            'Open Location Code',
            'Total Due',
            'Amount Paid',
            'Is Draft Delivered',
            'Gated Community'

        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->properties;
    }
}
