<?php

namespace App\Http\Controllers\Api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;
use RestResponse;
use File;
use Storage;

class SettingsController extends Controller
{
    private $perCompanySetting;
    public function __construct()
    {
        $this->perCompanySetting = config('constant.PERMISSION_COMPANY_SETTING');
    }
    public function getCompanySettings(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perCompanySetting)){
                /*$companyId= $request->query('company_id');
                if(!empty($companyId)){*/
                    $getCompanySetting = CompanySettings::with('currency')->first();
                    if(empty($getCompanySetting)){
                        return RestResponse::warning('Company settings not found.');
                    }
                    $data['company_setting'] = $getCompanySetting;
                    $data['all_currency'] = Currency::where('active',1)->get();
                    return RestResponse::success($data, 'Company settings retrieve successfully.');
                /*}else{
                    return RestResponse::warning('Company id is required.');
                }*/
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function updateCompanySettings(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perCompanySetting)){
                $validate = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'company_name' => 'required',
                    'address_line1' => 'required',
                    'area' => 'required',
                    'zipcode' => 'required|min:4|max:8',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'currency_id' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $getCompanySetting = CompanySettings::where('id',$request['company_id'])->first();
                if(empty($getCompanySetting)){
                    return RestResponse::warning('User company setting not found.');
                }

                $getCompanySetting['company_name'] = $request['company_name'];
                $getCompanySetting['address_line1'] = $request['address_line1'];
                $getCompanySetting['area'] = $request['area'];
                $getCompanySetting['zipcode'] = $request['zipcode'];
                $getCompanySetting['city'] = $request['city'];
                $getCompanySetting['state'] = $request['state'];
                $getCompanySetting['country'] = $request['country'];
                $getCompanySetting['currency_id'] = $request['currency_id'];
                $s3 = Storage::disk('s3');
                if(array_key_exists('company_logo',$request->all()) && !empty($request['company_logo'])){
                    $logoUrl = $request->file('company_logo');
                    if (!empty($logoUrl)) {
                        if (File::size($logoUrl) > 2097152) {
                            return RestResponse::warning('Company logo upto 2 Mb max.', 422);
                        }
                        $extension = $logoUrl->getClientOriginalExtension();
                        if (!in_array(strtolower($extension), array("png", "jpeg", "jpg", "gif", "svg"))) {
                            return RestResponse::warning('Company logo must be a PNG, JPEG, GIF, SVG file.', 422);
                        }
                        $imageName = time() . '-' . rand(0, 100) . '.' . $extension;
                        $filePath = 'company_logo/' . $imageName;
                        $s3->put($filePath, file_get_contents($logoUrl),'public');
                        if ($getCompanySetting->company_logo != "") {
                            $s3->delete('company_logo/' . $getCompanySetting->company_logo);
                        }
                        $getCompanySetting['company_logo'] = $imageName;
                    }else {
                        if (!Str::contains($request['company_logo'], $getCompanySetting->getAttributes()['company_logo'])) {
                            return RestResponse::warning('Whoops something went wrong.');
                        }
                    }
                }else{
                    if ($getCompanySetting->company_logo != "") {
                        $s3->delete('company_logo/' . $getCompanySetting->company_logo);
                    }
                    $getCompanySetting['company_logo'] = null;
                }
                if(array_key_exists('company_favicon',$request->all()) && !empty($request['company_favicon'])){
                    $faviconUrl = $request->file('company_favicon');
                    if (!empty($faviconUrl)) {
                        if (File::size($faviconUrl) > 2097152) {
                            return RestResponse::warning('Company favicon upto 2 Mb max.', 422);
                        }
                        $extension = $faviconUrl->getClientOriginalExtension();
                        if (!in_array(strtolower($extension), array("png", "jpeg", "jpg", "gif", "svg"))) {
                            return RestResponse::warning('Company favicon must be a PNG, JPEG, GIF, SVG file.', 422);
                        }
                        $imageName = time() . '-' . rand(0, 100) . '.' . $extension;
                        $filePath = 'company_favicon/' . $imageName;
                        $s3->put($filePath, file_get_contents($faviconUrl),'public');
                        if ($getCompanySetting->company_favicon != "") {
                            $s3->delete('company_favicon/' . $getCompanySetting->company_favicon);
                        }
                        $getCompanySetting['company_favicon'] = $imageName;
                    }else {
                        if (!Str::contains($request['company_logo'], $getCompanySetting->getAttributes()['company_logo'])) {
                            return RestResponse::warning('Whoops something went wrong.');
                        }
                    }
                }else{
                    if ($getCompanySetting->company_favicon != "") {
                        $s3->delete('company_favicon/' . $getCompanySetting->company_favicon);
                    }
                    $getCompanySetting['company_favicon'] = null;
                }
                $getCompanySetting->save();
                return RestResponse::success([], 'Company settings updated successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getCurrency(){
        $defaultCurrency =  CompanySettings::select('id','currency_id')->with(['currency'=> function($q){
            $q->select('id','name','symbol');
        }])->first();
        return RestResponse::success($defaultCurrency, 'Company currency');
    }
}
