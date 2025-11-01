<?php

namespace App\Http\Resources\AdminTask;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllAdminTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $endTime = $this->timeLogs()->latest()->take(2)->get();

        $formattedEndTime = "";

        if(count($endTime) == 1){
            if($endTime[0]->status->value != 0){
                $formattedEndTime = Carbon::parse($endTime[0]->created_at)->format('d/m/Y H:i:s');
            }
        }else if(count($endTime) == 2){
            $formattedEndTime = Carbon::parse($endTime[0]->created_at)->format('d/m/Y H:i:s');
            if(($endTime[0]->status->value == 2 && $endTime[1]->status->value == 1) &&  $endTime[0]->total_time == $endTime[1]->total_time){
                $formattedEndTime = Carbon::parse($endTime[1]->created_at)->format('d/m/Y H:i:s');
            }
        }

        return [
            'taskId' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'number' => $this->number,
            'accountantName' => $this->user->full_name,
            'serviceCategoryName' => $this->serviceCategory->name,
            'totalHours' => $this->total_hours,
            //'costOfService' => $this->serviceCategory->getPrice(),
            //'costAfterDiscount' => $this->getTotalPriceAfterDiscountAttribute(),
            'createdAt' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'startDate' => $this->start_date?Carbon::parse($this->start_at)->format('d/m/Y'):"",
            'endDate' => $this->end_date?Carbon::parse($this->end_date)->format('d/m/Y'):"",
            "startTime"=>$this->timeLogs()->first()?Carbon::parse($this->timeLogs()->first()->created_at)->format('d/m/Y H:i:s') : "",
            //"endTime"=>$this->timeLogs()->latest()->first()?Carbon::parse($this->timeLogs()->latest()->first()->created_at)->format('d/m/Y H:i:s'):"",
            //"endTime"=>$this->closed_at?Carbon::parse(time: $this->closed_at)->format('d/m/Y H:i'):"",
            "endTime" => $formattedEndTime
        ];
    }
}
