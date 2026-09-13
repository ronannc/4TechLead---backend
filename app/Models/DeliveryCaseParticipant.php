<?php

namespace App\Models;

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryParticipantRole;
use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonInterface;
use Database\Factories\DeliveryCaseParticipantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'delivery_case_id',
    'person_id',
    'role',
    'source_provider',
    'source_ref',
    'confidence',
    'valid_from',
    'valid_to',
    'metadata',
])]
class DeliveryCaseParticipant extends Model
{
    /** @use HasFactory<DeliveryCaseParticipantFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return BelongsTo<DeliveryCase, $this>
     */
    public function deliveryCase(): BelongsTo
    {
        return $this->belongsTo(DeliveryCase::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @param  Builder<DeliveryCaseParticipant>  $query
     * @return Builder<DeliveryCaseParticipant>
     */
    public function scopeActiveAt(Builder $query, CarbonInterface $at): Builder
    {
        return $query
            ->where('valid_from', '<=', $at)
            ->where(function (Builder $query) use ($at): void {
                $query->whereNull('valid_to')->orWhere('valid_to', '>', $at);
            });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => DeliveryParticipantRole::class,
            'confidence' => DeliveryAssociationConfidence::class,
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
