<?php

namespace App\Http\Controllers\Api\Private\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Parameter\ParameterValue;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ClientPayInstallmentEndDateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('permission:all_client_pay_installments', ['only' => ['index']]);
        // $this->middleware('permission:create_client_pay_installment', ['only' => ['create']]);
        // $this->middleware('permission:edit_client_pay_installment', ['only' => ['edit']]);
        // $this->middleware('permission:update_client_pay_installment', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $client = Client::find($request->clientId);

        $startAt = Carbon::parse($request->startAt);

        $allowedDaysToPay = $client->allowed_days_to_pay ?? 0; // Fetch from the client table

        $installmentEndDataAdd = ParameterValue::where('id', $request->paymentTypeId)->first();

        $installmentEndDataAddMonth = ceil($installmentEndDataAdd->description / 30);

        $endDate = $startAt->copy()->addMonths($installmentEndDataAddMonth)->subDays(1);
        //$endDate = $startAt->copy()->startOfMonth()->addMonths($installmentEndDataAddMonth);
        

        $isSpecialMonthEnd = in_array($endDate->format('m-d'), ['08-31', '12-31']);

        if ($isSpecialMonthEnd) {
            $endDate->addDays(9);
        } else {
            $endDate->addDays($allowedDaysToPay > 1 ? $allowedDaysToPay - 1 : $allowedDaysToPay);
        }

        return response()->json([
            'endAt' => $endDate->format('Y-m-d'),
        ]);

    } 
    
// public function index(Request $request)
// {
//     $client = Client::find($request->clientId);

//     $startAt = Carbon::parse($request->startAt ?? now());
//     $allowedDaysToPay = $client->allowed_days_to_pay ?? 0;

//     $installmentEndDataAdd = ParameterValue::find($request->paymentTypeId);
//     $installmentEndDays = (int) $installmentEndDataAdd->description;

//     // Add exact days first
//     $tempDate = $startAt->copy()->addDays($installmentEndDays);
    

//     // ✅ If the result went past two months ahead (like March 2 for 60 days), take end of previous month
//     if ($tempDate->day > 1) {
//         $endDate = $tempDate->copy()->subDay()->endOfMonth();
//     } else {
//         $endDate = $tempDate->copy()->endOfMonth();
//     }

//     // Handle special months and allowed days
//     $isSpecialMonthEnd = in_array($endDate->format('m-d'), ['08-31', '12-31']);

//     if ($isSpecialMonthEnd) {
//         $endDate->addDays(9);
//     } else {
//         $endDate->addDays($allowedDaysToPay > 1 ? $allowedDaysToPay - 1 : $allowedDaysToPay);
//     }

//     return response()->json([
//         'endAt' => $endDate->format('Y-m-d'),
//     ]);
// }



}
