<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use App\Models\Customer;
use App\Models\SMS;

class SmsController extends Controller
{
  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  function index()
  {
    $page = config('app.page');
    $smses = SMS::paginate($page);
    return view('admin.sms.index', compact('smses'));
  }

  // store
  function store(Request $request)
  {
    $body = $request->body;
    $user_id = auth()->user()->id;
    $sms = new SMS();
    $sms->body = $body;
    $sms->user_id = $user_id;
    $sms->save();
    return response()->json(['status' => 'success', 'message' => 'تم حفظ القالب بنجاح']);
  }

  //edit
  public function edit(Request $request, $id)
  {
    $sms = SMS::where('id', $id)->first();
    return view('admin.sms.edit', compact('sms'));
  }

  //update
  public function update(Request $request)
  {
    $body = $request->sms_body;
    $id = $request->sms_body_id;
    $sms = SMS::where('id', $id)->first();
    $sms->body = $body;
    $sms->save();
    return redirect('/sms')->with('success', 'تم تحديث القالب بنجاح');
  }

  public function show(Request $request, $reciever)
  {
    $customer = Customer::where('id', $reciever)->first();
    $smses = SMS::get();

    $sms_check_balance_url = 'http://www.hi5sms.com/api.php?comm=chk_balance&user=' . env('SMS_USERNAME') . '&pass=' . env('SMS_PASSWORD');
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
      $data = view('admin.sms.send', compact('customer', 'smses', 'sms_balance'))->render();
      return response()->json(['data' => $data]);
    }
  }

  public function send(Request $request)
  {

    $responseInfo = [
      '-999' => '',
      '-100' => 'بعض الحقول مطلوبة',
      '-110' => 'خطا في اسم المستخدم او كلمة المرور',
      '-113' => 'لا يوجد رصيد كاف',
      '-115' => 'المرسل غير متوفر',
      '-116' => 'خطا في اسم المرسل'
    ];

    $sms_user = env('SMS_USERNAME');
    $sms_password = env('SMS_PASSWORD');
    $sms_sender = env('SMS_SENDER');

    $sms_reciever = '972' . $request->reciever;
    $sms_body_id = $request->sms_body_id;
    $sms_template_body = SMS::where('id', $sms_body_id)->first()->body;
    $select_all_customers = $request->select_all_customers;

    // if the select_all_customers is checked then send the sms to all customers which have phone number no 00000 (8 zeroes sometimes 10 zeroes) and the balance is less than 0
    if ($select_all_customers) {
      $customers = Customer::where('phone', '!=', '0000000000')->where('balance', '<', 0)->get();
      foreach ($customers as $customer) {
        $sms_reciever = '972' . $customer->phone;
        $sms_body = str_replace("<b>", abs($customer->balance), $sms_template_body);
        $sms_body = str_replace("<c>", abs($customer->balance), $sms_body);
        $sms_url_service = 'https://hi5sms.com/api.php?comm=sendsms&user=' . $sms_user . '&pass=' . $sms_password . '&to=' . $sms_reciever . '&message=' . $sms_body . '&sender=' . $sms_sender;
        try {

          $response = Http::acceptJson()->get($sms_url_service);

          if ($response->json() < 0) {
            // Return the JSON data
            return response()->json([
              'status' => 'error',
              'error' => $response->body(),
              'message' => $responseInfo[$response->body()]
            ], $response->status());
          }
        } catch (\Exception $e) {
          // Handle connection errors, timeouts, etc.
          return response()->json([
            'status' => 'error',
            'message' => 'Request error: ' . $e->getMessage()
          ], 500);
        }
      }
      return response()->json([
        'status' => 'success',
        'message' => 'تم ارسال الرسائل بنجاح'
      ]);
    }
    // single customer send
    $customer = Customer::where('id', '=', $request->customer_id)->first();
    $sms_body = str_replace("<b>", abs($customer->balance), $sms_template_body);
    $sms_body = str_replace("<c>", abs($customer->balance), $sms_body);
    $sms_url_service = 'https://hi5sms.com/api.php?comm=sendsms&user=' . $sms_user . '&pass=' . $sms_password . '&to=' . $sms_reciever . '&message=' . $sms_body . '&sender=' . $sms_sender;

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
