<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use App\Models\OneOnOneSession;
use App\Models\OneOnOneTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class OneOnOneSessionStoreService implements StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes): OneOnOneSession {
            $template = $this->template($attributes);

            if ($template instanceof OneOnOneTemplate) {
                $attributes['document_snapshot'] = [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'questions' => $template->questions,
                    'captured_at' => now()->toISOString(),
                ];
                $attributes['questions'] ??= $template->questions;
            }

            return OneOnOneSession::query()->create($attributes);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function template(array $attributes): ?OneOnOneTemplate
    {
        $templateId = $attributes['one_on_one_template_id'] ?? null;

        if ($templateId === null) {
            return null;
        }

        return OneOnOneTemplate::query()->find($templateId);
    }
}
