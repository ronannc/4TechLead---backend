<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
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
`bootstrap/app.php`. Every resource route is a plain `Route::apiResource('things', ThingController::class)`.

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

There is currently no custom service in the codebase — `Team` and `Person` both use the generic defaults
for all four operations. Do not create a per-module service unless the logic genuinely diverges; that is
the whole point of this layer.

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
