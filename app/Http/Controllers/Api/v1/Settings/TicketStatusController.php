<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use RestResponse;
class TicketStatusController extends Controller
{
    private $perTicketStatus;
    public function __construct()
    {
        $this->perTicketStatus = config('constant.PERMISSION_TICKET_STATUS_CRUD');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perTicketStatus)) {
                $getAllTicketStatus = TicketStatus::all();
                if(empty($getAllTicketStatus)){
                    return RestResponse::warning('Ticket Status not found.');
                }
                return RestResponse::success($getAllTicketStatus,'Ticket Status list retrieve successfully.');
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
            if(Auth::user()->hasPermissionTo($this->perTicketStatus)) {
                $validate = Validator::make($request->all(), [
                    'status_name' => 'required|unique:ticket_statuses,status_name,NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $createTicketStatus = TicketStatus::create(['status_name' => $request['status_name']]);
                if(!$createTicketStatus){
                    return RestResponse::warning('Ticket status create failed.');
                }
                return RestResponse::success([], 'Ticket status created successfully.');
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
            if(Auth::user()->hasPermissionTo($this->perTicketStatus)) {
                $getTicketStatus = TicketStatus::find($id);
                if(empty($getTicketStatus)){
                    return RestResponse::warning('Ticket status not found.');
                }
                return RestResponse::success($getTicketStatus,'Ticket status retrieve successfully.');
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
            if(Auth::user()->hasPermissionTo($this->perTicketStatus)) {
                $validate = Validator::make($request->all(), [
                    'status_name' => 'required|unique:ticket_statuses,status_name,'.$id.'NULL,id,deleted_at,NULL'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $findTicketStatus = TicketStatus::find($id);
                if(empty($findTicketStatus)){
                    return RestResponse::warning('Ticket status not found.');
                }

                if($findTicketStatus['is_lock'] == 1){
                    return RestResponse::warning("You can't update default ticket status.");
                }

                $findTicketStatus['status_name'] = $request['status_name'];
                $findTicketStatus->save();
                return RestResponse::success([], 'Ticket status updated successfully.');
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
            if(Auth::user()->hasPermissionTo($this->perTicketStatus)) {
                $getTicketStatus = TicketStatus::find($id);
                if (empty($getTicketStatus)) {
                    return RestResponse::warning('Ticket status not found.');
                }
                if($getTicketStatus['is_lock'] == 1){
                    return RestResponse::warning("You can't delete default ticket status.");
                }
                $getTicketStatus->delete();
                return RestResponse::Success([],'Ticket status deleted successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
