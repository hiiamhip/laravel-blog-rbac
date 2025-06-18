<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CategoryNotEmptyRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        {
        $category = Category::find($value);

        if (!$category) {
            $fail('The selected category does not exist.');
            return;
        }

        if ($category->posts()->exists()) {
            $fail('The selected category contains posts and cannot be deleted.');
        }
    }
    }
}
