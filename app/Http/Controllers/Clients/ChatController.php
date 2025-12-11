<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // Lấy lịch sử cả khách lẫn người dùng
    public function fetchMessages(Request $request)
    {
        if (Auth::check()) {
            $msgs = ChatMessage::where('user_id', Auth::id())->orderBy('created_at')->get();
        } else {
            $token = $request->cookie('chat_token');
            $msgs = $token ? ChatMessage::where('guest_token', $token)->orderBy('created_at')->get() : collect();
        }
        return response()->json($msgs);
    }

    // Gửi tin nhắn
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();

        // Xử lý cookie kháchh
        $guestToken = null;
        if (!$userId) {
            $guestToken = $request->cookie('chat_token');
            if (!$guestToken) {
                $guestToken = 'guest_' . Str::random(32);
                // Lưu cookie 180 ngày 
                cookie()->queue(cookie('chat_token', $guestToken, 60 * 24 * 180));
            }
        }

        // bước 1: Lưu tin nhắn người dùng
        $userMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // bước 2: Soạn prompt 
        $products = Product::where('status', 'in_stock')
            ->with(['variants.color', 'variants.size', 'category'])
            ->get()
            ->map(function ($product) {
                $productInfo = "Sản phẩm: {$product->name}\n";
                $productInfo .= "Mô tả: " . ($product->description ?? 'Không có') . "\n";
                $productInfo .= "Giá: " . number_format($product->price ?? 0, 0, ',', '.') . " VNĐ\n";
                $productInfo .= "Danh mục: " . ($product->category->name ?? 'N/A') . "\n";

                // Thêm các biến thể (màu, size, kho)
                if ($product->variants && $product->variants->count() > 0) {
                    $productInfo .= "Các lựa chọn:\n";
                    foreach ($product->variants as $variant) {
                        $color = $variant->color->name ?? 'N/A';
                        $size = $variant->size->name ?? 'N/A';
                        $stock = $variant->stock ?? 0;
                        $variantPrice = number_format($variant->price ?? $product->price ?? 0, 0, ',', '.') . " VNĐ";

                        $stockStatus = $stock > 0 ? "còn {$stock} sản phẩm" : "hết hàng";
                        $productInfo .= "  - Màu {$color}, Size {$size}: {$variantPrice} ({$stockStatus})\n";
                    }
                } else {
                    $productInfo .= "Chưa có thông tin về màu sắc và kích cỡ\n";
                }

                return $productInfo;
            })->toArray();

        $productList = count($products) ? implode("\n---\n", $products) : "Hiện tại cửa hàng chưa có sản phẩm được public.";

        $prompt = "Bạn là trợ lý bán hàng AI cho shop quần áo.\n\n" .
            "SẢN PHẨM:\n{$productList}\n\n" .
            "QUY TẮC:\n" .
            "1. Trả lời DỰA TRÊN danh sách trên, không bịa đặt thông tin\n" .
            "2. Hỏi giá/màu/size/tồn kho → Trả lời CHÍNH XÁC từ data\n" .
            "3. Gợi ý sản phẩm phù hợp với nhu cầu khách\n" .
            "4. Ngắn gọn, thân thiện, tối đa 3-4 câu\n" .
            "5. Câu hỏi ngoài sản phẩm → Từ chối lịch sự, gợi ý hỏi về sản phẩm";

        // Lấy lịch sử chat gần nhất
        $history = ChatMessage::query()
            ->where(function ($q) use ($userId, $guestToken) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('guest_token', $guestToken);
                }
            })
            ->orderBy('created_at', 'desc')// sắp xếp lại tăng dần
            ->limit(10)
            ->get() 
            ->values();

        // Chuẩn bị tin theo format dsk
        $messages = [];

       
        $messages[] = [
            'role' => 'system',
            'content' => $prompt
        ];

        // Thêm lịch sử chat
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->message
            ];
        }

        // Thêm tin nhắn hiện tại
        $messages[] = [
            'role' => 'user',
            'content' => $request->message
        ];

        // bước 3: cal API
        $aiReplyText = "Xin lỗi, hiện tại AI chưa được cấu hình";
        $deepseekKey = env('DEEPSEEK_API_KEY');

        if (empty($deepseekKey)) {
            Log::warning('DEEPSEEK_API_KEY chưa được cấu hình trong .env');
            $aiReplyText = "Xin lỗi, trợ lý AI chưa được cấu hình. Vui lòng liên hệ quản trị để bật AI.";
        } else {
            try {
               
                $endpoint = 'https://api.deepseek.com/v1/chat/completions';

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $deepseekKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($endpoint, [
                    'model' => 'deepseek-chat',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

                
                if ($response->successful()) {
                    $data = $response->json();

                    
                    if (isset($data['choices'][0]['message']['content'])) {
                        $aiReplyText = $data['choices'][0]['message']['content'];
                    } elseif (isset($data['choices'][0]['text'])) {
                       
                        $aiReplyText = $data['choices'][0]['text'];
                    } else {
                        Log::warning('DeepSeek response structure unexpected: ' . json_encode($data));
                        $aiReplyText = "Xin lỗi, tôi chưa hiểu câu hỏi.";
                    }
                } else {
                    $status = $response->status();
                    $body = $response->body();
                    Log::error("DeepSeek API error HTTP {$status}: {$body}");

                    
                }
            } catch (\Throwable $e) {
                Log::error('DeepSeek API Exception: ' . $e->getMessage());
                $aiReplyText = "Đã có lỗi xảy ra khi kết nối tới AI.";
            }
        }

        // bước 4: Lưu tin nhắn 
        $botMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $aiReplyText,
        ]);

        // bước 5: Trả về tin nhắn gồm 2 actor
        return response()->json([
            'user' => $userMsg,
            'bot' => $botMsg,
        ]);
    }
}
