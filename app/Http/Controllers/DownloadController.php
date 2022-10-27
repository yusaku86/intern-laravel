<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::all();
        return view('download')
            ->with('hospitals', $hospitals);
    }

    // 診療時間csvダウンロード
    public function downloadBusinessHourCsv($hospitalId)
    {
        $hospital = Hospital::find($hospitalId);

        return response()->streamDownload(
            function () use ($hospital) {
                // ストリーム作成
                $stream = fopen('php://output', 'w');
                // 文字コードをShift-JISに変換
                stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');
                // ヘッダー
                fputcsv($stream, [
                    '曜日',
                    '開始時間',
                    '終了時間',
                    '定休日',
                ]);
                $daysOfWeek = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                $daysOfWeekInJa = ['日', '月', '火', '水', '木', '金', '土'];

                foreach ($hospital->businessHours->sortBy('days_of_week') as $businessHour) {
                    $isOpen = 'is_open_' . $daysOfWeek[$businessHour->days_of_week];

                    $startTime = $hospital->$isOpen === 1 ? $businessHour->start_time : '-';
                    $endTime = $hospital->$isOpen === 1 ? $businessHour->end_time : '-';

                    fputcsv($stream, [
                        $daysOfWeekInJa[$businessHour->days_of_week],
                        $startTime,
                        $endTime,
                        $hospital->$isOpen === 1 ? '営業日' : '定休日',
                    ]);
                }
                fclose($stream);
            },
            $hospital->name . '_診療時間.csv',
            [
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }

    // 長期休暇csvダウンロード
    public function downloadVacationCsv($hospitalId)
    {
        $hospital = Hospital::find($hospitalId);

        return response()->streamDownload(
            function () use ($hospital) {
                // ストリーム作成
                $stream = fopen('php://output', 'w');
                // 文字コードをShift-JISに変換
                stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');
                // ヘッダー
                fputcsv($stream, [
                    '休暇開始日',
                    '休暇終了日',
                    '理由',
                ]);

                foreach ($hospital->vacations->sortBy('start_date') as $vacation) {
                    fputcsv($stream, [
                        $vacation->start_date,
                        $vacation->end_date,
                        $vacation->reason,
                    ]);
                }
                fclose($stream);
            },
            $hospital->name . '_長期休暇.csv',
            [
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }
}
