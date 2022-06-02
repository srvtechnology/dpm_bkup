<?php

namespace App\Grids;

use Closure;
use Leantony\Grid\Grid;

class TaxPayerGrid extends Grid implements TaxPayerGridInterface
{
    /**
     * The name of the grid
     *
     * @var string
     */
    protected $name = 'Tax Payers';

    protected $id = 'tax-payers';

    /**
     * List of buttons to be generated on the grid
     *
     * @var array
     */
    protected $buttonsToGenerate = [
//        'create',
//        'view',
//        'delete',
        'refresh',
    ];

    /**
     * Specify if the rows on the table should be clicked to navigate to the record
     *
     * @var bool
     */
    protected $linkableRows = false;

    /**
     * Set the columns to be displayed.
     *
     * @return void
     * @throws \Exception if an error occurs during parsing of the data
     */
    public function setColumns()
    {
        $this->columns = [
            "first_name" => [
                "label" => "First Name",
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "like"
                ]
            ],
            "middle_name" => [
                "label" => "Middle Name",
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "like"
                ]
            ],
            "last_name" => [
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "like"
                ]
            ],
            "mobile_1" => [
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "like"
                ]
            ],
            "properties_count" => [
                "sort" => true,
            ]
        ];
    }

    public function setRoutes()
    {
        // searching, sorting and filtering
        $this->setIndexRouteName('admin.tax-payer');

        // crud support
        $this->setCreateRouteName('admin.system-user.create');
        $this->setViewRouteName('admin.system-user.show');
        $this->setDeleteRouteName('admin.system-user.delete');

        // default route parameter
        $this->setDefaultRouteParameter('id');
    }

    /**
     * Return a closure that is executed per row, to render a link that will be clicked on to execute an action
     *
     * @return Closure
     */
    public function getLinkableCallback(): Closure
    {
        return function ($gridName, $item) {
            return route($this->getViewRouteName(), [$gridName => $item->id]);
        };
    }

    /**
     * Configure rendered buttons, or add your own
     *
     * @return void
     */
    public function configureButtons()
    {
        $this->editRowButton('delete', [
            'class' => 'delete-confirm data-remote grid-row-button btn btn-outline-danger btn-sm',
        ]);

        $this->editRowButton('view', [
            'name' => 'Edit',
        ]);

        $this->editRowButton('view', [
            'name' => 'Edit',
        ]);

        // call `addRowButton` to add a row button
        // call `addToolbarButton` to add a toolbar button
        // call `makeCustomButton` to do either of the above, but passing in the button properties as an array

        // call `editToolbarButton` to edit a toolbar button
        // call `editRowButton` to edit a row button
        // call `editButtonProperties` to do either of the above. All the edit functions accept the properties as an array
    }

    /**
     * Returns a closure that will be executed to apply a class for each row on the grid
     * The closure takes two arguments - `name` of grid, and `item` being iterated upon
     *
     * @return Closure
     */
    public function getRowCssStyle(): Closure
    {
        return function ($gridName, $item) {
            // e.g, to add a success class to specific table rows;
            // return $item->id % 2 === 0 ? 'table-success' : '';
            return "";
        };
    }
}
