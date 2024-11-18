<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsExcelFile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $extension = $value->getClientOriginalExtension();
        $mimeType = $value->getClientMimeType();

        if (!in_array($extension, ['xlsx', 'xls'])
            || !in_array($mimeType, [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ])) {
            $fail(__('validation.excel_file', ['attribute' => $attribute]));
        }
    }
}
