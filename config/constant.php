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
    ],
];

?>
