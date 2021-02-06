<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterCourseRequest extends FormRequest
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
            'course_id' => 'required',
            'category_id' => 'required',
            'group_id' => 'required'
        ];
    }
}
