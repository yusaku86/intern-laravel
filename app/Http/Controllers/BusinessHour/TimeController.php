<?php

namespace App\Http\Controllers\BusinessHour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BusinessHour\TimeService;

class TimeController extends Controller
{
    public function executeQuery(Request $request, TimeService $service)
    {

        // 削除された診療時間
        $deletedHours = explode(',', $request->input('deleted-list'));
        foreach ($deletedHours as $deletedHour) {
            if ($deletedHour !== '') {
                $service->deletebusinessHour($deletedHour);
            }
        }

        // 変更された診療時間
        $changedHours = explode(',', $request->input('changed-list'));
        foreach ($changedHours as $changedHour) {
            if ($changedHour !== '') {
                // $changedHourは 「DB上のid_曜日番号-曜日内で何個目か」の形で値が格納されている
                // 例)DB上のidが2,日曜日で2つ目の診療時間 ⇒ 2_0-2
                $busiessHourId = intval(strstr($changedHour, '_', true));

                if (!in_array($busiessHourId, $deletedHours)) {
                    $htmlId = explode('_', $changedHour)[1];
                    $service->updateBusinessHour($request, $busiessHourId, $htmlId);
                }
            }
        }
        // 追加された診療時間
        $addeHours = explode(',', $request->input('added-list'));
        foreach ($addeHours as $addedHour) {
            if ($addedHour !== '') {
                // $addedHour は、「曜日番号-曜日内で何個目か」の形で値が格納される
                // 例) 日曜日の2つ目 ⇒ 0-2
                $service->addBusinessHour($request, intval(substr($addedHour, 0, 1)), $addedHour);
            }
        }

        // 定休日の設定
        $service->setHoliday($request, intval($request->input('hospital')));

        $hospitalId = intval($request->input('hospital'));
        return redirect()->route('business_hour.id', $hospitalId);
    }
}
