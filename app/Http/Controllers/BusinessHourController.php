<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessHourRequest;
use App\Services\BusinessHourService;
use App\Models\Hospital;

class BusinessHourController extends Controller
{
    // 診療時間一覧表示
    public function index($selectedId = null)
    {
        $hospitals = Hospital::orderBy('id', 'asc')->get();

        $selectedId = $selectedId ?? $hospitals[0]->id;
        $businessHours = Hospital::find($selectedId)->businessHours->sortBy('days_of_week');

        return view('business_hour')->with([
            'hospitals' => $hospitals,
            'businessHours' => $businessHours,
            'selectedId' => intval($selectedId),
        ]);
    }

    public function executeQuery(BusinessHourRequest $request, BusinessHourService $service)
    {
        // 削除された診療時間
        $isDeleted = FALSE;
        $deletedHours = explode(',', $request->input('deleted-list'));
        foreach ($deletedHours as $deletedHour) {
            if ($deletedHour !== '') {
                $service->deletebusinessHour($deletedHour);
                $isDeleted = TRUE;
            }
        }

        // 変更された診療時間
        $isChanged = FALSE;
        $changedHours = explode(',', $request->input('changed-list'));

        foreach ($changedHours as $changedHour) {
            if ($changedHour !== '') {
                // $changedHourは 「DB上のid_曜日番号-曜日内で何個目か」の形で値が格納されている
                // 例)DB上のidが2,日曜日で2つ目の診療時間 ⇒ 2_0-2
                $busiessHourId = intval(strstr($changedHour, '_', true));

                if (!in_array($busiessHourId, $deletedHours)) {
                    $htmlId = explode('_', $changedHour)[1];
                    $service->updateBusinessHour($request, $busiessHourId, $htmlId);
                    $isChanged = TRUE;
                }
            }
        }

        // 追加された診療時間
        $isAdded = FALSE;
        $addeHours = explode(',', $request->input('added-list'));
        foreach ($addeHours as $addedHour) {
            if ($addedHour !== '') {
                // $addedHour は、「曜日番号-曜日内で何個目か」の形で値が格納される
                // 例) 日曜日の2つ目 ⇒ 0-2
                $service->addBusinessHour($request, intval(substr($addedHour, 0, 1)), $addedHour);
                $isAdded = TRUE;
            }
        }

        // 定休日の設定
        $isChangedHoliday = $service->setHoliday($request, intval($request->input('hospital')));

        $hospitalId = intval($request->input('hospital'));

        if ($isDeleted || $isChanged || $isAdded || $isChangedHoliday) {
            return redirect()->route('business_hour.id', $hospitalId)
                ->with('feedback_success', '変更を保存しました。');
        } else {
            return redirect()->route('business_hour.id', $hospitalId);
        }
    }
}
