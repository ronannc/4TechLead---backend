<?php

namespace App\Services;

use App\Models\DailyMeetingEntry;

class PersonDailyStatsSummaryService
{
    /**
     * @return array<string, float|int>
     */
    public function summarize(int $personId): array
    {
        $summary = DailyMeetingEntry::query()
            ->where('person_id', $personId)
            ->selectRaw('COUNT(*) as entry_count')
            ->selectRaw('COALESCE(AVG(actual_seconds), 0) as average_actual_seconds')
            ->selectRaw('SUM(CASE WHEN actual_seconds > allotted_seconds THEN 1 ELSE 0 END) as burned_count')
            ->selectRaw(
                'SUM(CASE WHEN actual_seconds < allotted_seconds * ? THEN 1 ELSE 0 END) as spoke_too_little_count',
                [DailyMeetingEntry::SPOKE_TOO_LITTLE_RATIO],
            )
            ->first();

        $entryCount = (int) ($summary?->entry_count ?? 0);
        $averageActualSeconds = round((float) ($summary?->average_actual_seconds ?? 0), 2);
        $burnedCount = (int) ($summary?->burned_count ?? 0);
        $spokeTooLittleCount = (int) ($summary?->spoke_too_little_count ?? 0);
        $onTimeCount = max(0, $entryCount - $burnedCount - $spokeTooLittleCount);

        if ($entryCount === 0) {
            return [
                'entry_count' => 0,
                'average_actual_seconds' => 0,
                'on_time_percentage' => 0,
                'burned_percentage' => 0,
                'spoke_too_little_percentage' => 0,
            ];
        }

        return [
            'entry_count' => $entryCount,
            'average_actual_seconds' => $averageActualSeconds,
            'on_time_percentage' => round(($onTimeCount / $entryCount) * 100, 2),
            'burned_percentage' => round(($burnedCount / $entryCount) * 100, 2),
            'spoke_too_little_percentage' => round(($spokeTooLittleCount / $entryCount) * 100, 2),
        ];
    }
}
