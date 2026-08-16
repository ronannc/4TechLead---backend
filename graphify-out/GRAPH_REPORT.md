# Graph Report - backend  (2026-08-16)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 900 nodes · 1817 edges · 101 communities (61 shown, 40 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 8 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d0179c60`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Eloquent\Model
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Factories\Factory
- composer.json
- IntegrationWebhookEvent
- StoreServiceContract.php
- Controller
- Person
- DevelopmentPlan
- scripts
- DailyMeetingEntry
- Team
- DailyMeeting
- AuthController.php
- OneOnOneTemplate
- User
- devDependencies
- ContractType.php
- OneOnOneSession
- Illuminate\Contracts\Validation\ValidationRule
- DevelopmentPlanItem
- ExternalNotification
- TestCase
- CrudControllerTrait.php
- Illuminate\Foundation\Http\FormRequest
- InvalidCredentialsException
- Illuminate\Http\JsonResponse
- PersonController.php
- Illuminate\Database\Migrations\Migration
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Schema\Blueprint
- ExternalNotificationWebhookController.php
- IntegrationWebhookController.php
- AppServiceProvider
- logging.php
- DatabaseSeeder.php
- IntegrationWebhookTest.php
- IndexDailyMeetingRequest
- IndexDevelopmentPlanRequest
- StoreDevelopmentPlanRequest
- IndexDevelopmentPlanItemRequest
- IntegrationSystem
- IndexIntegrationSystemRequest
- StoreIntegrationSystemRequest
- UpdateIntegrationSystemRequest
- IndexOneOnOneSessionRequest
- StoreOneOnOneSessionRequest
- UpdateOneOnOneSessionRequest
- IndexOneOnOneTemplateRequest
- StoreOneOnOneTemplateRequest
- UpdateOneOnOneTemplateRequest
- IndexPersonDeliveryMetricRequest
- IndexPersonExternalIdentityRequest
- UpdatePersonExternalIdentityRequest
- DailyMeetingStoreService.php
- UpdateTeamRequest
- PersonExternalIdentity
- Filterable.php
- laravel-boost
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- PersonGrowthSuggestionController
- IntegrationWebhookIngestService.php
- DeleteServiceContract.php
- UpdateServiceContract.php
- StoreDevelopmentPlanItemRequest
- IndexTeamRequest
- Illuminate\Database\Eloquent\Casts\Attribute
- PersonExternalIdentityFactory.php

## God Nodes (most connected - your core abstractions)
1. `User` - 132 edges
2. `Person` - 52 edges
3. `IntegrationSystem` - 45 edges
4. `Controller` - 34 edges
5. `Team` - 32 edges
6. `DailyMeeting` - 29 edges
7. `DailyMeetingEntry` - 28 edges
8. `PersonExternalIdentity` - 24 edges
9. `IntegrationWebhookEvent` - 23 edges
10. `PersonDeliveryMetric` - 23 edges

## Surprising Connections (you probably didn't know these)
- `index()` --calls--> `ListParams`  [EXTRACTED]
  app/Http/Controllers/Concerns/CrudControllerTrait.php → app/DTOs/ListParams.php
- `AuthController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/AuthController.php → app/Http/Controllers/Controller.php
- `IntegrationSystemController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/IntegrationSystemController.php → app/Http/Controllers/Controller.php
- `TeamController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/TeamController.php → app/Http/Controllers/Controller.php
- `ExternalNotificationController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/ExternalNotificationController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (101 total, 40 thin omitted)

### Community 0 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.36
Nodes (4): Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 1 - "Illuminate\Http\Request"
Cohesion: 0.07
Nodes (22): index(), ListParams, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, ExternalNotificationResource (+14 more)

### Community 2 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.12
Nodes (8): ExternalNotificationFactory, IntegrationSystemFactory, IntegrationWebhookEventFactory, OneOnOneTemplateFactory, PersonDeliveryMetricFactory, PersonFactory, TeamFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 3 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 4 - "IntegrationWebhookEvent"
Cohesion: 0.07
Nodes (5): IntegrationWebhookEvent, PersonDeliveryMetric, IntegrationWebhookEventPolicy, PersonDeliveryMetricPolicy, IntegrationWebhookIngestService

### Community 5 - "StoreServiceContract.php"
Cohesion: 0.13
Nodes (8): store(), RegisterUserService, GenericStoreService, IntegrationSystemStoreService, Illuminate\Support\Facades\DB, Illuminate\Support\Str, Pdo\Mysql, Throwable

### Community 6 - "Controller"
Cohesion: 0.11
Nodes (11): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController, PersonExternalIdentityController (+3 more)

### Community 7 - "Person"
Cohesion: 0.13
Nodes (4): Person, Attribute, PersonPolicy, DevelopmentPlanFactory

### Community 8 - "DevelopmentPlan"
Cohesion: 0.09
Nodes (6): UpdateDevelopmentPlanRequest, DevelopmentPlan, DevelopmentPlanPolicy, Carbon\CarbonImmutable, DevelopmentPlanItemFactory, Illuminate\Validation\Validator

### Community 9 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 10 - "DailyMeetingEntry"
Cohesion: 0.12
Nodes (3): DailyMeetingEntry, DailyMeetingEntryPolicy, Carbon\Carbon

### Community 11 - "Team"
Cohesion: 0.14
Nodes (3): Team, TeamPolicy, DailyMeetingFactory

### Community 12 - "DailyMeeting"
Cohesion: 0.14
Nodes (4): DailyMeeting, DailyMeetingPolicy, DailyMeetingEntryFactory, static

### Community 13 - "AuthController.php"
Cohesion: 0.14
Nodes (5): AuthController, LoginRequest, RegisterRequest, LogoutService, Laravel\Sanctum\PersonalAccessToken

### Community 14 - "OneOnOneTemplate"
Cohesion: 0.15
Nodes (3): OneOnOneTemplate, OneOnOneTemplatePolicy, OneOnOneSessionFactory

### Community 15 - "User"
Cohesion: 0.17
Nodes (6): User, PersonExternalIdentityPolicy, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens

### Community 16 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 17 - "ContractType.php"
Cohesion: 0.16
Nodes (3): IndexPersonRequest, StorePersonRequest, UpdatePersonRequest

### Community 19 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.16
Nodes (4): StoreDailyMeetingRequest, StorePersonExternalIdentityRequest, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 22 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 23 - "CrudControllerTrait.php"
Cohesion: 0.33
Nodes (12): destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService(), resolveUpdateService(), resourceClass() (+4 more)

### Community 24 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): IndexDailyMeetingEntryRequest, UpdateDevelopmentPlanItemRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 25 - "InvalidCredentialsException"
Cohesion: 0.16
Nodes (6): InvalidCredentialsException, LoginService, static, UserFactory, Exception, Illuminate\Support\Facades\Hash

### Community 26 - "Illuminate\Http\JsonResponse"
Cohesion: 0.18
Nodes (6): ExternalNotificationController, IntegrationSystemController, TeamController, IndexExternalNotificationRequest, IntegrationSystemTokenService, Illuminate\Http\JsonResponse

### Community 31 - "ExternalNotificationWebhookController.php"
Cohesion: 0.31
Nodes (3): ExternalNotificationWebhookController, StoreExternalNotificationWebhookRequest, ExternalNotificationIngestService

### Community 34 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 35 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 36 - "IntegrationWebhookTest.php"
Cohesion: 0.22
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

### Community 41 - "IntegrationSystem"
Cohesion: 0.10
Nodes (3): IntegrationSystem, IntegrationSystemPolicy, Illuminate\Database\Eloquent\Relations\HasMany

### Community 54 - "DailyMeetingStoreService.php"
Cohesion: 0.21
Nodes (3): DailyMeetingAnnotation, DailyMeetingStoreService, DailyMeetingAnnotationFactory

### Community 57 - "Filterable.php"
Cohesion: 0.42
Nodes (7): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Builder

### Community 93 - "IntegrationWebhookIngestService.php"
Cohesion: 0.40
Nodes (4): Illuminate\Support\Arr, Illuminate\Support\Carbon, Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException, Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException

## Knowledge Gaps
- **63 isolated node(s):** `private`, `$schema`, `build`, `dev`, `type` (+58 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `DatabaseSeeder.php`, `IntegrationWebhookEvent`, `StoreServiceContract.php`, `IntegrationWebhookTest.php`, `Person`, `DevelopmentPlan`, `IntegrationSystem`, `DailyMeetingEntry`, `Team`, `DailyMeeting`, `AuthController.php`, `OneOnOneTemplate`, `OneOnOneSession`, `DevelopmentPlanItem`, `ExternalNotification`, `InvalidCredentialsException`?**
  _High betweenness centrality (0.158) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\Factory`, `PersonExternalIdentityFactory.php`, `IntegrationWebhookTest.php`, `DevelopmentPlan`, `IntegrationSystem`, `DailyMeetingEntry`, `Team`, `DailyMeeting`, `OneOnOneTemplate`, `ContractType.php`, `DailyMeetingStoreService.php`, `Filterable.php`, `PersonController.php`, `PersonGrowthSuggestionController`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\Factory`, `PersonExternalIdentityFactory.php`, `IntegrationWebhookEvent`, `StoreServiceContract.php`, `IntegrationWebhookTest.php`, `Filterable.php`, `Illuminate\Http\JsonResponse`, `IntegrationWebhookIngestService.php`, `ExternalNotificationWebhookController.php`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **What connects `private`, `$schema`, `build` to the rest of the system?**
  _63 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.06892655367231638 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Eloquent\Factories\Factory` be split into smaller, more focused modules?**
  _Cohesion score 0.12380952380952381 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.044444444444444446 - nodes in this community are weakly interconnected._