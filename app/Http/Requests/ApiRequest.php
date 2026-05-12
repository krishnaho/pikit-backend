<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        try {
            $formData = json_decode(json_encode(request()->data, false));
            $vsdf = [];
            foreach ($formData as $key => $value) {
                $vsdf[$key] = $value;
            }
            request()->merge($vsdf);
        } catch (\Throwable $th) {
        }


        return [
            //
        ];
    }
}
