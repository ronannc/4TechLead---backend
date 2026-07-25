<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class ListParams
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $order
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public array $filters = [],
        public array $order = [],
        public ?string $search = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            page: max(1, $request->integer('page', 1)),
            perPage: min(100, max(1, $request->integer('per_page', 15))),
            filters: (array) $request->input('filters', []),
            order: (array) $request->input('order', []),
            search: $request->string('search')->trim()->value() ?: null,
        );
    }
}
