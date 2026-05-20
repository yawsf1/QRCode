<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RapportService;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function __construct(protected RapportService $rapports) {}

    public function exportEmploye(Request $request, User $user)
    {
        if (!$user->isEmploye() || $user->admin_id !== $request->user()->id) {
            abort(403);
        }

        $type = $request->validate([
            'type' => 'nullable|in:journalier,mensuel,annuel',
        ])['type'] ?? 'mensuel';

        return $this->rapports->exportEmployeCsv($user, $request->user(), $type);
    }
}
