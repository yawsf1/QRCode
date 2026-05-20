<?php

namespace App\Services;

use App\Models\Presence;
use App\Models\Rapport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapportService
{
    public function exportEmployeCsv(User $employe, User $admin, string $type = 'mensuel'): StreamedResponse
    {
        [$start, $end] = $this->periodBounds($type);

        $rapport = Rapport::create([
            'date_debut' => $start,
            'date_fin' => $end,
            'type' => $type,
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
        ]);

        $presences = Presence::query()
            ->where('user_id', $employe->id)
            ->whereBetween('date_heure_scan', [
                $start->copy()->startOfDay(),
                $end->copy()->endOfDay(),
            ])
            ->orderBy('date_heure_scan')
            ->get();

        $summary = $this->buildSummary($presences);

        $filename = sprintf(
            'rapport-%s-%s-%s.csv',
            $type,
            Str::slug($employe->prenom . '-' . $employe->nom),
            $start->format('Y-m')
        );

        return response()->streamDownload(function () use ($employe, $presences, $start, $end, $type, $summary) {
            $handle = fopen('php://output', 'w');

            // Helps Excel / WPS split columns on ; and recognize UTF-8
            fwrite($handle, "\xEF\xBB\xBFsep=;\r\n");

            $this->writeRow($handle, ['Rapport de présence — QRCoded']);
            $this->writeRow($handle, []);
            $this->writeRow($handle, ['Employé', trim($employe->prenom . ' ' . $employe->nom)]);
            $this->writeRow($handle, ['Période', $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
            $this->writeRow($handle, ['Type', $this->typeLabel($type)]);
            $this->writeRow($handle, ['Généré le', Carbon::now()->format('d/m/Y H:i')]);
            $this->writeRow($handle, []);

            $this->writeRow($handle, ['Résumé']);
            $this->writeRow($handle, ['Total pointages', (string) $summary['total']]);
            $this->writeRow($handle, ['À l\'heure', (string) $summary['a_lheure']]);
            $this->writeRow($handle, ['En retard', (string) $summary['en_retard']]);
            $this->writeRow($handle, ['En avance', (string) $summary['en_avance']]);
            $this->writeRow($handle, ['Absent', (string) $summary['absent']]);
            $this->writeRow($handle, []);

            $this->writeRow($handle, ['Date', 'Heure', 'Statut', 'Écart (min)', 'IP']);

            if ($presences->isEmpty()) {
                $this->writeRow($handle, [
                    '—',
                    '—',
                    'Aucune présence enregistrée pour cette période',
                    '—',
                    '—',
                ]);
            } else {
                foreach ($presences as $presence) {
                    $scannedAt = Carbon::parse($presence->date_heure_scan);
                    $this->writeRow($handle, [
                        $scannedAt->format('d/m/Y'),
                        $scannedAt->format('H:i:s'),
                        $this->statutLabel($presence->statut),
                        (string) ($presence->ecart_minutes ?? 0),
                        $presence->adresse_ip ?? '',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{total: int, a_lheure: int, en_retard: int, en_avance: int, absent: int}
     */
    private function buildSummary($presences): array
    {
        return [
            'total' => $presences->count(),
            'a_lheure' => $presences->where('statut', 'a_lheure')->count(),
            'en_retard' => $presences->where('statut', 'en_retard')->count(),
            'en_avance' => $presences->where('statut', 'en_avance')->count(),
            'absent' => $presences->where('statut', 'absent')->count(),
        ];
    }

    private function writeRow($handle, array $row): void
    {
        fputcsv($handle, $row, ';');
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'journalier' => 'Journalier',
            'annuel' => 'Annuel',
            default => 'Mensuel',
        };
    }

    private function statutLabel(string $statut): string
    {
        return match ($statut) {
            'a_lheure' => 'À l\'heure',
            'en_retard' => 'En retard',
            'en_avance' => 'En avance',
            'absent' => 'Absent',
            default => $statut,
        };
    }

    private function periodBounds(string $type): array
    {
        $now = Carbon::now();

        return match ($type) {
            'journalier' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'annuel' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}
