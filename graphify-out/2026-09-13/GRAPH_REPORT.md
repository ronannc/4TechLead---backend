# Graph Report - backend  (2026-09-12)

## Corpus Check
- 346 files · ~82,887 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1318 nodes · 2932 edges · 127 communities (86 shown, 41 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 72 edges (avg confidence: 0.86)
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
- Illuminate\Database\Eloquent\Model
- User
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\Factory
- ExternalNotificationController.php
- devDependencies
- Illuminate\Validation\Rule
- DeliveryCase
- StoreServiceContract.php
- Generic CRUD Architecture
- PersonController.php
- IntegrationSystemController.php
- AuthenticatedPersonController.php
- TestCase
- Person
- PersonGrowthSuggestionController
- DeleteServiceContract.php
- DevelopmentPlan
- DailyMeeting
- Illuminate\Foundation\Http\FormRequest
- ClickUpWebhookIngestService
- ListParams
- GitHubWebhookIngestService
- AcceptPersonInvitationService
- ExternalNotification
- PersonExternalIdentity
- AcceptPersonInvitationRequest
- StoreDevelopmentPlanRequest
- Illuminate\Contracts\Validation\ValidationRule
- Illuminate\Database\Schema\Blueprint
- DeliveryMetricIngestService
- OneOnOneTemplate
- Illuminate\Support\Facades\DB
- AppServiceProvider
- IntegrationWebhookEvent
- Laravel Cloud Production Deployment
- Illuminate\Http\JsonResponse
- DailyMeetingController.php
- logging.php
- UpdateIntegrationSystemRequest
- DatabaseSeeder.php
- StorePersonRequest
- require-dev
- setup
- IndexDailyMeetingEntryRequest
- Illuminate\Support\Str
- config
- IntegrationWebhookEventManagementTest.php
- Illuminate\Database\Migrations\Migration
- require
- StorePersonOneOnOneNoteRequest
- psr-4
- IndexOneOnOneSessionRequest
- UpdateServiceContract.php
- IndexDailyMeetingRequest
- IndexTeamRequest
- laravel-boost
- IndexPersonOneOnOneNoteRequest
- UpdateOneOnOneSessionRequest
- StoreDailyMeetingRequest.php
- IndexDevelopmentPlanRequest
- LogoutService.php
- IndexExternalNotificationRequest
- post-create-project-cmd
- StorePersonExternalIdentityRequest.php
- Illuminate\Support\Facades\Schema
- IndexIntegrationSystemRequest
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- StoreOneOnOneTemplateRequest
- extra
- IndexPersonExternalIdentityRequest
- InvalidCredentialsException
- IntegrationSystem

## God Nodes (most connected - your core abstractions)
1. `User` - 158 edges
2. `Person` - 82 edges
3. `IntegrationSystem` - 70 edges
4. `IntegrationWebhookEvent` - 62 edges
5. `TenantRule` - 48 edges
6. `Controller` - 44 edges
7. `Team` - 39 edges
8. `DeliveryCase` - 33 edges
9. `DailyMeeting` - 30 edges
10. `PersonExternalIdentity` - 30 edges

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

## Communities (127 total, 41 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (18): TeamController, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, IntegrationSystemResource, OneOnOneSessionResource, OneOnOneTemplateResource, PersonDeliveryMetricResource (+10 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.11
Nodes (3): DailyMeetingEntry, DailyMeetingEntryPolicy, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 3 - "Controller"
Cohesion: 0.10
Nodes (12): DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, ExternalNotificationWebhookController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController, PersonExternalIdentityController (+4 more)

### Community 4 - "Team"
Cohesion: 0.15
Nodes (3): Team, TeamPolicy, tenantFixture()

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
Nodes (8): IndexDevelopmentPlanItemRequest, StoreDevelopmentPlanItemRequest, IndexIntegrationWebhookEventRequest, StoreOneOnOneSessionRequest, IndexPersonDeliveryMetricRequest, UpdatePersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 10 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.05
Nodes (22): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), DailyMeetingAnnotation, OneOnOneSession (+14 more)

### Community 11 - "User"
Cohesion: 0.10
Nodes (5): User, OneOnOneSessionPolicy, PersonDeliveryMetricPolicy, PersonOneOnOneNotePolicy, Illuminate\Foundation\Auth\User

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.25
Nodes (13): PersonOneOnOneNoteController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService(), resolveUpdateService() (+5 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.05
Nodes (18): Tenant, RegisterUserService, DailyMeetingAnnotationFactory, DailyMeetingFactory, DeliveryCaseFactory, DevelopmentPlanFactory, ExternalNotificationFactory, IntegrationSystemFactory (+10 more)

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Illuminate\Validation\Rule"
Cohesion: 0.11
Nodes (5): StoreExternalNotificationWebhookRequest, StoreIntegrationSystemRequest, IndexPersonRequest, UpdatePersonRequest, Illuminate\Validation\Rule

### Community 17 - "DeliveryCase"
Cohesion: 0.06
Nodes (20): fromClickUpStatus(), self, DeliveryCase, DeliveryCaseExternalLink, DeliveryCaseMilestone, DeliveryCaseParticipant, DeliveryCaseProjector, DeliveryStage (+12 more)

### Community 18 - "StoreServiceContract.php"
Cohesion: 0.23
Nodes (4): store(), GenericStoreService, OneOnOneSessionStoreService, PersonOneOnOneNoteStoreService

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.15
Nodes (13): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, Daily Meeting Custom Store Service, Filterable Query Convention (+5 more)

### Community 21 - "IntegrationSystemController.php"
Cohesion: 0.32
Nodes (3): IntegrationSystemController, IntegrationSystemStoreService, IntegrationSystemTokenService

### Community 22 - "AuthenticatedPersonController.php"
Cohesion: 0.20
Nodes (4): AuthenticatedPersonController, DevelopmentPlanItemResource, DevelopmentPlanResource, Symfony\Component\HttpKernel\Exception\NotFoundHttpException

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.09
Nodes (7): Attribute, Person, Attribute, PersonPolicy, PersonInvitationFactory, PersonOneOnOneNoteFactory, Illuminate\Database\Eloquent\Casts\Attribute

### Community 27 - "DevelopmentPlan"
Cohesion: 0.08
Nodes (5): DevelopmentPlan, DevelopmentPlanItem, DevelopmentPlanItemPolicy, DevelopmentPlanPolicy, DevelopmentPlanItemFactory

### Community 28 - "DailyMeeting"
Cohesion: 0.13
Nodes (4): DailyMeeting, DailyMeetingPolicy, DailyMeetingEntryFactory, static

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (8): LoginRequest, IndexOneOnOneTemplateRequest, UpdateOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, StorePersonInvitationRequest, StoreTeamRequest, UpdateTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "ListParams"
Cohesion: 0.20
Nodes (7): index(), ListParams, self, GenericIndexService, IntegrationWebhookEventIndexService, OneOnOneSessionIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator

### Community 35 - "PersonExternalIdentity"
Cohesion: 0.08
Nodes (7): PersonExternalIdentity, PersonExternalIdentityPolicy, PersonExternalIdentityStoreService, Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Testing\Fluent\AssertableJson, githubNativePullRequestPayload(), githubNativeReviewPayload()

### Community 38 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.12
Nodes (6): RegisterRequest, UpdateDevelopmentPlanRequest, UpdateDevelopmentPlanItemRequest, Carbon\CarbonImmutable, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Validator

### Community 42 - "OneOnOneTemplate"
Cohesion: 0.16
Nodes (3): OneOnOneTemplate, OneOnOneTemplatePolicy, OneOnOneSessionFactory

### Community 43 - "Illuminate\Support\Facades\DB"
Cohesion: 0.22
Nodes (9): Illuminate\Http\Client\ConnectionException, Illuminate\Support\Arr, Illuminate\Support\Carbon, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Http, Illuminate\Validation\ValidationException, Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException, Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException (+1 more)

### Community 45 - "IntegrationWebhookEvent"
Cohesion: 0.13
Nodes (4): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, Illuminate\Database\Eloquent\SoftDeletes, githubMetricEvent()

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "Illuminate\Http\JsonResponse"
Cohesion: 0.15
Nodes (6): AuthController, ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventController, IntegrationWebhookEventResource, Illuminate\Http\JsonResponse

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 53 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+2 more)

### Community 54 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 56 - "Illuminate\Support\Str"
Cohesion: 0.25
Nodes (3): Illuminate\Support\Facades\Hash, Illuminate\Support\Str, Pdo\Mysql

### Community 57 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 58 - "IntegrationWebhookEventManagementTest.php"
Cohesion: 0.25
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

### Community 124 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 129 - "InvalidCredentialsException"
Cohesion: 0.29
Nodes (3): InvalidCredentialsException, LoginService, Exception

### Community 130 - "IntegrationSystem"
Cohesion: 0.15
Nodes (3): IntegrationSystem, IntegrationSystemPolicy, ExternalNotificationIngestService

## Knowledge Gaps
- **80 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+75 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DailyMeetingEntry`, `IntegrationSystem`, `InvalidCredentialsException`, `Team`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\Factory`, `DeliveryCase`, `Person`, `DevelopmentPlan`, `DailyMeeting`, `AcceptPersonInvitationService`, `ExternalNotification`, `PersonExternalIdentity`, `OneOnOneTemplate`, `Illuminate\Support\Facades\DB`, `IntegrationWebhookEvent`, `DatabaseSeeder.php`, `Illuminate\Support\Str`, `IntegrationWebhookEventManagementTest.php`, `LogoutService.php`?**
  _High betweenness centrality (0.139) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `GitHubWebhookIngestService`, `DailyMeetingEntry`, `Controller`, `Team`, `PersonExternalIdentity`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Facades\DB`, `OneOnOneTemplate`, `Illuminate\Database\Eloquent\Factories\Factory`, `IntegrationWebhookEvent`, `Illuminate\Validation\Rule`, `DeliveryCase`, `PersonController.php`, `PersonGrowthSuggestionController`, `IntegrationWebhookEventManagementTest.php`, `DevelopmentPlan`, `DailyMeeting`, `ClickUpWebhookIngestService`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `GitHubWebhookIngestService`, `Controller`, `Team`, `PersonExternalIdentity`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Facades\DB`, `Illuminate\Database\Eloquent\Factories\Factory`, `IntegrationWebhookEvent`, `DeliveryCase`, `IntegrationSystemController.php`, `IntegrationWebhookEventManagementTest.php`, `ClickUpWebhookIngestService`?**
  _High betweenness centrality (0.082) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _80 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.08687943262411348 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.11067193675889328 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._