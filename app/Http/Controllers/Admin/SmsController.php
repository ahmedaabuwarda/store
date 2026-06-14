<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use App\Models\Customer;

class SmsController extends Controller
{
    // auth
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show (Request $request, $reciever) {
        $customer = Customer::where('id', $reciever)->first();
        $sms_body = "زبونان الكريم\nرصيد حسابك ".abs($customer->balance)." شيكل\nالرجاء تحويل المبلغ للاستمرار بخدمتكم\nاياد ماركت";
        $sms_balance = '0';
        $sms_check_balance_url = 'http://www.hi5sms.com/api.php?comm=chk_balance&user='.env('SMS_USERNAME').'&pass='.env('SMS_PASSWORD');
        try {

            $response = Http::acceptJson()->get($sms_check_balance_url);
            
            if ($response->json() > 0) {
                // Return the JSON data
                $sms_balance = $response->json();
                // return response()->json([
                //     'status' => 'success',
                //     'message' => $response->json()
                // ]);
            }

            // return response()->json([
            //     'status' => 'error',
            //     'error' => $response->body(),
            //     'message' => $response->body()
            // ], $response->status());

        } catch (\Exception $e) {
            // Handle connection errors, timeouts, etc.
            return response()->json([
                'status' => 'error',
                'message' => 'Request error: ' . $e->getMessage()
            ], 500);
        }
        if ($request->ajax()) {
            $data = view('admin.sms.send', compact('customer', 'sms_body', 'sms_balance'))->render();
            return response()->json(['data' => $data]);
        }
    }

    public function send (Request $request) {

        $responseInfo = ['-999' => '', '-100' => 'بعض الحقول مطلوبة', '-110' => 'خطا في اسم المستخدم او كلمة المرور', '-113' => 'لا يوجد رصيد كاف', '-115' => 'المرسل غير متوفر', '-116' => 'خطا في اسم المرسل'];
        $sms_user = env('SMS_USERNAME');
        $sms_password = env('SMS_PASSWORD');
        $sms_reciever = '972' . $request->reciever;
        $sms_body = $request->sms_body;
        $sms_sender = env('SMS_SENDER');
        $sms_url_service = 'https://hi5sms.com/api.php?comm=sendsms&user='.$sms_user.'&pass='.$sms_password.'&to='.$sms_reciever.'&message='.$sms_body.'&sender='.$sms_sender;
        // perform the get request
        try {

            $response = Http::acceptJson()->get($sms_url_service);
            
            if ($response->json() < 0) {
                // Return the JSON data
                // return response()->json([
                //     'status' => 'success',
                //     'message' => $responseInfo[$response->json()]
                // ]);
                return response()->json([
                    'status' => 'error',
                    'error' => $response->body(),
                    'message' => $responseInfo[$response->body()]
                ], $response->status());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'تم ارسال الرسالة بنجاح'
            ]);
            // return response()->json([
            //     'status' => 'error',
            //     'error' => $response->body(),
            //     'message' => $responseInfo[$response->body()]
            // ], $response->status());

        } catch (\Exception $e) {
            // Handle connection errors, timeouts, etc.
            return response()->json([
                'status' => 'error',
                'message' => 'Request error: ' . $e->getMessage()
            ], 500);
        }
    }
}
