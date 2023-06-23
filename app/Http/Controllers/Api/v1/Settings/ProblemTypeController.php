<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProblemType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use RestResponse;
class ProblemTypeController extends Controller
{
    private $perProblemType;
    public function __construct()
    {
        $this->perProblemType = config('constant.PERMISSION_PROBLEM_TYPE_CRUD');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProblemType)) {
                $getAllProblemType = ProblemType::all();
                if(empty($getAllProblemType)){
                    return RestResponse::warning('Problem type not found.');
                }
                return RestResponse::success($getAllProblemType,'Problem type list retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProblemType)) {
                $validate = Validator::make($request->all(), [
                    'problem_name' => 'required|unique:problem_types,problem_name,NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $create = ProblemType::create(['problem_name' => $request['problem_name']]);
                if(!$create){
                    return RestResponse::warning('Problem type create failed.');
                }
                return RestResponse::success($create, 'Problem type created successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProblemType)) {
                $getProblemType = ProblemType::find($id);
                if(empty($getProblemType)){
                    return RestResponse::warning('Problem type not found.');
                }
                return RestResponse::success($getProblemType,'Problem type retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProblemType)) {
                $validate = Validator::make($request->all(), [
                    'problem_name' => 'required|unique:problem_types,problem_name,'.$id.'NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $findProblemType = ProblemType::find($id);
                if(empty($findProblemType)){
                    return RestResponse::warning('Problem type not found.');
                }

                $findProblemType['problem_name'] = $request['problem_name'];
                $findProblemType->save();
                return RestResponse::success([], 'Problem type updated successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perProblemType)) {
                $getProblemType = ProblemType::find($id);
                if (empty($getProblemType)) {
                    return RestResponse::warning('Problem type not found.');
                }
                $getProblemType->delete();
                return RestResponse::Success([],'Problem type deleted successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
