<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'new_clock_in' => ['required', 'date_format:H:i', 'before:new_clock_out'],
        'new_clock_out' => ['required', 'date_format:H:i'],
        'new_break_in.*' => [
            'nullable',
            'date_format:H:i',
            'required_with:new_break_out.*',
            'after_or_equal:new_clock_in',
            'before_or_equal:new_clock_out',
        ],
        'new_break_out.*' => [
            'nullable',
            'date_format:H:i',
            'required_with:new_break_in.*',
            'before_or_equal:new_clock_out',
        ],
        'comment' => ['required', 'max:255'],
    ];
}
/**
 * 同じ文言が2回表示されないよう、出勤・退勤のエラーをまとめる
 */
public function withValidator($validator): void
{
    $validator->after(function ($validator) {
        $errors = $validator->errors();

        if ($errors->has('new_clock_in') && $errors->has('new_clock_out')) {
            $errors->forget('new_clock_out');
        }
    });
}
public function messages(): array
    {
        return [
            'new_clock_in.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'new_clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'new_clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            
            'new_clock_out.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'new_clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',

            'new_break_in.*.date_format' => '休憩時間が不適切な値です',
            'new_break_in.*.required_with' => '休憩時間が不適切な値です',
            'new_break_in.*.after_or_equal' => '休憩時間が不適切な値です',
            'new_break_in.*.before_or_equal' => '休憩時間が不適切な値です',
            'new_break_out.*.date_format' => '休憩時間が不適切な値です',
            'new_break_out.*.required_with' => '休憩時間が不適切な値です',

            'new_break_out.*.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',
            
            'comment.required' => '備考を記入してください',
            'comment.max' => '備考は255文字以内で入力してください',
        ];
    }
}
