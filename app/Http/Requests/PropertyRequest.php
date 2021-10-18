<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'     => 'required|max:255',
            'price'     => 'required',
            'purpose'   => 'required',
            'type'      => 'required',
            'bedroom'   => 'required',
            'bathroom'  => 'required',
            'city'      => 'required',
            'address'   => 'required',
            'area'      => 'required',
            'image'     => 'image|mimes:jpeg,jpg,png',
            'floor_plan'=> 'image|mimes:jpeg,jpg,png',
            'description'        => 'required',
        ];
    }
}
