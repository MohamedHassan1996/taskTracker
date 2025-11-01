<?php

namespace App\Http\Controllers\Api\Private\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Parameter\ParameterValue;
use Illuminate\Http\Request;


class ClientPayInstallmentDividerController extends Controller
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
        $installmentNumbers = ParameterValue::where('id', $request->payStepsId)->pluck('description')->first();

        $client = Client::find($request->clientId);

        $clientEndDataAdd = 0;

        $clientEndDataAddMonth = 0;

        $installmentAmount = 0;

        $allowedDaysToPay = 0; // Fetch from the client table

        

        if ($client) {

            $client->price = $request->price;

            $client->payment_type_id = $request->paymentTypeId??null;

            $client->save();

            $clientEndDataAdd = ParameterValue::where('id', $client->payment_type_id)->first();

            $clientEndDataAddMonth = ceil($clientEndDataAdd->description / 30);

            $installmentAmount = $client->price / $installmentNumbers;

            $allowedDaysToPay = $client->allowed_days_to_pay ?? 0; // Fetch from the client table

        } else {


            $clientEndDataAdd = ParameterValue::where('id', $request->paymentTypeId)->first();

            $clientEndDataAddMonth = ceil($clientEndDataAdd->description / 30);

            $installmentAmount = $request->price / $installmentNumbers;


        }



        // $installmentsData = [];
        // $currentDate = now()->startOfYear();// First day of the current month
    
        // foreach ( range(1, $installmentNumbers) as $installmentNumber ) {

        //     $endDate = $currentDate->copy()->addMonths($clientEndDataAddMonth);

        //     $isSpecialMonthEnd = in_array($endDate->format('m-d'), ['08-31', '12-31']);

        //     if ($isSpecialMonthEnd) {
        //         $endDate->addDays(10);
        //     } else {
        //         $endDate->addDays($allowedDaysToPay);
        //     }
            

        //     $installmentsData[] = [
        //         'startAt' => $currentDate->format('Y-m-d'),
        //         'endAt' => $endDate->format('Y-m-d'),
        //         'parameterValueName' => '',
        //         'amount' => round($installmentAmount, 2),
        //         'paymentTypeId' => $client?->payment_type_id ?? $request->paymentTypeId ?? "",
        //         'payInstallmentSubData' => []
        //     ];

        //     $currentDate->addMonth(); // Move to the next month
        // }
        
$installmentsData = [];
$currentDate = now()->startOfYear(); // January 1st of current year

$monthsBetweenInstallments = floor(12 / $installmentNumbers); // Evenly space installments

foreach (range(1, $installmentNumbers) as $installmentNumber) {

    $startAt = $currentDate->copy()->startOfMonth();

    if ($clientEndDataAddMonth <= 1) {
        // End at end of the same month
        $endDate = $startAt->copy()->endOfMonth();
    } else {
        // End at the end of the month after adding X months
        $endDate = $startAt->copy()->addMonths($clientEndDataAddMonth - 1)->endOfMonth();
    }

    $isSpecialMonthEnd = in_array($endDate->format('m-d'), ['08-31', '12-31']);

    if ($isSpecialMonthEnd) {
        $endDate->addDays(10);
    } else {
        $endDate->addDays($allowedDaysToPay);
    }

    $installmentsData[] = [
        'startAt' => $startAt->format('Y-m-d'),
        'endAt' => $endDate->format('Y-m-d'),
        'parameterValueName' => '',
        'amount' => round($installmentAmount, 2),
        'paymentTypeId' => $client?->payment_type_id ?? $request->paymentTypeId ?? "",
        'payInstallmentSubData' => []
    ];

    $currentDate->addMonths($monthsBetweenInstallments);
}

        return response()->json([
            'data' => [
                'payInstallments' => $installmentsData
            ]
        ]);

    }


}
