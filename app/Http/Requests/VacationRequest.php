<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class VacationRequest extends FormRequest
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
            'add_reason' => 'required | max:50',
            'add_start-date' => 'required',
            'add_end-date' => 'required',
        ];
    }

    // 長期休暇の終了日が開始日より前に来ていないか確認
    public function withValidator(Validator $validator)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->input('add_start-date'));
        $endDate = Carbon::createFromFormat('Y-m-d', $this->input('add_end-date'));

        $validator->after(function ($validator) use ($startDate, $endDate) {
            if ($startDate >= $endDate) {
                $validator->errors()->add('time_series', '長期休暇の終了日は開始日より後に設定してください。');
            }
        });
    }
}
