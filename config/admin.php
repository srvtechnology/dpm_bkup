<?php

return [

    'side_nav' => [
        [
            'label' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'admin.dashboard',
            'role' => 'Super Admin|manager',
        ],
        [
            'label' => 'Properties',
            'icon' => 'list',
            'route' => 'admin.properties',
            'role' => 'Super Admin|manager|supervisor',
        ],
        [
            'label' => 'Payments',
            'icon' => 'payment',
            'route' => 'admin.payment',
            'role' => 'Super Admin|manager|cashiers|supervisor',
        ],
        [
            'label' => 'Assign Properties',
            'icon' => 'assignment_ind',
            'route' => 'admin.assign.property',
            'role' => 'Super Admin',
        ],
        [
            'label' => 'Users',
            'icon' => 'perm_identity',
            'role' => 'Super Admin|manager',
            'children' => [
                [
                    'label' => 'System User',
                    'icon' => 'accessible',
                    'children' => [
                        [
                            'label' => 'List',
                            'route' => 'admin.system-user.list',
                        ],
                        [
                            'label' => 'Create',
                            'route' => 'admin.system-user.create',
                        ],

                    ]
                ],
                [
                    'label' => 'App User',
                    'icon' => 'apps',
                    'children' => [
                        [
                            'label' => 'List',
                            'route' => 'admin.app-user.list',
                        ],
                        [
                            'label' => 'Create',
                            'route' => 'admin.app-user.create',
                        ],

                    ]
                ],

            ]
        ],
        [
            'label' => 'Assessment Options',
            'icon' => 'assessment',
            'role' => 'Super Admin|manager',
            'children' => [
                [
                    'label' => 'Property Categories',
                    'route' => 'admin.list.property.category',

                ],
                [
                    'label' => 'Property Types',
                    'route' => 'admin.list.property.type',

                ],
                [
                    'label' => 'Wall Material',
                    'route' => 'admin.list.property.wall-material',

                ],
                [
                    'label' => 'Roof Material',
                    'route' => 'admin.list.property.roof-material',

                ],
                [
                    'label' => 'Property Dimensions',
                    'route' => 'admin.list.property.dimension',

                ],
                [
                    'label' => 'Value Added',
                    'route' => 'admin.list.property.value-added',

                ],
                [
                    'label' => 'Property Use',
                    'route' => 'admin.list.property.use',

                ],
                [
                    'label' => 'Zones',
                    'route' => 'admin.list.property.zone',

                ],
                [
                    'label' => 'Swimming Pool',
                    'route' => 'admin.list.property.swimming',

                ],
                [
                    'label' => 'Property Inaccessible',
                    'route' => 'admin.list.property.inaccessible',

                ],
            ]
        ],
        [
            'label' => 'Meta Options',
            'icon' => 'donut_small',
            'role' => 'Super Admin|manager',
            'children' => [
                [
                    'label' => 'Create Meta',
                    'route' => 'admin.meta.value',

                ],
                [
                    'label' => 'First Name List',
                    'route' => 'admin.meta.value.first-name',

                ],
                [
                    'label' => 'Surname List',
                    'route' => 'admin.meta.value.surname',

                ],
                [
                    'label' => 'Street Name List',
                    'route' => 'admin.meta.value.street-name',

                ],
            ]
        ],
        [
            'label' => 'Reports',
            'icon' => 'graphic_eq',
            'route' => 'admin.report',
            'role' => 'Super Admin|supervisor|manager',
        ],
        [
            'label' => 'Online Charges',
            'icon' => 'graphic_eq',
            'route' => 'admin.online-payment',
            'role' => 'Super Admin|supervisor|manager',
        ],
        [
            'label' => 'Tax Payers',
            'icon' => 'graphic_eq',
            'route' => 'admin.tax-payer',
            'role' => 'Super Admin|supervisor|manager',
        ],
        [
            'label' => 'System Setting',
            'icon' => 'settings',
            'route' => 'admin.config.community',
            'role' => 'Super Admin',
        ],
        [
            'label' => 'Forgot Password Request',
            'icon' => 'change_history',
            'route' => 'admin.forgot.request',
            'role' => 'Super Admin',
        ],
        [
            'label' => 'Audit Trail',
            'icon' => 'donut_small',
            'role' => 'Super Admin',
            'children' => [
                [
                    'label' => 'App User Login Trail',
                    'route' => 'admin.audit.user',

                ],
                [
                    'label' => 'System User Login Trail',
                    'route' => 'admin.audit.admin',

                ],
                [
                    'label' => 'Property  Trail',
                    'route' => 'admin.audit.property',

                ],
                [
                    'label' => 'Property Assessment Trail',
                    'route' => 'admin.audit.property.assessment',

                ],
                [
                    'label' => 'Property Payment Trail',
                    'route' => 'admin.audit.property.payment',

                ],
                [
                    'label' => 'Property Landlord Trail',
                    'route' => 'admin.audit.property.landlord',

                ],
                [
                    'label' => 'Property Occupancy Trail',
                    'route' => 'admin.audit.property.occupancy',

                ],
                [
                    'label' => 'Property Occupancy Detail Trail',
                    'route' => 'admin.audit.property.occupancyDetail',

                ],
                [
                    'label' => 'Property Geo Registry Trail',
                    'route' => 'admin.audit.property.geoRegistry',

                ],
                [
                    'label' => 'Property Registry Meter Trail',
                    'route' => 'admin.audit.property.registryMeter',

                ],
                [
                    'label' => 'Property Categories Trail',
                    'route' => 'admin.audit.assessment.property.categories',

                ],
                [
                    'label' => 'Property Types Trail',
                    'route' => 'admin.audit.assessment.property.types',
                ],
                [
                    'label' => 'Property Wall Material Trail',
                    'route' => 'admin.audit.assessment.wall.material',
                ],
                [
                    'label' => 'Property Roof Material Trail',
                    'route' => 'admin.audit.assessment.roof.material',
                ],
                [
                    'label' => 'Property Dimensions Trail',
                    'route' => 'admin.audit.assessment.property.dimensions',
                ],
                [
                    'label' => 'Property Value Added Trail',
                    'route' => 'admin.audit.assessment.value.added',
                ],
                [
                    'label' => 'Property Use Trail',
                    'route' => 'admin.audit.assessment.property.use',
                ],
                [
                    'label' => 'Property Zone Trail',
                    'route' => 'admin.audit.assessment.property.zones',
                ],
                [
                    'label' => 'Property Swimming Pool Trail',
                    'route' => 'admin.audit.assessment.property.swimmingpool',
                ],
                [
                    'label' => 'Property Inaccessible Trail',
                    'route' => 'admin.audit.assessment.property.inaccessible',
                ],
            ]
        ],
        [
            'label' => 'Send SMS Text',
            'icon' => 'textsms',
            'route' => 'admin.notification.index',
            'role' => 'Super Admin',
        ],

    ]
];
