<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Vacation;
use App\Http\Requests\VacationRequest;

class VacationController extends Controller
{
    // 長期休暇一覧を表示
    public function index($selectedId = null)
    {
        $hospitals = Hospital::orderBy('id', 'asc')->get();

        /**
         *  selectedId ⇒ 選択された病院のid
         *  引数として渡されていなければ1番目の病院のidを格納する
         * */
        $selectedId = $selectedId ?? $hospitals[0]->id;
        $vacations = Hospital::find($selectedId)->vacations->sortBy('start_date');

        return view('vacation')->with([
            'hospitals'  => $hospitals,
            'vacations'  => $vacations,
            'selectedId' => intval($selectedId),
        ]);
    }

    // 長期休暇を追加

    public function create(VacationRequest $request)
    {
        Vacation::create([
            'hospital_id' => $request->input('hospital'),
            'start_date'  => $request->input('add_start-date'),
            'end_date'    => $request->input('add_end-date'),
            'reason'      => $request->input('add_reason'),
        ]);

        return redirect()->back();
    }

    // 長期休暇を削除
    public function delete($id)
    {
        Vacation::destroy($id);
        return redirect()->back();
    }
}
