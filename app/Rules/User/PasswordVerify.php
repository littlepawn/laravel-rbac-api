<?php

namespace App\Rules\User;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class PasswordVerify implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 检查旧密码是否与当前登录用户的密码一致
        if (!Hash::check($value, auth()->user()->password)) {
            $fail(trans('validation.user.old_password', ['attribute' => trans('attribute.user.old_password')]));
        }
    }

}
