<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['sometimes','string','max:255'],
            'category_id' => ['sometimes','integer','exists:categories,id'],
            'cuisine_id'  => ['sometimes','integer','exists:cuisines,id'],
            'flavour_id'  => ['sometimes','integer','exists:flavours,id'],
            'description' => ['nullable','string'],
            'image'       => ['nullable','file','image','max:2048'],
        ];
    }
}
