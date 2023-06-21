<?php

return [
    'SEEDER_TYPE' => env('SEEDER_TYPE', "owner"),
    'FRONTEND_URL' => env('FRONTEND_URL', 'http://localhost:3000'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'no-reply+local@tickethub.com'),
    'APP_NAME' => env('APP_NAME', 'Ticket Management'),
    'MAIL_SALES_ADDRESS' => env('MAIL_SALES_ADDRESS', 'no-reply+local@tickethub.com'),

    //General
    'PAGINATION_RECORD' =>  10,
    'SOMETHING_WENT_WRONG_ERROR' => 'Whoops something went wrong.',
    'USER_DONT_HAVE_PERMISSION'  =>  "You don't have permission to access this api.",

    'PAYMENT_MODE' =>['card','cash','online'],
    'USER_ROLES'    =>  [
        ['name'=>'owner','role_slug'=>'owner','display_name'=>'Owner','guard_name'=>'api'],
        ['name'=>'admin','role_slug'=>'admin','display_name'=>'Admin','guard_name'=>'api'],
        ['name'=>'user','role_slug'=>'user','display_name'=>'User','guard_name'=>'api']
    ],
    'USER_PERMISSIONS'  =>  [
        ['name' => 'user-auth','permission_slug' => 'user-auth','display_name'=>'User Auth','guard_name' => 'api'],
        ['name' => 'user-crud','permission_slug' => 'user-crud','display_name'=>'User CRUD','guard_name' => 'api'],
        ['name' => 'company-setting','permission_slug' => 'company-setting','display_name'=>'Company Setting','guard_name' => 'api'],
        ['name' => 'user-profile','permission_slug' => 'user-profile','display_name'=>'User Profile','guard_name' => 'api'],
        ['name' => 'customer-crud','permission_slug' => 'customer-crud','display_name'=>'Customer CRUD','guard_name' => 'api'],
        ['name' => 'contract-type-crud','permission_slug' => 'contract-type-crud','display_name'=>'Contract Type CRUD','guard_name' => 'api'],
        ['name' => 'problem-type-crud','permission_slug' => 'problem-type-crud','display_name'=>'Problem Type CRUD','guard_name' => 'api'],
        ['name' => 'ticket-status-crud','permission_slug' => 'ticket-status-crud','display_name'=>'Ticket Status CRUD','guard_name' => 'api'],
        ['name' => 'product-services-crud','permission_slug' => 'product-services-crud','display_name'=>'Product Services CRUD','guard_name' => 'api'],
    ],

    //Permissions
    'PERMISSION_USER_AUTH' => 'user-auth',
    'PERMISSION_USER_CRUD' => 'user-crud',
    'PERMISSION_COMPANY_SETTING' => 'company-setting',
    'PERMISSION_USER_PROFILE' => 'user-profile',
    'PERMISSION_CUSTOMER_CRUD' => 'customer-crud',
    'PERMISSION_CONTRACT_TYPE_CRUD' => 'contract-type-crud',
    'PERMISSION_PROBLEM_TYPE_CRUD' => 'problem-type-crud',
    'PERMISSION_TICKET_STATUS_CRUD' => 'ticket-status-crud',
    'PERMISSION_PRODUCT_SERVICES_CRUD' => 'product-services-crud',

    //Contract Durations
    'CONTRACT_DURATIONS' => [
        ['slug' => 'year', 'display_name'=> 'Year'],
        ['slug' => 'half-year', 'display_name'=> 'Half Year'],
        ['slug' => 'qtr', 'display_name'=> 'Qtr'],
        ['slug' => 'month', 'display_name'=> 'Month'],
    ],

    'CONTRACT_PAYMENT_TERM' => [
        ['slug' => 'month', 'display_name'=>'Month'],
        ['slug' => 'qtr', 'display_name'=>'Qtr'],
        ['slug' => 'half-year', 'display_name'=>'Half Year'],
        ['slug' => 'all-at-once', 'display_name'=>'All at Once'],
    ]
];

?>
