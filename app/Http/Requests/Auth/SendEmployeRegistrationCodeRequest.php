<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\EmployeRegistrationFields;
use Illuminate\Foundation\Http\FormRequest;

class SendEmployeRegistrationCodeRequest extends FormRequest
{
    use EmployeRegistrationFields;

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return $this->employeRegistrationFieldRules();
    }

    public function messages(): array
    {
        return $this->employeRegistrationFieldMessages();
    }
}
