# Graph Report - backend  (2026-09-11)

## Corpus Check
- 324 files · ~76,995 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1212 nodes · 2587 edges · 132 communities (83 shown, 49 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3d5385f3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- DailyMeetingEntry
- composer.json
- Controller
- Team
- scripts
- Database Performance Best Practices
- Laravel Best Practices Skill
- Laravel Best Practices
- TenantRule
- Illuminate\Database\Eloquent\Factories\HasFactory
- User
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\Factory
- Illuminate\Http\JsonResponse
- devDependencies
- Person.php
- PersonOneOnOneNote
- Illuminate\Database\Eloquent\Model
- Generic CRUD Architecture
- Illuminate\Database\Eloquent\Casts\Attribute
- IntegrationSystemController.php
- AuthenticatedPersonController.php
- TestCase
- Person
- DevelopmentPlanItem
- IntegrationWebhookTest.php
- DevelopmentPlan
- DailyMeeting
- Illuminate\Foundation\Http\FormRequest
- IntegrationSystem
- ListParams
- GitHubWebhookIngestService
- PersonInvitation
- ExternalNotification
- PersonExternalIdentity
- LoginRequest
- StoreDevelopmentPlanRequest
- Illuminate\Contracts\Validation\ValidationRule
- Illuminate\Database\Schema\Blueprint
- IntegrationWebhookEvent
- OneOnOneTemplate
- Illuminate\Support\Facades\DB
- AppServiceProvider
- Filterable.php
- Laravel Cloud Production Deployment
- IntegrationWebhookEventResource
- DailyMeetingAnnotation
- logging.php
- TenantIsolationTest.php
- DatabaseSeeder.php
- StorePersonRequest
- require-dev
- setup
- IndexDailyMeetingEntryRequest
- PersonExternalIdentityStoreService.php
- config
- IntegrationWebhookEventManagementTest.php
- require
- psr-4
- IndexOneOnOneSessionRequest
- UpdateServiceContract.php
- IndexDailyMeetingRequest
- UpdatePersonRequest
- UpdateOneOnOneTemplateRequest
- laravel-boost
- IndexPersonOneOnOneNoteRequest
- UpdateOneOnOneSessionRequest
- StoreDailyMeetingRequest.php
- StorePersonInvitationRequest
- UpdatePersonOneOnOneNoteRequest
- IndexDevelopmentPlanRequest
- LogoutService.php
- IndexExternalNotificationRequest
- Illuminate\Database\Migrations\Migration
- post-create-project-cmd
- StorePersonExternalIdentityRequest.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Database\Eloquent\Relations\BelongsTo
- IndexIntegrationSystemRequest
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- StoreOneOnOneTemplateRequest
- IndexPersonDeliveryMetricRequest
- extra
- IndexPersonExternalIdentityRequest
- PersonOneOnOneNote.php
- LoginService.php
- ExternalNotificationIngestService

## God Nodes (most connected - your core abstractions)
1. `User` - 157 edges
2. `Person` - 74 edges
3. `IntegrationSystem` - 63 edges
4. `IntegrationWebhookEvent` - 48 edges
5. `TenantRule` - 48 edges
6. `Controller` - 44 edges
7. `Team` - 35 edges
8. `DailyMeeting` - 30 edges
9. `DailyMeetingEntry` - 29 edges
10. `PersonExternalIdentity` - 27 edges

## Surprising Connections (you probably didn't know these)
- `Generic CRUD Architecture` --semantically_similar_to--> `Generic CRUD Architecture`  [INFERRED] [semantically similar]
  AGENTS.md → CLAUDE.md
- `Integration Webhook Ingest Service` --semantically_similar_to--> `Integration Webhook Ingest Service`  [INFERRED] [semantically similar]
  AGENTS.md → CLAUDE.md
- `Pest Testing 4` --semantically_similar_to--> `Testing Best Practices`  [INFERRED] [semantically similar]
  .claude/skills/pest-testing/SKILL.md → .github/skills/laravel-best-practices/rules/testing.md
- `Architecture Testing` --conceptually_related_to--> `Generic CRUD Architecture`  [INFERRED]
  .github/skills/pest-testing/SKILL.md → AGENTS.md
- `Subagent Delegation` --conceptually_related_to--> `Testing Best Practices`  [INFERRED]
  .ai/skills/deploying-laravel-cloud/SKILL.md → .claude/skills/laravel-best-practices/rules/testing.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Laravel Best Practices Core Rule Set** — _claude_skills_laravel_best_practices_skill_laravelbestpractices, _claude_skills_laravel_best_practices_rules_architecture_architecturebestpractices, _claude_skills_laravel_best_practices_rules_routing_routingcontrollerbestpractices, _claude_skills_laravel_best_practices_rules_security_securitybestpractices, _claude_skills_laravel_best_practices_rules_testing_testingbestpractices [EXTRACTED 1.00]
- **Laravel Async Execution Rules** — backend__agents_skills_laravel_best_practices_rules_queue_jobs_queue_job_best_practices, backend__agents_skills_laravel_best_practices_rules_events_notifications_events_notifications_best_practices, backend__agents_skills_laravel_best_practices_rules_mail_mail_best_practices, backend__agents_skills_laravel_best_practices_rules_scheduling_task_scheduling_best_practices, backend__agents_skills_laravel_best_practices_rules_http_client_http_client_best_practices [INFERRED 0.75]
- **Laravel Request Lifecycle Rules** — backend__agents_skills_laravel_best_practices_rules_routing_routing_controllers_best_practices, backend__agents_skills_laravel_best_practices_rules_validation_validation_forms_best_practices, backend__agents_skills_laravel_best_practices_rules_security_security_best_practices, backend__agents_skills_laravel_best_practices_rules_error_handling_error_handling_best_practices [INFERRED 0.75]
- **Deployment Operations Flow** — _ai_skills_deploying_laravel_cloud_skill_laravelcloudcli, _ai_skills_deploying_laravel_cloud_skill_deploymentworkflow, _ai_skills_deploying_laravel_cloud_reference_checklists_environmentsetupchecklist, _ai_skills_deploying_laravel_cloud_reference_checklists_customdomainchecklist [INFERRED 0.85]
- **Laravel Async Operations** — _github_skills_laravel_best_practices_rules_events_notifications_events_notifications_best_practices, _github_skills_laravel_best_practices_rules_mail_mail_best_practices, _github_skills_laravel_best_practices_rules_queue_jobs_queue_job_best_practices, _github_skills_laravel_best_practices_rules_scheduling_task_scheduling_best_practices, _github_skills_laravel_best_practices_rules_http_client_http_client_best_practices [INFERRED 0.85]
- **Laravel Data Access Guidance** — _github_skills_laravel_best_practices_rules_db_performance_database_performance_best_practices, _github_skills_laravel_best_practices_rules_advanced_queries_advanced_query_patterns, _github_skills_laravel_best_practices_rules_eloquent_eloquent_best_practices, _github_skills_laravel_best_practices_rules_collections_collection_best_practices, _github_skills_laravel_best_practices_rules_caching_caching_best_practices [INFERRED 0.85]
- **Laravel Data Access Quality Rules** — backend__agents_skills_laravel_best_practices_rules_advanced_queries_advanced_query_patterns, backend__agents_skills_laravel_best_practices_rules_db_performance_database_performance_best_practices, backend__agents_skills_laravel_best_practices_rules_eloquent_eloquent_best_practices, backend__agents_skills_laravel_best_practices_rules_migrations_migration_best_practices, backend__agents_skills_laravel_best_practices_rules_collections_collection_best_practices [INFERRED 0.85]
- **Laravel Delivery Flow** — _github_skills_laravel_best_practices_rules_routing_routing_controllers_best_practices, _github_skills_laravel_best_practices_rules_architecture_architecture_best_practices, _github_skills_laravel_best_practices_rules_security_security_best_practices, _github_skills_laravel_best_practices_rules_error_handling_error_handling_best_practices [INFERRED 0.85]

## Communities (132 total, 49 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (17): DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, OneOnOneSessionResource, OneOnOneTemplateResource, PersonDeliveryMetricResource (+9 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.13
Nodes (4): DailyMeetingEntry, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 3 - "Controller"
Cohesion: 0.09
Nodes (13): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController, PersonExternalIdentityController (+5 more)

### Community 5 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 6 - "Database Performance Best Practices"
Cohesion: 0.10
Nodes (26): Custom Domain Checklist, Environment Setup Checklist, Deployment Workflow, Laravel Cloud CLI Skill, Subagent Delegation, Advanced Query Patterns, Architecture Best Practices, Blade View Patterns (+18 more)

### Community 7 - "Laravel Best Practices Skill"
Cohesion: 0.14
Nodes (23): Advanced Query Patterns, Architecture Best Practices, Blade & Views Best Practices, Caching Best Practices, Collection Best Practices, Configuration Best Practices, Database Performance Best Practices, Eloquent Best Practices (+15 more)

### Community 8 - "Laravel Best Practices"
Cohesion: 0.16
Nodes (21): Pest Testing 4, Advanced Query Patterns, Architecture Best Practices, Blade and Views Best Practices, Caching Best Practices, Collection Best Practices, Configuration Best Practices, Database Performance Best Practices (+13 more)

### Community 9 - "TenantRule"
Cohesion: 0.09
Nodes (8): IndexDevelopmentPlanItemRequest, StoreDevelopmentPlanItemRequest, UpdateDevelopmentPlanItemRequest, IndexIntegrationWebhookEventRequest, StoreOneOnOneSessionRequest, StorePersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 10 - "Illuminate\Database\Eloquent\Factories\HasFactory"
Cohesion: 0.10
Nodes (7): OneOnOneSession, DailyMeetingFactory, DevelopmentPlanItemFactory, IntegrationSystemFactory, TenantFactory, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Factories\HasFactory

### Community 11 - "User"
Cohesion: 0.07
Nodes (9): User, IntegrationSystemPolicy, IntegrationWebhookEventPolicy, OneOnOneSessionPolicy, PersonDeliveryMetricPolicy, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+1 more)

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.25
Nodes (13): TeamController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService(), resolveUpdateService() (+5 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.11
Nodes (7): ExternalNotificationFactory, IntegrationWebhookEventFactory, OneOnOneTemplateFactory, PersonExternalIdentityFactory, PersonInvitationFactory, TeamFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.11
Nodes (8): InvalidCredentialsException, AuthController, ExternalNotificationController, ExternalNotificationWebhookController, PersonController, ExternalNotificationResource, Exception, Illuminate\Http\JsonResponse

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Person.php"
Cohesion: 0.18
Nodes (4): StoreExternalNotificationWebhookRequest, IndexPersonRequest, PersonFactory, Illuminate\Validation\Rule

### Community 18 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.14
Nodes (9): delete(), store(), DailyMeetingStoreService, GenericDeleteService, GenericStoreService, IntegrationSystemStoreService, OneOnOneSessionStoreService, PersonOneOnOneNoteStoreService (+1 more)

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.15
Nodes (13): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, Daily Meeting Custom Store Service, Filterable Query Convention (+5 more)

### Community 20 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 21 - "IntegrationSystemController.php"
Cohesion: 0.29
Nodes (4): IntegrationSystemController, IntegrationSystemResource, IntegrationSystemTokenService, Illuminate\Support\Facades\URL

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.12
Nodes (5): PersonGrowthSuggestionController, Person, PersonPolicy, OneOnOneSessionFactory, Illuminate\Database\Eloquent\Relations\HasOne

### Community 26 - "IntegrationWebhookTest.php"
Cohesion: 0.18
Nodes (5): Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Support\Facades\Http, Illuminate\Testing\Fluent\AssertableJson, githubNativePullRequestPayload(), githubNativeReviewPayload()

### Community 28 - "DailyMeeting"
Cohesion: 0.13
Nodes (4): DailyMeeting, DailyMeetingPolicy, DailyMeetingEntryFactory, static

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (8): RegisterRequest, StoreIntegrationSystemRequest, IndexOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, IndexTeamRequest, StoreTeamRequest, UpdateTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "ListParams"
Cohesion: 0.17
Nodes (8): index(), ListParams, IntegrationWebhookEventController, GenericIndexService, IntegrationWebhookEventIndexService, OneOnOneSessionIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 33 - "PersonInvitation"
Cohesion: 0.20
Nodes (4): PersonInvitation, AcceptPersonInvitationService, PersonInvitationCreateService, Illuminate\Validation\ValidationException

### Community 35 - "PersonExternalIdentity"
Cohesion: 0.14
Nodes (3): PersonExternalIdentity, PersonExternalIdentityPolicy, PersonExternalIdentityStoreService

### Community 38 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.12
Nodes (6): AcceptPersonInvitationRequest, UpdateDevelopmentPlanRequest, UpdateIntegrationSystemRequest, Carbon\CarbonImmutable, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Validator

### Community 43 - "Illuminate\Support\Facades\DB"
Cohesion: 0.33
Nodes (6): Illuminate\Support\Arr, Illuminate\Support\Carbon, Illuminate\Support\Facades\DB, Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException, Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException, Throwable

### Community 45 - "Filterable.php"
Cohesion: 0.30
Nodes (8): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\SoftDeletes

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "IntegrationWebhookEventResource"
Cohesion: 0.22
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 50 - "TenantIsolationTest.php"
Cohesion: 0.16
Nodes (6): Tenant, RegisterUserService, static, UserFactory, Illuminate\Support\Facades\Hash, tenantFixture()

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 53 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+2 more)

### Community 54 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 57 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 58 - "IntegrationWebhookEventManagementTest.php"
Cohesion: 0.33
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

### Community 63 - "require"
Cohesion: 0.33
Nodes (6): require, laravel/framework, laravel/sanctum, laravel/tinker, league/flysystem-aws-s3-v3, php

### Community 65 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 82 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 93 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (3): PersonDeliveryMetric, PersonDeliveryMetricFactory, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 124 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **80 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+75 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DailyMeetingEntry`, `LoginService.php`, `Team`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Database\Eloquent\Factories\Factory`, `PersonOneOnOneNote`, `Person`, `DevelopmentPlanItem`, `IntegrationWebhookTest.php`, `DevelopmentPlan`, `DailyMeeting`, `PersonInvitation`, `ExternalNotification`, `PersonExternalIdentity`, `OneOnOneTemplate`, `Illuminate\Support\Facades\DB`, `TenantIsolationTest.php`, `DatabaseSeeder.php`, `IntegrationWebhookEventManagementTest.php`, `LogoutService.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PersonOneOnOneNote.php`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `DailyMeetingEntry`, `Controller`, `Team`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Http\JsonResponse`, `Person.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Casts\Attribute`, `IntegrationWebhookTest.php`, `DevelopmentPlan`, `DailyMeeting`, `IntegrationSystem`, `GitHubWebhookIngestService`, `PersonInvitation`, `Illuminate\Support\Facades\DB`, `Filterable.php`, `TenantIsolationTest.php`, `IntegrationWebhookEventManagementTest.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PersonOneOnOneNote.php`?**
  _High betweenness centrality (0.099) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `GitHubWebhookIngestService`, `ExternalNotificationIngestService`, `ExternalNotification`, `IntegrationWebhookTest.php`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `User`, `Illuminate\Support\Facades\DB`, `Filterable.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Database\Eloquent\Model`, `TenantIsolationTest.php`, `IntegrationSystemController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `IntegrationWebhookEventManagementTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.070) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _80 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.09065679925994449 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.12857142857142856 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._