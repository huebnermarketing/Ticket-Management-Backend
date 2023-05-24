<?php

return [
    'SEEDER_TYPE' => env('SEEDER_TYPE', "owner"),
    'FRONTEND_URL' => env('FRONTEND_URL', 'http://localhost:3000'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'no-reply+local@tickethub.com'),
    'APP_NAME' => env('APP_NAME', 'Ticket Management'),
    'MAIL_SALES_ADDRESS' => env('MAIL_SALES_ADDRESS', 'no-reply+local@tickethub.com'),

    //General
    'SOMETHING_WENT_WRONG_ERROR' => 'Whoops something went wrong.'
];

?>
