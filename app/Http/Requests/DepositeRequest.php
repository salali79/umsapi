<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositeRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(
                response()->json
                ([
                    'status' => 'error',
                    'message' => 'validation error',
                    'data' => $errors,
                    'action'=> ''
                ])
            );
        }

        parent::failedValidation($validator);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_id' =>'required',
            'study_year_id' =>'required',
            'semester_id' =>'required',
            'requested_hours' =>'required',
        ];
    }
}
