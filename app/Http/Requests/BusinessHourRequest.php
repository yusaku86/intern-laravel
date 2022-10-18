<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;


class BusinessHourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'start_hour0-1' => 'required'
        ];
    }

    // 時間のバリデーション
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // 開始時間と終了時間の確認
            foreach ($this->all() as $key => $__) {
                // htmlエレメント名が start_hour0-0の形で、開始時間と終了時間のバリデートに引っかかったら
                if (
                    preg_match('/^start_hour\d-\d$/', $key) === 1
                    && !$this->validateStartAndEnd(str_replace('start_hour', '', $key))
                ) {
                    $validator->errors()->add('business_hour' . str_replace('start_hour', '', $key), '終了時間は開始時間より後に設定してください。');
                }
            }
            // 各曜日の診療時間が上から時系列に並んでいるか確認
            for ($i = 0; $i <= 6; $i++) {
                if (!$this->validateTimeSeries($i)) {
                    $validator->errors()->add('time_series' . $i, '各曜日の診療時間は上から時系列で設定してください。');
                }
            }
        });
    }

    /**
     * 診療時間の終了時間が開始時間より前に来ていないか確認
     */
    private function validateStartAndEnd($htmlId)
    {
        // 新規追加で時間が未入力のものと定休日設定がしてあるものは飛ばす
        if (
            $this->getTime('start', $htmlId) === 'default'
            || $this->getTime('end', $htmlId) === 'default'
            || $this->input('is_open' . explode('-', $htmlId)[0]) === '0'
            || $this->input('is_open' . explode('-', $htmlId)[0]) === 'false'
        ) {
            return TRUE;
        }

        $startDateTime = Carbon::createFromFormat('H:i', $this->getTime('start', $htmlId));
        $endDateTime = Carbon::createFromFormat('H:i', $this->getTime('end', $htmlId));

        if ($startDateTime >= $endDateTime) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 各曜日の診療時間が上から時間順で並んでいるか確認
     */
    private function validateTimeSeries($dayOfWeek)
    {
        $targetBusinessHours = [];
        $result = TRUE;

        // 定休日の場合は抜ける
        if ($this->input('is_open' . $dayOfWeek) === 'false' || $this->input('is_open' . $dayOfWeek) === '0') {
            return $result;
        }
        // 曜日の診療時間を配列に格納(日曜日の開始時間は比較に不要なため格納しない)
        // 各曜日の診療時間は5つまで設定できるため、1から5でループ(新規で時間が選択されてないものは飛ばす)
        for ($i = 1; $i <= 5; $i++) {
            if (
                $this->has('business_hour_id' . $dayOfWeek . '-' . $i)
                && $this->getTime('start', $dayOfWeek . '-' . $i) !== 'default'
                && $this->getTime('end', $dayOfWeek . '-' . $i) !== 'default'
            ) {
                if ($i !== 1) {
                    $targetBusinessHours[] = Carbon::createFromFormat('H:i', $this->getTime('start', $dayOfWeek . '-' . $i));
                }
                $targetBusinessHours[] = Carbon::createFromFormat('H:i', $this->getTime('end', $dayOfWeek . '-' . $i));
            }
        }

        /**
         * 診療時間が上から時系列で設定されているか確認
         * 診療終了時間と1つ下の診療開始時間を比較
         * 終了時間が1つ下の開始時間より遅い場合は時系列ではないためfalseを返す
         */
        for ($k = 0; $k < count($targetBusinessHours); $k = $k + 2) {
            if ($k < count($targetBusinessHours) - 1 && $targetBusinessHours[$k] >= $targetBusinessHours[$k + 1]) {
                $result = FALSE;
            }
        }
        return $result;
    }

    // 診療開始時間(終了時間)の値をhtmlから取得(分は2桁に0埋め)
    private function getTime($timeType, $htmlId)
    {
        // 新規追加したもので値を初期値(-)から変えていない場合は、defalutと返す
        if ($this->input($timeType . '_hour' . $htmlId) === 'default' || $this->input($timeType . '_minute' . $htmlId) === 'default') {
            return 'default';
        }
        return $this->input($timeType . '_hour' . $htmlId) . ':' . str_pad($this->input($timeType . '_minute' . $htmlId), 2, 0, STR_PAD_LEFT);
    }
}
