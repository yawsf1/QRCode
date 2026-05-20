<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\AdminRegistrationFields;
use Illuminate\Foundation\Http\FormRequest;

class SendAdminRegistrationCodeRequest extends FormRequest
{
    use AdminRegistrationFields;

    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return $this->adminRegistrationFieldRules();
    }

    public function messages(): array
    {
        return $this->adminRegistrationFieldMessages();
    }
}
