<?php

namespace App\Http\Requests;

use App\Rules\CategoryNotEmptyRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return array_merge($this->all(), ['category_id' => $this->route('category')->id]);
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
                new CategoryNotEmptyRule
            ],
        ];
    }
}
