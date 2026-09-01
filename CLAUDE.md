<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

---

## CRUD architecture (project-specific, not Laravel Boost content)

This section documents the custom API architecture built on top of the stock Laravel scaffold. It is
maintained by hand (not regenerated by `boost:install`) — keep it in sync whenever the pattern changes.
`AGENTS.md` in this directory is kept byte-identical to this file; update both together.

### Flow: route → controller → service

The API is versioned and API-only: `routes/api.php`, mounted under `api/v1` via `apiPrefix` in
`bootstrap/app.php`. Every resource route is a `Route::apiResource('things', ThingController::class)` —
by default with all 5 verbs, but `->only([...])` restricts the set for append-only/read-only resources
(see `daily-meetings`/`daily-meeting-entries` below): the route restriction alone isn't the whole
story — the matching Policy also denies those abilities, so the "why" lives in code, not just routing.

- **Controller**: authorizes (Gate/Policy) and validates (Form Request) — no business logic. For standard
  CRUD, a controller is *only* a constructor (see below); it does not declare `index`/`store`/`show`/
  `update`/`destroy` itself.
- **Service**: one class per operation (store/update/delete/index), each implementing a single-method
  contract. Business logic that needs to diverge from the default lives here, never in the controller.

### Directory map

```
app/
  Contracts/Services/       StoreServiceContract, UpdateServiceContract, DeleteServiceContract, IndexServiceContract
  Services/                 Generic{Store,Update,Delete,Index}Service — the default implementations
  DTOs/ListParams.php        page/perPage/filters/order/search, built via ListParams::fromRequest()
  Enums/SortDirection.php    Ascending|Descending, used by the model's order() scope
  Models/Concerns/Filterable.php   filter()/search()/order() query scopes for any model
  Http/Controllers/Concerns/CrudControllerTrait.php   full apiResource CRUD for any controller
  Http/Controllers/Api/V1/   one thin controller per resource (e.g. TeamController, PersonController)
  Http/Requests/<Resource>/  Store/Update/Index Form Requests per resource
  Http/Resources/            one Eloquent API Resource per model
  Policies/                  one Policy per model (auto-discovered: App\Models\X → App\Policies\XPolicy)
```

### `CrudControllerTrait` — the standard controller

`App\Http\Controllers\Concerns\CrudControllerTrait` implements `index`/`store`/`show`/`update`/`destroy`
completely, including authorization (`$this->authorize(...)` against `$this->model` or the resolved
instance) and model resolution (`$this->model::query()->findOrFail($id)` — route params are plain
`int|string`, there is no implicit route-model-binding here). A standard CRUD controller only needs to set
properties in its constructor:

```php
final class TeamController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = Team::class;
        $this->resource = TeamResource::class;
        $this->storeRequest = StoreTeamRequest::class;
        $this->updateRequest = UpdateTeamRequest::class;
        $this->indexRequest = IndexTeamRequest::class;
    }
}
```

That's the entire controller — see `app/Http/Controllers/Api/V1/TeamController.php` and
`PersonController.php` for the real files. Nothing else needs to be written for a plain CRUD resource.

### Generic services vs. custom services

The trait lazily resolves each operation's service, defaulting to `App\Services\Generic{Store,Update,
Delete,Index}Service` parameterized by `$this->model`:

```php
protected function resolveStoreService(): StoreServiceContract
{
    return $this->storeService ??= app(GenericStoreService::class, ['model' => $this->model]);
}
```

When one operation for one resource needs to diverge from the default (extra side effects, derived fields,
etc.), write a class implementing the matching contract (it can compose a `Generic*Service` internally or
not) and assign it in the controller's constructor — only that one operation changes, the rest keep using
the generic default:

```php
public function __construct()
{
    $this->model = Team::class;
    // ...
    $this->storeService = app(TeamStoreService::class); // only this module's store diverges
}
```

`Team` and `Person` both use the generic defaults for all four operations. The one custom service in the
codebase is `App\Services\DailyMeetingStoreService` (see the "Daily" section below) — created only
because that store operation genuinely needs to persist a parent + several children in one transaction,
which `GenericStoreService`'s plain `create($attributes)` cannot do. Do not create a per-module service
unless the logic genuinely diverges that way; that is the whole point of this layer.

### `Filterable` model trait — query scopes & query string convention

Any model that wants to be listable through `GenericIndexService` (or ad hoc anywhere else) uses
`App\Models\Concerns\Filterable` and declares allow-lists:

```php
use Filterable;

protected function filterableFields(): array { return ['status', 'team_id']; }   // ?filters[key]=value
protected function searchableFields(): array { return ['name']; }               // ?search=term (LIKE, OR'd)
protected function sortableFields(): array   { return ['name', 'created_at']; } // ?order[key]=asc|desc
```

The allow-lists are the actual security boundary — arbitrary column names in the query string are silently
ignored if not listed. Scopes read straight from the current request when called without arguments, so
`Team::filter()->search()->order()->get()` works ad hoc, and `GenericIndexService` calls them the same way
with explicit args from `ListParams`:

```
GET /api/v1/teams?page=2&per_page=20&search=hello&order[name]=asc&order[created_at]=desc&filters[status]=published
```

`order[...]` accepts multiple columns; each becomes its own `orderBy()` in the order given. With no `order`
sent (and no default match), the scope falls back to `latest()`.

### Adding a new plain CRUD module — recipe

Using `Team`/`Person` (`app/Models/Team.php`, `app/Models/Person.php`, `app/Http/Controllers/Api/V1/
{Team,Person}Controller.php`) as the reference, a new resource `Thing` needs:

1. `php artisan make:model Thing -mf --no-interaction` (migration + factory).
2. Model: `#[Fillable([...])]`, `use HasFactory, Filterable;`, declare `filterableFields()`/
   `searchableFields()`/`sortableFields()`, plus any Eloquent relationships.
3. `make:request Thing/StoreThingRequest`, `Thing/UpdateThingRequest`, `Thing/IndexThingRequest`
   (`authorize()` returns `true` — real authorization is the Policy, not the Form Request; `IndexThingRequest`
   validates `page`/`per_page`/`search`/`order`/`order.*`/`filters.<allowed-key>`).
4. `make:resource ThingResource`.
5. `make:policy ThingPolicy --model=Thing` (auto-discovered, no manual registration).
6. `make:controller Api/V1/ThingController` — `use CrudControllerTrait;` + constructor wiring the 5
   properties (`model`, `resource`, `storeRequest`, `updateRequest`, `indexRequest`).
7. `Route::apiResource('things', ThingController::class);` in `routes/api.php`.
8. Only if a specific operation needs custom logic: a class in `app/Services/` implementing the matching
   contract, wired via the matching `$this->{store,update,delete,index}Service` property.

### Testing conventions for this architecture

- `tests/Feature/Api/V1/<Resource>CrudTest.php` — HTTP-level CRUD (index/show/store/update/destroy,
  including 404s and validation errors), one per resource.
- `tests/Unit/Models/<Resource>ModelTest.php` — `filter()`/`search()`/`order()` scopes and relationships in
  isolation, no HTTP.
- `tests/Unit/Policies/<Resource>PolicyTest.php` — each ability tested directly against the Policy class.
- `tests/Unit/Architecture/ArchitectureTest.php` — `arch()` tests locking in the pattern itself: controllers
  don't touch `DB`/`Builder` directly, controllers use `CrudControllerTrait`, `Generic*Service` classes
  implement their contract, `Team`/`Person` use `Filterable`. Extend this file (don't bypass it) when adding
  a resource that should be held to the same rules.
- `tests/Pest.php` binds **both** `Feature` and `Unit` to `Tests\TestCase` + `RefreshDatabase` (not just
  `Feature` as in the stock Pest scaffold) — Unit model/policy tests need a real (sqlite in-memory) database.

## `Person` — real fields, still 100% generic CRUD

`Person` started as a bare `{name, team_id}` model to prove out the CRUD architecture above; it has
since grown a real HR-ish field set (management/development data a tech lead tracks per team member),
without needing any custom Service, custom Controller logic, or a change to `CrudControllerTrait`
itself — this is the intended payoff of the generic layer.

Fields: `birth_date`, `position`, `contract_type`, `admission_date`, `seniority` (all required on
create), plus optional `email`/`phone`. `age` is **never stored** — it's an Eloquent accessor
(`Person::age()`, `Attribute::make(get: fn () => $this->birth_date?->age)`) computed from `birth_date`
on every read, so there's a single source of truth and it can't drift out of sync.

Two enums in `app/Enums/` (`ContractType`: Clt/Pj/Hourly/Cooperative — `'clt'|'pj'|'horista'|'cooperado'`;
`SeniorityLevel`: Intern/Junior/Mid/Senior/Specialist — `'estagiario'|'junior'|'pleno'|'senior'|'especialista'`)
are Eloquent-cast on the model (`casts()`: `contract_type` => `ContractType::class`, etc.) and validated
via `Rule::enum(...)` in the Form Requests — Eloquent automatically unwraps a `BackedEnum` to its scalar
value when the model is serialized, so `PersonResource` returns them as plain strings with no extra
handling. `filterableFields()` includes `contract_type`/`seniority` (so `?filters[contract_type]=pj`
works out of the box via `Filterable`), `searchableFields()` adds `position`.

`admission_date` must be `after:birth_date` in `StorePersonRequest` — deliberately **not** repeated in
`UpdatePersonRequest`, since a partial (`sometimes`) update could send `admission_date` alone without
`birth_date` in the same payload, and Laravel's `after:<field>` rule compares against a missing value in
that case rather than the already-persisted one.

## `Daily` — the project's first custom Service, append-only history

A `DailyMeeting` (`team_id`, `time_limit_seconds`, `started_at`, `ended_at`) belongs to a `Team` and
`hasMany` `DailyMeetingEntry` (one per person's speaking turn: `person_id`, `speaking_order`,
`allotted_seconds`, `actual_seconds`, `note_type`, `note`). Both models use `Filterable` like every
other resource, but neither is a plain CRUD module:

- **`POST /api/v1/daily-meetings` creates the meeting AND every entry in one request** — the Flutter
  client runs the whole timer session locally and only submits once, at the end. The nested `entries`
  array in the payload would be silently dropped by `#[Fillable]` if handled by `GenericStoreService`,
  so `DailyMeetingController` wires a custom `$this->storeService = app(DailyMeetingStoreService::class)`
  that wraps the meeting-create + entries-create loop in one `DB::transaction`, deriving `team_id` and
  `allotted_seconds` on each entry from the parent meeting and `speaking_order` from the array index
  (never trusted from the client payload).
- **Both resources are append-only/read-only by route**: `Route::apiResource(...)->only([...])` restricts
  `daily-meetings` to `index/show/store` and `daily-meeting-entries` to `index/show` — there is no
  update/destroy route for either. `DailyMeetingPolicy`/`DailyMeetingEntryPolicy` also hard-deny
  `update`/`delete`/`restore`/`forceDelete` (and `DailyMeetingEntryPolicy.create` too — an entry only ever
  comes into existence inside `DailyMeetingStoreService`'s transaction), so the "never edit history"
  intent is enforced at the authorization layer too, not just by omission from `routes/api.php`.
- **`DailyMeetingEntry::status` is a computed accessor** (`OnTime`/`Burned`/`SpokeTooLittle`), same
  pattern as `Person::age()` — never stored, derived from `actual_seconds` vs `allotted_seconds` (the
  threshold ratio is `DailyMeetingEntry::SPOKE_TOO_LITTLE_RATIO`, a public constant so the Flutter client
  can preview the same status locally for an unsaved draft turn before it's ever sent to the API).
- **No aggregation endpoint**: team/person stats (% on time, who burns most, etc.) are computed
  client-side from plain `GET /api/v1/daily-meeting-entries?filters[team_id]=`/`filters[person_id]=`
  listings, the same approach the frontend already uses for the Home "próximos aniversários" card —
  deliberately keeping the backend 100% generic CRUD instead of adding bespoke stats routes.
- `daily_meeting_entries.team_id` is **denormalized** from the parent meeting purely so
  `Filterable::scopeFilter` (a plain column `where`, no joins) can support `?filters[team_id]=` directly
  on the entries resource. Both `team_id` and `person_id` foreign keys use `restrictOnDelete()` (not
  `cascadeOnDelete()`) — this is historical data, so deleting a Team/Person must never silently erase
  past daily records.

## External integrations and webhooks

External systems are registered through authenticated CRUD at `integration-systems`; each create uses
`IntegrationSystemStoreService` to generate a one-time plaintext token, store `token_hash`, store the
webhook secret encrypted when provider-specific signatures need it, and return the token only in the
creation/regeneration response. Do not generate tokens or apply side effects directly in controllers.

Only provider-specific public ingest routes should exist for official sources: `POST /api/v1/github-webhooks`
and `POST /api/v1/clickup-webhooks`. Do not reintroduce a generic exposed integration webhook route; if a new
provider becomes official, add a dedicated controller/service pair with provider-specific authentication and
normalization.

Webhook ingestion belongs in the provider-specific ingest services. They must persist the raw event, normalize
the payload, update `last_received_at`, and generate `PersonDeliveryMetric` records in one transaction when an
active `person-external-identities` mapping exists. Shared KPI persistence/calculation belongs in
`DeliveryMetricIngestService`, not in controllers.
Idempotency is enforced by the database unique key on `(integration_system_id, event_id)` and code should use
concurrency-safe create-or-return behavior rather than a separate `first()` then `create()` sequence. Metrics
are keyed by `(integration_webhook_event_id, metric_type)` and should also be safe to replay.

External actor mapping goes through `person-external-identities`; the pair `(integration_system_id,
external_code)` is unique and must be validated in Form Requests before hitting the database. Public ingest
tests should cover success, invalid token/signature, inactive integration, alternate token header where still
supported, unmapped people, KPI creation for mapped people, and idempotent replay.

## Authentication (Sanctum bearer tokens)

Auth is `laravel/sanctum` (v4) using **personal access tokens** (`Authorization: Bearer <token>`), not
the SPA/cookie "stateful" mode — this app is consumed by a Flutter client (mobile + desktop), not a web
SPA sharing a cookie domain with the backend, so the token-guard mode is the correct fit.

- `config/auth.php` has a `sanctum` guard (`driver: sanctum, provider: users`); `app/Models/User.php` uses
  `Laravel\Sanctum\HasApiTokens`.
- `routes/api.php`: `POST /auth/register` and `POST /auth/login` are **public** (outside any auth
  middleware). `POST /auth/logout`, `GET /auth/me`, and all of `teams`/`people` sit behind
  `Route::middleware('auth:sanctum')`. An unauthenticated request to a protected route now gets a real
  **401** from the guard itself (not the 403-by-accident from Gate's guest-denial the Policies used to
  produce before Sanctum existed).
- `app/Http/Controllers/Api/V1/AuthController.php` — thin, delegates to one service per operation
  (`App\Services\Auth\{RegisterUserService,LoginService,LogoutService}`), same "one class per operation"
  convention as the CRUD services. `register()`/`login()` both return `{data: {id, name, email,
  created_at}, token}` — **register auto-authenticates** (returns a token immediately, same shape as
  login) so the client never needs a second login call after signing up.
- Failed login is a generic `InvalidCredentialsException` (`app/Exceptions/InvalidCredentialsException.php`,
  renders as 401 `{message: "These credentials do not match our records."}`) — never reveals whether the
  email or the password was wrong.
- `LogoutService` revokes only the *current* token (`$user->currentAccessToken()->delete()`), not all of
  the user's tokens.
- Tests: use `Sanctum::actingAs($user)`-equivalent — in this codebase, `$this->actingAs($user, 'sanctum')`
  (explicit guard name, since the routes now require the `sanctum` guard specifically, not the default
  `web` guard). See `tests/Feature/Api/V1/AuthTest.php` for the full auth test suite and
  `TeamCrudTest`/`PersonCrudTest` for the guard-name update applied to existing CRUD tests. Avoid chaining
  two authenticated HTTP calls against the *same* token within a single Pest test to assert revocation
  (e.g. "logout then call a protected route again") — Laravel's `Auth` guard caches the resolved user on
  the guard instance across sequential test-client calls within one test method, so a second call may
  incorrectly still appear authenticated even though the token was really deleted (confirmed via manual
  `curl` that revocation works correctly across real, separate HTTP requests). Assert the DB/token-count
  side effect directly instead.
