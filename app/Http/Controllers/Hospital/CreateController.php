<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\CreateRequest;
use App\Models\Hospital;
use App\Models\Business_hour;

class CreateController extends Controller
{
    public function __invoke(CreateRequest $request)
    {
        $id = Hospital::create([
            'name' => $request->input('hospital_name'),
            'is_open_sun' => TRUE,
            'is_open_mon' => TRUE,
            'is_open_tue' => TRUE,
            'is_open_wed' => TRUE,
            'is_open_thu' => TRUE,
            'is_open_fri' => TRUE,
            'is_open_sat' => TRUE,
        ])->id;

        // 新しく登録した病院の診療時間を10:00～19:00で1週間分登録
        for ($i = 0; $i <= 6; $i++) {
            Business_hour::create([
                'hospital_id'  => $id,
                'days_of_week' => $i,
                'start_time'   => '10:00',
                'end_time'     => '19:00',
            ]);
        }

        return redirect()->route('hospital')->with('feedback_success', '登録が完了しました。');
    }
}
