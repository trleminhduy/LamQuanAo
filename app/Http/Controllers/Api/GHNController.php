<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\GHNService;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    protected $ghnService;

    public function __construct(GHNService $gHNService)
    {
        $this->ghnService = $gHNService;
    }

    // Lấy danh sách tỉnh/thành phố từ GHN

    public function getProvinces()
    {
        $result = $this->ghnService->getProvinces();

        if($result['success']){
            return response()->json([
                'status' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'],
        ], 400);
        
    }

    // Lấy danh sách quận/huyện từ GHN

    public function getDistricts(Request $request){

        $provinceId = $request->input('province_id');
        
        if(!$provinceId){
            return response()->json([
                'status' => false,
                'message' => 'Thiếu province_id',
            ], 400);
        }

        $result = $this->ghnService->getDistricts($provinceId);

        if($result['success']){
            return response()->json([
                'status' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'],
        ], 400);
        
    }

    // Lấy danh sách xã/phường từ GHN
    public function getWards(Request $request){


        $districtId = $request->input('district_id');


        if(!$districtId){
            return response()->json([
                'status' => false,
                'message' => 'Thiếu district_id',
            ], 400);
        }

        
        $result = $this->ghnService->getWards($districtId);

        if($result['success']){
            return response()->json([
                'status' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'],
        ], 400);
    }
}