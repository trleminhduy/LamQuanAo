<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderByDesc('created_at')->get();
        return view('admin.pages.contacts', compact('contacts'));
    }
    public function replyContact(Request $request)
    {
        $id = $request -> contact_id;
        $messageContent = $request -> message;
        $email = $request -> email;
        if(is_object($messageContent)){
            $messageContent = (string) $messageContent;
        }
         try {
            Mail::send('admin.emails.reply-contact', compact('messageContent'), function ($message) use ($email) {
                $message->to($email)
                    ->subject('Phản hồi liên hệ');
            });
            //Cập nhật trạng thái
            Contact::where('id', $id)->update(['is_reply' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Phản hồi thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gửi email thất bại: ' . $e->getMessage(),
            ]);
        }
        
    }
}
