<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProblemType;
use Illuminate\Http\Request;
use Validator;
use RestResponse;
class ProblemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $getAllProblemType = ProblemType::all();
            if(empty($getAllProblemType)){
                return RestResponse::warning('Problem type not found.');
            }
            return RestResponse::success($getAllProblemType,'Problem type list retrieve successfully.');
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
            $validate = Validator::make($request->all(), [
                'problem_name' => 'required|unique:problem_types'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $data['problem_name'] = $request['problem_name'];
            $data['is_active'] = 1;
            $create = ProblemType::create($data);
            if(!$create){
                return RestResponse::warning('Problem type create failed.');
            }
            return RestResponse::success([], 'Problem type created successfully.');
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
            $getProblemType = ProblemType::find($id);
            if(empty($getProblemType)){
                return RestResponse::warning('Problem type not found.');
            }
            return RestResponse::success($getProblemType,'Problem type retrieve successfully.');
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
            $validate = Validator::make($request->all(), [
                'problem_name' => 'required|unique:problem_types,problem_name,'.$id,
                'is_active' => 'required'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $findProblemType = ProblemType::find($id);
            if(empty($findProblemType)){
                return RestResponse::warning('Problem type not found.');
            }

            $findProblemType['problem_name'] = $request['problem_name'];
            $findProblemType['is_active'] = $request['is_active'];
            $findProblemType->save();
            return RestResponse::success([], 'Problem type updated successfully.');
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
            $getProblemType = ProblemType::find($id);
            if (empty($getProblemType)) {
                return RestResponse::warning('Problem type not found.');
            }
            $getProblemType->delete();
            return RestResponse::Success([],'Problem type deleted successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
