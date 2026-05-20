<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendAdminRegistrationCodeRequest;
use App\Http\Requests\Auth\SendEmployeRegistrationCodeRequest;
use App\Services\EmailVerificationService;

class RegistrationVerificationController extends Controller
{
    public function __construct(protected EmailVerificationService $verification) {}

    public function sendAdminCode(SendAdminRegistrationCodeRequest $request)
    {
        $data = $request->validated();

        $this->verification->sendRegistrationCode($data['email'], $data['prenom']);

        return back();
    }

    public function sendEmployeCode(SendEmployeRegistrationCodeRequest $request)
    {
        $data = $request->validated();

        $this->verification->sendRegistrationCode($data['email'], $data['prenom']);

        return back();
    }
}
