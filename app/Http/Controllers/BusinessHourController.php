<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessHourRequest;
use App\Services\BusinessHourService;
use App\Models\Hospital;
use App\Models\Business_hour;

class BusinessHourController extends Controller
{
    // 診療時間一覧表示
    public function index($selectedId = null)
    {
        $hospitals = Hospital::orderBy('id', 'asc')->get();
        $selectedId = $selectedId ?? $hospitals[0]->id;
        $businessHours = Hospital::find($selectedId)->businessHours->sortBy('days_of_week')->toArray();

        return view('business_hour')->with([
            'hospitals' => $hospitals,
            'businessHours' => $businessHours,
            'selectedId' => intval($selectedId),
        ]);
    }

    // 診療時間を削除
    public function deleteBusinessHour($businessHourId,  BusinessHourRequest $request)
    {
        Business_hour::destroy($businessHourId);
        return redirect()->route('business_hour.id', $request->input('hospital'))->withInput();
    }

    public function executeQuery(BusinessHourRequest $request, BusinessHourService $service)
    {
        // 変更された診療時間
        $isChanged = FALSE;
        $changedHours = explode(',', $request->input('changed-list'));

        foreach ($changedHours as $changedHour) {
            if (
                $changedHour !== ''
                && $service->getTime($request, 'start', $changedHour) !== 'default'
                && $service->getTime($request, 'end', $changedHour) !== 'default'
            ) {
                // $changedHourは 「曜日番号-DB上でのid」の形で値が格納されている
                // 例)日曜日でDB上のidが200の診療時間 ⇒ 0-200
                $busiessHourId = (int) explode('-', $changedHour)[1];
                $service->updateBusinessHour($request, $busiessHourId, $changedHour);
                $isChanged = TRUE;
            }
        }

        // 追加された診療時間
        $isAdded = FALSE;
        $addedHours  = array_filter($request->all(), function ($value) {
            return preg_match('/^new_\d-\d$/', $value);
        });

        var_dump($addedHours);

        foreach ($addedHours as $addedHour) {
            // $addedHourには新規追加された診療時間が「new_曜日番号-曜日のなかで何番目に追加されたか」の形で格納されている
            // 例) 日曜日の中で2つめの新規登録 ⇒ new_0-2
            if ($addedHour !== '') {
                // $addedHourから 「曜日番号」-「曜日の中での番号」を切り取る
                $addedHour = str_replace('new_', '', $addedHour);
                // 新規追加で時間を入力していないものは飛ばす
                if ($service->getTime($request, 'start', $addedHour) === 'default' || $service->getTime($request, 'end', $addedHour) === 'default') continue;

                $service->addBusinessHour($request, (int)(substr($addedHour, 0, 1)), $addedHour);
                $isAdded = TRUE;
            }
        }

        // 定休日の設定
        $isChangedHoliday = $service->setHoliday($request, intval($request->input('hospital')));
        $hospitalId = intval($request->input('hospital'));

        if ($isChanged || $isAdded || $isChangedHoliday) {
            return redirect()->route('business_hour.id', $hospitalId)
                ->with('feedback_success', '変更を保存しました。');
        } else {
            return redirect()->route('business_hour.id', $hospitalId);
        }
    }
}
