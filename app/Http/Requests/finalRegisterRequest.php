<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class finalRegisterRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(
                response()->json
                ([
                    'status' => 'error',
                    'message' => 'تأكد من صحة الايميل و رقم الهاتف المدخلين',
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => ['required', 'regex:/^[0][0-9]/', 'min:10', 'max:10'],
            'email' => ['sometimes', 'email:rfc,dns']
        ];
    }
}
