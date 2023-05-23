<?php
namespace App\RestResource;

use Illuminate\Support\Facades\Response;

class RestResponse {

    public function success($data=[],$msg='Success Response'){
        $response = [];
        $response['status'] = true;
        $response['code'] = 200;
        $response['message'] = $msg;
        $response['data']= $data;
        $response['error']= [];
        return Response::json($response,200);
    }

    public function error($msg = 'Whoops! Something went wrong.', $errorType = null, $data = []){
        $response = [];
        $response['status'] = false;
        $response['code'] = 500;
        $response['message'] = $msg;
        $response['data'] = $data;
        return Response::json($response, 500);
    }

    public function warning($msg='Warning occurred',$code=402){
        $response = [];
        $response['status'] = false;
        $response['code'] = $code;
        $response['message'] = $msg;
        return Response::json($response,$code);
    }

    public function validationError($error=[],$msg='Whoops! Validation is failed.'){
        $response = [];
        $response['status'] = false;
        $response['code'] = 400;
        $response['message'] = $msg;
        $response['error'] = $error;
        return Response::json($response,400);
    }
}
