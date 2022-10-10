<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use App\Models\Hospital;

class IndexController extends Controller
{
    public function __invoke($selectedId = null)
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
}
