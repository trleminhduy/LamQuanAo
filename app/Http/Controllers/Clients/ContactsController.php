<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

use function Flasher\Toastr\Prime\toastr;

class ContactsController extends Controller
{
    public function index()
    {
        return view('clients.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // Xử lý lưu liên hệ hoặc gửi email ở đây
        Contact::create([
            'full_name' => $request->name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'is_reply' =>0,
        ]);

        toastr()->success('Gửi liên hệ thành công! Admin sẽ liên hệ sớm với bạn ');
        return redirect()->back();
    }
}
