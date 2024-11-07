<?php
namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;  
use App\Models\Ubigeo\Country;
use App\Models\Ubigeo\Department;
use App\Models\Ubigeo\Province;
use App\Models\Ubigeo\District; 
use App\Helpers\ResponseHelper;

class UbigeoController extends Controller
{
    // Fetch all countries
    public function getCountries()
    {
        $countries = Country::all();
        return ResponseHelper::json($countries);   
    }

    // Fetch all departments based on country ID
    public function getDepartments($countryId)
    {
         
        $departments = Department::where('country_name', $countryId)->get()
                                 ->map(function ($department) {
                                    return [
                                        'id' => $department->iddepartamento,
                                        'name' => $department->nomdepartamento,
                                    ];
                                 });

        return ResponseHelper::json($departments);  
    }

    // Fetch all provinces based on department ID
    public function getProvinces($departmentId)
    { 
        $provinces = Province::where('iddepartamento', $departmentId)->get()
                                ->map(function ($province) {
                                    return [
                                        'id' => $province->idprovincia,
                                        'name' => $province->nomprovincia,
                                    ];
                                });

        return ResponseHelper::json($provinces);
    }

    // Fetch all districts based on province ID
    public function getDistricts($provinceId)
    { 
        $districts = District::where('idprovincia', $provinceId)->get()
                                ->map(function ($district) {
                                    return [
                                        'id' => $district->iddistrito,
                                        'name' => $district->nomdistrito,
                                    ];
                                });
                                
        return ResponseHelper::json($districts);
    }
}
