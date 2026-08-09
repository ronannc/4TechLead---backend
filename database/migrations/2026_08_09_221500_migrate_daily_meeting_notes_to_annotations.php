<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('daily_meeting_entries', 'note_type') || ! Schema::hasColumn('daily_meeting_entries', 'note')) {
            return;
        }

        DB::table('daily_meeting_entries')
            ->select([
                'id',
                'daily_meeting_id',
                'person_id',
                'note_type',
                'note',
                'created_at',
                'updated_at',
            ])
            ->where(function ($query): void {
                $query->whereNotNull('note_type')
                    ->orWhereNotNull('note');
            })
            ->orderBy('id')
            ->chunkById(100, function ($entries): void {
                $rows = [];

                foreach ($entries as $entry) {
                    $rows[] = [
                        'daily_meeting_id' => $entry->daily_meeting_id,
                        'person_id' => $entry->person_id,
                        'type' => $entry->note_type === 'impedimento' ? 'bloqueio' : 'topico',
                        'text' => $entry->note ?? '',
                        'resolved' => false,
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at,
                    ];
                }

                if ($rows !== []) {
                    DB::table('daily_meeting_annotations')->insert($rows);
                }
            });

        Schema::table('daily_meeting_entries', function (Blueprint $table): void {
            $table->dropColumn(['note_type', 'note']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_meeting_entries', function (Blueprint $table): void {
            $table->string('note_type')->nullable()->after('actual_seconds');
            $table->text('note')->nullable()->after('note_type');
        });

        // Legacy entries supported only one note per person/meeting turn, so the rollback can only
        // restore the first person-scoped annotation for each entry and collapses all non-blockers.
        DB::table('daily_meeting_annotations')
            ->select([
                'id',
                'daily_meeting_id',
                'person_id',
                'type',
                'text',
            ])
            ->whereNotNull('person_id')
            ->orderBy('id')
            ->chunkById(100, function ($annotations): void {
                foreach ($annotations as $annotation) {
                    DB::table('daily_meeting_entries')
                        ->where('daily_meeting_id', $annotation->daily_meeting_id)
                        ->where('person_id', $annotation->person_id)
                        ->whereNull('note_type')
                        ->update([
                            'note_type' => $annotation->type === 'bloqueio' ? 'impedimento' : 'alinhamento',
                            'note' => $annotation->text,
                        ]);
                }
            });
    }
};
