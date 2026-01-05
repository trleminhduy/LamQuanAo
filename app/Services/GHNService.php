<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected $apiUrl;
    protected $token;
    protected $shopId;
    protected $fromDistrictId;


    public function __construct()
    {
        $this->apiUrl = config('ghn.api_url');
        $this->token = config('ghn.token');
        $this->shopId = config('ghn.shop_id');
        $this->fromDistrictId = config('ghn.from_district_id');
    }

    //API tính phí ship
    public function calculateShippingFee($toDistrictId, $toWardCode, $weight = 1000)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
            ])->post("{$this->apiUrl}/v2/shipping-order/fee", [
                'from_district_id' => (int)$this->fromDistrictId,
                'to_district_id' => (int)$toDistrictId,
                'to_ward_code' => $toWardCode,
                'weight' => (int)$weight,
                'service_type_id' => 2, // 2 = Giao hàng tiêu chuẩn
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'fee' => $response->json()['data']['total'],
                    'expected_delivery' => $response->json()['data']['expected_delivery_time'] ?? null
                ];
            }

            Log::error('Tính phí ship GHN lỗi', [
                'status' => $response->status(),
                'body' => $response->body()

            ]);
            return [
                'success' => false,
                'message' => 'Không thể tính phí ship'
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi tính phí ship GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function createOrder($orderData)
    {
        try {

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
            ])->post("{$this->apiUrl}/v2/shipping-order/create", $orderData);

            if ($response->successful()) {
                $data = $response->json()['data'];
                return [
                    'success' => true,
                    'order_code' => $data['order_code'],
                    'expected_delivery' => $data['expected_delivery_time'] ?? null,
                    'total_fee' => $data['total_fee'] ?? 0,

                ];
            }


            Log::error('Tạo đơn hàng GHN lỗi', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [
                'success' => false,
                'message' => 'Không thể tạo đơn hàng vận chuyển'
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi tạo đơn hàng GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    //Track info đơn hàng

    public function getOrderInfo($orderCode)
    {

        try {

            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->post("{$this->apiUrl}/v2/shipping-order/detail", [
                'order_code' => $orderCode,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin đơn hàng GHN',
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi lấy thông tin đơn hàng GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    //Hủy đơn hàng 
    public function cancelOrder($orderCode)
    {
        try {


            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
            ])->post("{$this->apiUrl}/v2/switch-status/cancel", [
                'order_codes' => [$orderCode],
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }


            Log::error('Hủy đơn hàng GHN lỗi', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'message' => 'Không thể hủy đơn hàng'];
        } catch (\Exception $e) {
            Log::error('Lỗi hủy đơn hàng GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    //Lấy dânh sách tình thành

    public function getProvinces()
    {

        try {

            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->get("{$this->apiUrl}/master-data/province");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return ['success' => false, 'message' => 'Không lấy được danh sách tỉnh/thành'];
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách tỉnh GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // quận huyện

    public function getDistricts($provinceId)
    {

        try {

            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->post("{$this->apiUrl}/master-data/district", [
                'province_id' => (int)$provinceId,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }


            return ['success' => false, 'message' => 'Không lấy được danh sách quận/huyện'];
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách quận huyện GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    //xã phường

    public function getWards($districtId)
    {


        try {

            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->post("{$this->apiUrl}/master-data/ward", [
                'district_id' => (int)$districtId,
            ]);
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return ['success' => false, 'message' => 'Không lấy được danh sách xã/phường'];
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách xã/phường GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    
    /*
    public function getOrderTracking($orderCode)
    {
        
        if (env('APP_ENV') === 'local' || $orderCode === 'DEMO') {
            return [
                'success' => true,
                'status' => 'delivering',
                'current_status' => 'Shipper đang giao hàng đến bạn',
                'log' => [
                    [
                        'status' => 'Shipper đang giao hàng',
                        'updated_date' => now()->format('Y-m-d H:i:s'),
                        'location' => 'Đang giao tại Quận 3, TP. Hồ Chí Minh'
                    ],
                    [
                        'status' => 'Đã đến trạm giao hàng',
                        'updated_date' => now()->subHour()->format('Y-m-d H:i:s'),
                        'location' => 'Trạm C - Bưu cục Quận 3, TP.HCM'
                    ],
                    [
                        'status' => 'Đã đến trạm phân loại',
                        'updated_date' => now()->subHours(3)->format('Y-m-d H:i:s'),
                        'location' => 'Trạm B - Hub Tân Bình, TP.HCM'
                    ],
                    [
                        'status' => 'Đã lấy hàng từ người gửi',
                        'updated_date' => now()->subHours(5)->format('Y-m-d H:i:s'),
                        'location' => 'Trạm A - Bưu cục Quận 1, TP.HCM'
                    ]
                ],
                'expected_delivery_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->post("{$this->apiUrl}/v2/shipping-order/detail", [
                'order_code' => $orderCode,
            ]);
            
            if ($response->successful()) {
                $data = $response->json()['data'];
                return [
                    'success' => true,
                    'status' => $data['status'] ?? '',
                    'current_status' => $data['current_status'] ?? '',
                    'log' => $data['log'] ?? [],
                    'expected_delivery_time' => $data['expected_delivery_time'] ?? null,
                ];
            }
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin tracking đơn hàng GHN'
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi lấy thông tin tracking GHN', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    */
}
