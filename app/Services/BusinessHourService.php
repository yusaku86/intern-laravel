<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Business_hour;
use App\Models\Hospital;

class BusinessHourService
{
    // 診療時間の追加
    public function addBusinessHour(Request $request, $daysOfWeek, $htmlId)
    {
        $startTime = $this->getTime($request, 'start', $htmlId);
        $endTime = $this->getTime($request, 'end', $htmlId);

        if ($startTime === 'default' || $endTime === 'default') {
            return;
        }

        Business_hour::create([
            'hospital_id' => $request->input('hospital'),
            'days_of_week' => $daysOfWeek,
            'start_time' => $this->getTime($request, 'start', $htmlId),
            'end_time' => $this->getTime($request, 'end', $htmlId),
        ]);
    }

    // 診療時間の削除
    public function deleteBusinessHour($businessHourId)
    {
        Business_hour::destroy($businessHourId);
    }

    // 診療時間の変更
    public function updateBusinessHour(Request $request, $businessHourId, $htmlId)
    {
        Business_hour::where('id', '=', $businessHourId)->update([
            'start_time' => $this->getTime($request, 'start', $htmlId),
            'end_time' => $this->getTime($request, 'end', $htmlId),
        ]);
    }

    // 定休日の設定
    public function setHoliday(Request $request, $hospitalId)
    {
        $daysOfWeek = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

        for ($i = 0; $i <= 6; $i++) {
            if ($request->input('is_open' . $i) === 'true') {
                Hospital::where('id', '=', $hospitalId)->update([
                    'is_open_' . $daysOfWeek[$i] => TRUE,
                ]);
            } elseif ($request->input('is_open' . $i) === 'false') {
                Hospital::where('id', '=', $hospitalId)->update([
                    'is_open_' . $daysOfWeek[$i] => FALSE,
                ]);
            }
        }
    }

    // 診療開始時間(終了時間)の値をhtmlから取得
    private function getTime(Request $request, $timeType, $htmlId)
    {
        // 新規追加したもので値を初期値(-)から変えていない場合は、defalutと返す
        if ($request->input($timeType . '_hour' . $htmlId) === 'default' || $request->input($timeType . '_minute' . $htmlId) === 'default') {
            return 'default';
        }
        return $request->input($timeType . '_hour' . $htmlId) . ':' . $request->input($timeType . '_minute' . $htmlId);
    }
}
