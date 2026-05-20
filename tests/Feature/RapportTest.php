<?php

namespace Tests\Feature;

use App\Models\Presence;
use App\Models\Rapport;
use App\Models\SessionQR;
use Carbon\Carbon;
use Tests\TestCase;

class RapportTest extends TestCase
{
    public function test_admin_can_export_employe_rapport_csv(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'rapport-token',
            'expires_at' => Carbon::now()->addDay(),
        ]);

        Presence::create([
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
            'statut' => 'a_lheure',
            'date_heure_scan' => Carbon::now(),
            'ecart_minutes' => 0,
            'session_id' => $session->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.employe.rapport', ['user' => $employe->id, 'type' => 'mensuel']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Rapport de présence', $csv);
        $this->assertStringContainsString('Résumé', $csv);
        $this->assertStringContainsString('À l\'heure', $csv);
        $this->assertStringContainsString('Total pointages', $csv);

        $this->assertDatabaseHas('rapports', [
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
            'type' => 'mensuel',
        ]);
    }

    public function test_rapport_csv_shows_message_when_no_presences_in_period(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $response = $this->actingAs($admin)
            ->get(route('admin.employe.rapport', ['user' => $employe->id, 'type' => 'mensuel']));

        $response->assertOk();
        $this->assertStringContainsString(
            'Aucune présence enregistrée pour cette période',
            $response->streamedContent()
        );
    }
}
