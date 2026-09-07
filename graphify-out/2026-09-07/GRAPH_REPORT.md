# Graph Report - backend  (2026-09-07)

## Corpus Check
- 318 files · ~74,517 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1181 nodes · 2490 edges · 119 communities (81 shown, 38 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 68 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d0ac2eea`
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
- PersonOneOnOneNote
- User
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\Factory
- AuthController.php
- devDependencies
- Person.php
- ExternalNotificationTest.php
- Illuminate\Database\Eloquent\Model
- Generic CRUD Architecture
- IntegrationWebhookTest.php
- IntegrationSystem
- UserFactory
- TestCase
- Person
- DevelopmentPlanItem
- PersonDeliveryMetric
- DevelopmentPlan
- DailyMeeting
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Contracts\Validation\ValidationRule
- IntegrationWebhookEvent
- ListParams
- GitHubWebhookIngestService
- ExternalNotification
- OneOnOneSession
- OneOnOneTemplate
- StoreDevelopmentPlanRequest
- UpdateDevelopmentPlanRequest.php
- Illuminate\Database\Schema\Blueprint
- ClickUpWebhookController.php
- TenantIsolationTest.php
- DevelopmentPlan.php
- AppServiceProvider
- UpdateDevelopmentPlanItemRequest
- Laravel Cloud Production Deployment
- Illuminate\Database\Eloquent\Relations\BelongsTo
- IndexIntegrationSystemRequest
- logging.php
- UpdateOneOnOneSessionRequest
- DatabaseSeeder.php
- StorePersonRequest
- require-dev
- setup
- StorePersonOneOnOneNoteRequest
- IndexDevelopmentPlanRequest
- config
- StoreTeamRequest
- StoreOneOnOneSessionRequest
- require
- psr-4
- Illuminate\Database\Migrations\Migration
- IntegrationWebhookEvent.php
- PersonDeliveryMetric.php
- IndexDailyMeetingRequest
- ExternalNotificationWebhookController.php
- laravel-boost
- IndexPersonOneOnOneNoteRequest
- PersonExternalIdentity.php
- AuthenticatedPersonController
- autoload-dev
- Filterable.php
- IndexPersonDeliveryMetricRequest
- post-create-project-cmd
- Illuminate\Support\Facades\Schema
- IndexPersonExternalIdentityRequest
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- ExternalNotificationController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 156 edges
2. `Person` - 73 edges
3. `IntegrationSystem` - 57 edges
4. `TenantRule` - 46 edges
5. `Controller` - 42 edges
6. `Team` - 35 edges
7. `IntegrationWebhookEvent` - 33 edges
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

## Communities (119 total, 38 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.07
Nodes (21): TeamController, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, IntegrationSystemResource, OneOnOneSessionResource (+13 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.14
Nodes (4): DailyMeetingEntry, Attribute, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

### Community 3 - "Controller"
Cohesion: 0.09
Nodes (13): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController, PersonExternalIdentityController (+5 more)

### Community 4 - "Team"
Cohesion: 0.13
Nodes (3): Team, TeamPolicy, Carbon\Carbon

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
Nodes (8): StoreDailyMeetingRequest, IndexDailyMeetingEntryRequest, IndexDevelopmentPlanItemRequest, StoreDevelopmentPlanItemRequest, IndexOneOnOneSessionRequest, UpdatePersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 11 - "User"
Cohesion: 0.13
Nodes (8): User, PersonExternalIdentityPolicy, LogoutService, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens, Laravel\Sanctum\PersonalAccessToken

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.20
Nodes (16): IntegrationSystemController, PersonController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService() (+8 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.08
Nodes (12): DailyMeetingFactory, DevelopmentPlanItemFactory, ExternalNotificationFactory, OneOnOneSessionFactory, OneOnOneTemplateFactory, PersonInvitationFactory, PersonOneOnOneNoteFactory, TeamFactory (+4 more)

### Community 14 - "AuthController.php"
Cohesion: 0.10
Nodes (7): InvalidCredentialsException, AuthController, AcceptPersonInvitationRequest, LoginRequest, RegisterRequest, LoginService, Exception

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Person.php"
Cohesion: 0.16
Nodes (4): IndexPersonRequest, UpdatePersonRequest, PersonFactory, Illuminate\Validation\Rule

### Community 17 - "ExternalNotificationTest.php"
Cohesion: 0.29
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

### Community 18 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.05
Nodes (26): delete(), store(), update(), PersonExternalIdentity, PersonInvitation, AcceptPersonInvitationService, DailyMeetingStoreService, GenericDeleteService (+18 more)

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.15
Nodes (13): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, Daily Meeting Custom Store Service, Filterable Query Convention (+5 more)

### Community 20 - "IntegrationWebhookTest.php"
Cohesion: 0.20
Nodes (4): Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Testing\Fluent\AssertableJson, githubNativePullRequestPayload(), githubNativeReviewPayload()

### Community 21 - "IntegrationSystem"
Cohesion: 0.09
Nodes (4): IntegrationSystem, IntegrationSystemPolicy, IntegrationSystemFactory, Illuminate\Database\Eloquent\Relations\HasMany

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.11
Nodes (6): PersonGrowthSuggestionController, Person, Attribute, PersonPolicy, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\Relations\HasOne

### Community 28 - "DailyMeeting"
Cohesion: 0.13
Nodes (4): DailyMeeting, DailyMeetingPolicy, DailyMeetingEntryFactory, static

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (8): UpdateIntegrationSystemRequest, IndexOneOnOneTemplateRequest, UpdateOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, StorePersonInvitationRequest, IndexTeamRequest, UpdateTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 30 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.15
Nodes (3): StoreIntegrationSystemRequest, StoreOneOnOneTemplateRequest, Illuminate\Contracts\Validation\ValidationRule

### Community 31 - "IntegrationWebhookEvent"
Cohesion: 0.10
Nodes (4): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, ClickUpWebhookIngestService, DeliveryMetricIngestService

### Community 32 - "ListParams"
Cohesion: 0.25
Nodes (6): index(), ListParams, GenericIndexService, OneOnOneSessionIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 38 - "UpdateDevelopmentPlanRequest.php"
Cohesion: 0.29
Nodes (3): UpdateDevelopmentPlanRequest, Carbon\CarbonImmutable, Illuminate\Validation\Validator

### Community 41 - "ClickUpWebhookController.php"
Cohesion: 0.24
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

### Community 42 - "TenantIsolationTest.php"
Cohesion: 0.24
Nodes (3): Tenant, RegisterUserService, tenantFixture()

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): DailyMeetingAnnotation, DailyMeetingAnnotationFactory, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 57 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 63 - "require"
Cohesion: 0.33
Nodes (6): require, laravel/framework, laravel/sanctum, laravel/tinker, league/flysystem-aws-s3-v3, php

### Community 65 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 71 - "ExternalNotificationWebhookController.php"
Cohesion: 0.31
Nodes (3): ExternalNotificationWebhookController, StoreExternalNotificationWebhookRequest, ExternalNotificationIngestService

### Community 76 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 77 - "Filterable.php"
Cohesion: 0.42
Nodes (7): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Builder

### Community 82 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 116 - "ExternalNotificationController.php"
Cohesion: 0.28
Nodes (3): ExternalNotificationController, IndexExternalNotificationRequest, ExternalNotificationResource

## Knowledge Gaps
- **80 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+75 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DailyMeetingEntry`, `Team`, `PersonOneOnOneNote`, `Illuminate\Database\Eloquent\Factories\Factory`, `AuthController.php`, `ExternalNotificationTest.php`, `Illuminate\Database\Eloquent\Model`, `IntegrationWebhookTest.php`, `IntegrationSystem`, `Person`, `DevelopmentPlanItem`, `PersonDeliveryMetric`, `DevelopmentPlan`, `DailyMeeting`, `IntegrationWebhookEvent`, `ExternalNotification`, `OneOnOneSession`, `OneOnOneTemplate`, `TenantIsolationTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `DatabaseSeeder.php`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `DailyMeetingEntry`, `Controller`, `Team`, `CrudControllerTrait.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `Person.php`, `Illuminate\Database\Eloquent\Model`, `IntegrationWebhookTest.php`, `IntegrationSystem`, `DevelopmentPlan`, `DailyMeeting`, `IntegrationWebhookEvent`, `GitHubWebhookIngestService`, `TenantIsolationTest.php`, `DevelopmentPlan.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `IntegrationWebhookEvent.php`, `PersonDeliveryMetric.php`, `PersonExternalIdentity.php`, `Filterable.php`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `GitHubWebhookIngestService`, `IntegrationWebhookEvent.php`, `PersonDeliveryMetric.php`, `ExternalNotificationWebhookController.php`, `PersonExternalIdentity.php`, `TenantIsolationTest.php`, `CrudControllerTrait.php`, `Filterable.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `ExternalNotificationTest.php`, `Illuminate\Database\Eloquent\Model`, `IntegrationWebhookTest.php`, `IntegrationWebhookEvent`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _80 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.07330827067669173 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.1368421052631579 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._