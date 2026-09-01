# Graph Report - backend  (2026-08-31)

## Corpus Check
- 318 files · ~74,752 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1164 nodes · 2458 edges · 129 communities (81 shown, 48 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `cb5b0d22`
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
- ExternalNotification
- IntegrationWebhookEvent
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\HasFactory
- Illuminate\Http\JsonResponse
- devDependencies
- Illuminate\Contracts\Validation\ValidationRule
- Illuminate\Database\Eloquent\Factories\Factory
- User
- Generic CRUD Architecture
- IntegrationWebhookTest.php
- IntegrationSystem
- ListParams
- TestCase
- Person
- DevelopmentPlanItem
- PersonDeliveryMetric
- DevelopmentPlan
- StoreServiceContract.php
- Illuminate\Foundation\Http\FormRequest
- AcceptPersonInvitationRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- OneOnOneSession
- IndexIntegrationSystemRequest
- IndexPersonRequest
- IndexOneOnOneSessionRequest
- OneOnOneTemplate
- StorePersonInvitationRequest
- UpdateDevelopmentPlanRequest.php
- Illuminate\Database\Migrations\Migration
- Illuminate\Database\Schema\Blueprint
- Illuminate\Database\Eloquent\Model
- TenantIsolationTest.php
- StoreIntegrationWebhookRequest
- AppServiceProvider
- UpdateTeamRequest
- Laravel Cloud Production Deployment
- GitHubWebhookController.php
- Illuminate\Support\Str
- logging.php
- StoreDailyMeetingRequest
- DatabaseSeeder.php
- StorePersonRequest
- PersonInvitation
- LoginRequest
- IndexDailyMeetingEntryRequest
- IndexDevelopmentPlanRequest
- StoreDevelopmentPlanRequest
- IndexExternalNotificationRequest
- StoreOneOnOneSessionRequest
- UpdateOneOnOneSessionRequest
- StoreOneOnOneTemplateRequest
- Illuminate\Support\Facades\DB
- IntegrationSystemController.php
- IndexDailyMeetingRequest
- UpdatePersonExternalIdentityRequest
- laravel-boost
- IndexPersonOneOnOneNoteRequest
- IndexDevelopmentPlanItemRequest
- StorePersonOneOnOneNoteRequest
- require-dev
- Filterable.php
- LogoutService.php
- UpdatePersonRequest
- StorePersonExternalIdentityRequest
- Illuminate\Support\Facades\Schema
- Person.php
- setup
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- ExternalNotificationController.php
- config
- require
- psr-4
- UserFactory
- IntegrationSystemStoreService.php
- PersonExternalIdentity.php
- post-create-project-cmd
- .store
- ExternalNotificationIngestService
- extra

## God Nodes (most connected - your core abstractions)
1. `User` - 156 edges
2. `Person` - 67 edges
3. `IntegrationSystem` - 60 edges
4. `TenantRule` - 46 edges
5. `Controller` - 44 edges
6. `Team` - 35 edges
7. `IntegrationWebhookEvent` - 31 edges
8. `DailyMeeting` - 30 edges
9. `DailyMeetingEntry` - 29 edges
10. `DevelopmentPlan` - 26 edges

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

## Communities (129 total, 48 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (19): TeamController, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, OneOnOneSessionResource, OneOnOneTemplateResource (+11 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.15
Nodes (4): DailyMeetingEntry, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 3 - "Controller"
Cohesion: 0.09
Nodes (13): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanItemController, ExternalNotificationWebhookController, IntegrationWebhookController, OneOnOneSessionController, OneOnOneTemplateController, PersonExternalIdentityController (+5 more)

### Community 4 - "Team"
Cohesion: 0.06
Nodes (8): DailyMeeting, DailyMeetingAnnotation, Team, DailyMeetingPolicy, TeamPolicy, DailyMeetingAnnotationFactory, DailyMeetingEntryFactory, static

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
Cohesion: 0.11
Nodes (7): StoreDevelopmentPlanItemRequest, UpdateDevelopmentPlanItemRequest, IndexPersonDeliveryMetricRequest, IndexPersonExternalIdentityRequest, UpdatePersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 11 - "IntegrationWebhookEvent"
Cohesion: 0.07
Nodes (5): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, ClickUpWebhookIngestService, GitHubWebhookIngestService, IntegrationWebhookIngestService

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.20
Nodes (14): DevelopmentPlanController, PersonDeliveryMetricController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService() (+6 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\HasFactory"
Cohesion: 0.10
Nodes (7): DailyMeetingFactory, DevelopmentPlanItemFactory, OneOnOneTemplateFactory, PersonInvitationFactory, PersonOneOnOneNoteFactory, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Factories\HasFactory

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.15
Nodes (6): InvalidCredentialsException, AuthController, AuthenticatedPersonController, PersonController, Exception, Illuminate\Http\JsonResponse

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.24
Nodes (3): StoreExternalNotificationWebhookRequest, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 17 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.13
Nodes (6): DevelopmentPlanFactory, ExternalNotificationFactory, OneOnOneSessionFactory, PersonFactory, TeamFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 18 - "User"
Cohesion: 0.11
Nodes (8): PersonExternalIdentity, User, PersonExternalIdentityPolicy, LoginService, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.12
Nodes (16): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, External Actor Mapping, PR Metrics Webhook Payload (+8 more)

### Community 20 - "IntegrationWebhookTest.php"
Cohesion: 0.13
Nodes (8): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Testing\Fluent\AssertableJson, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum, githubNativePullRequestPayload(), githubNativeReviewPayload()

### Community 21 - "IntegrationSystem"
Cohesion: 0.08
Nodes (5): IntegrationSystem, IntegrationSystemPolicy, IntegrationSystemFactory, IntegrationWebhookEventFactory, Illuminate\Database\Eloquent\Relations\HasMany

### Community 22 - "ListParams"
Cohesion: 0.25
Nodes (6): index(), ListParams, GenericIndexService, OneOnOneSessionIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.15
Nodes (3): PersonGrowthSuggestionController, Person, PersonPolicy

### Community 28 - "StoreServiceContract.php"
Cohesion: 0.21
Nodes (4): store(), GenericStoreService, OneOnOneSessionStoreService, PersonOneOnOneNoteStoreService

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (8): RegisterRequest, StoreIntegrationSystemRequest, UpdateIntegrationSystemRequest, IndexOneOnOneTemplateRequest, UpdateOneOnOneTemplateRequest, IndexTeamRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): PersonOneOnOneNote, PersonOneOnOneNotePolicy, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 38 - "UpdateDevelopmentPlanRequest.php"
Cohesion: 0.29
Nodes (3): UpdateDevelopmentPlanRequest, Carbon\CarbonImmutable, Illuminate\Validation\Validator

### Community 41 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.33
Nodes (5): delete(), update(), GenericDeleteService, GenericUpdateService, Illuminate\Database\Eloquent\Model

### Community 42 - "TenantIsolationTest.php"
Cohesion: 0.28
Nodes (3): Tenant, TenantFactory, tenantFixture()

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "GitHubWebhookController.php"
Cohesion: 0.22
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

### Community 48 - "Illuminate\Support\Str"
Cohesion: 0.22
Nodes (3): PersonExternalIdentityStoreService, Illuminate\Support\Str, Pdo\Mysql

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 53 - "PersonInvitation"
Cohesion: 0.18
Nodes (5): PersonInvitation, AcceptPersonInvitationService, PersonInvitationCreateService, Illuminate\Support\Facades\Hash, Illuminate\Validation\ValidationException

### Community 68 - "Illuminate\Support\Facades\DB"
Cohesion: 0.28
Nodes (7): RegisterUserService, Illuminate\Support\Arr, Illuminate\Support\Carbon, Illuminate\Support\Facades\DB, Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException, Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException, Throwable

### Community 69 - "IntegrationSystemController.php"
Cohesion: 0.29
Nodes (4): IntegrationSystemController, IntegrationSystemResource, IntegrationSystemTokenService, Illuminate\Support\Facades\URL

### Community 76 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+2 more)

### Community 77 - "Filterable.php"
Cohesion: 0.24
Nodes (8): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), PersonDeliveryMetricFactory, Illuminate\Database\Eloquent\Builder

### Community 93 - "Person.php"
Cohesion: 0.25
Nodes (4): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\Relations\HasOne

### Community 95 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 117 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 118 - "require"
Cohesion: 0.33
Nodes (6): require, laravel/framework, laravel/sanctum, laravel/tinker, league/flysystem-aws-s3-v3, php

### Community 119 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 123 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 126 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **82 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+77 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DailyMeetingEntry`, `Team`, `ExternalNotification`, `IntegrationWebhookEvent`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `IntegrationWebhookTest.php`, `IntegrationSystem`, `Person`, `DevelopmentPlanItem`, `PersonDeliveryMetric`, `DevelopmentPlan`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OneOnOneSession`, `OneOnOneTemplate`, `TenantIsolationTest.php`, `DatabaseSeeder.php`, `PersonInvitation`, `Illuminate\Support\Facades\DB`, `LogoutService.php`?**
  _High betweenness centrality (0.141) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `DailyMeetingEntry`, `Controller`, `Team`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Http\JsonResponse`, `Illuminate\Contracts\Validation\ValidationRule`, `Illuminate\Database\Eloquent\Factories\Factory`, `IntegrationWebhookTest.php`, `IntegrationSystem`, `DevelopmentPlan`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Model`, `TenantIsolationTest.php`, `PersonInvitation`, `Illuminate\Support\Facades\DB`, `Filterable.php`, `Person.php`, `PersonExternalIdentity.php`, `.store`?**
  _High betweenness centrality (0.071) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `Controller`, `Illuminate\Support\Facades\DB`, `IntegrationSystemController.php`, `Illuminate\Database\Eloquent\Model`, `TenantIsolationTest.php`, `IntegrationWebhookEvent`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Filterable.php`, `GitHubWebhookController.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `IntegrationWebhookTest.php`, `IntegrationSystemStoreService.php`, `PersonExternalIdentity.php`, `ExternalNotificationIngestService`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _82 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.08313725490196078 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.14619883040935672 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._