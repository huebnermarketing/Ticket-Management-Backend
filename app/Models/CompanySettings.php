<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySettings extends Model
{
    use HasFactory;
    protected $fillable = ['id','user_id','company_name','company_logo','company_favicon','address_line1',
        'area','zipcode','city','state','country','currency','created_at','updated_at'];

    public function getCompanyLogoAttribute(){
        $companyLogo = null;
        if(!empty($this->attributes['company_logo'])){
            $companyLogo = config('app.image_path')."company_logo/".$this->attributes['company_logo'];
        }
        return $companyLogo;
    }

    public function getCompanyFaviconAttribute(){
        $companyFavicon = null;
        if(!empty($this->attributes['company_favicon'])){
            $companyFavicon = config('app.image_path')."company_favicon/".$this->attributes['company_favicon'];
        }
        return $companyFavicon;
    }
}
