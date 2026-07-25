<?php

namespace App\Models\Concerns;

use App\Enums\SortDirection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder filter(?array<string, mixed> $filters = null)
 * @method static Builder search(?string $term = null)
 * @method static Builder order(?array<string, string> $order = null)
 */
trait Filterable
{
    /**
     * Columns eligible for exact-match filtering via ?filters[key]=value.
     *
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return [];
    }

    /**
     * Columns searched (LIKE) via ?search=term.
     *
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return [];
    }

    /**
     * Columns allowed for ?order[field]=direction. Defaults to filterableFields() when empty.
     *
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>|null  $filters  defaults to the current request's `filters` input
     */
    public function scopeFilter(Builder $query, ?array $filters = null): Builder
    {
        $filters ??= (array) request()->input('filters', []);
        $allowed = $this->filterableFields();

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true) || $value === null || $value === '') {
                continue;
            }

            $query->where($field, $value);
        }

        return $query;
    }

    /**
     * @param  string|null  $term  defaults to the current request's `search` input
     */
    public function scopeSearch(Builder $query, ?string $term = null): Builder
    {
        $term ??= request()->input('search');
        $fields = $this->searchableFields();

        if ($term === null || $term === '' || $fields === []) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($fields, $term): void {
            foreach ($fields as $field) {
                $query->orWhere($field, 'like', "%{$term}%");
            }
        });
    }

    /**
     * @param  array<string, string>|null  $order  column => direction pairs, defaults to the current request's `order` input
     */
    public function scopeOrder(Builder $query, ?array $order = null): Builder
    {
        $order ??= (array) request()->input('order', []);
        $allowed = $this->sortableFields() ?: $this->filterableFields();
        $applied = false;

        foreach ($order as $field => $direction) {
            if (! in_array($field, $allowed, true)) {
                continue;
            }

            $direction = SortDirection::tryFrom((string) $direction) ?? SortDirection::Ascending;
            $query->orderBy($field, $direction->value);
            $applied = true;
        }

        return $applied ? $query : $query->latest();
    }
}
