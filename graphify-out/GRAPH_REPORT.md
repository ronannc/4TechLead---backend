# Graph Report - backend  (2026-09-13)

## Corpus Check
- 359 files · ~88,522 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1403 nodes · 3246 edges · 132 communities (90 shown, 42 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 75 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9a3c9215`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- GitHubDeliveryCaseProjector
- composer.json
- Controller
- Team
- scripts
- Database Performance Best Practices
- Laravel Best Practices Skill
- Laravel Best Practices
- TenantRule
- Illuminate\Database\Eloquent\Model
- PersonDeliveryMetric
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\Factory
- GitHubWebhookIngestService
- devDependencies
- Illuminate\Validation\Rule
- DeliveryStage.php
- IntegrationSystem
- Generic CRUD Architecture
- DailyMeetingEntry
- IntegrationWebhookEvent
- AuthenticatedPersonController.php
- TestCase
- Person
- DeliveryCase
- DeleteServiceContract.php
- User
- DailyMeeting
- Illuminate\Foundation\Http\FormRequest
- ClickUpWebhookIngestService
- OneOnOneSession
- DeliveryKpiSummaryService
- PersonInvitation
- ExternalNotification
- PersonExternalIdentity
- IndexIntegrationSystemRequest
- StoreDevelopmentPlanRequest
- UpdateDevelopmentPlanRequest.php
- Illuminate\Database\Migrations\Migration
- PersonOneOnOneNote
- OneOnOneTemplate
- Illuminate\Support\Facades\DB
- AppServiceProvider
- DeliveryCaseProjector
- Laravel Cloud Production Deployment
- AuthController.php
- DeliveryMetricIngestService
- logging.php
- Illuminate\Database\Eloquent\Relations\HasMany
- DatabaseSeeder.php
- Tenant
- require-dev
- setup
- PersonGrowthSuggestionController
- UpdatePersonRequest
- config
- UpdateDevelopmentPlanItemRequest
- Illuminate\Support\Facades\Schema
- require
- StorePersonOneOnOneNoteRequest
- psr-4
- DevelopmentPlanItem
- UpdateServiceContract.php
- IndexDailyMeetingRequest
- IntegrationWebhookTest.php
- laravel-boost
- StoreIntegrationSystemRequest
- UpdateOneOnOneSessionRequest
- StoreDailyMeetingRequest.php
- IndexDevelopmentPlanRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- IndexExternalNotificationRequest
- StorePersonExternalIdentityRequest.php
- post-create-project-cmd
- Illuminate\Contracts\Validation\ValidationRule
- UpdatePersonOneOnOneNoteRequest
- Illuminate\Database\Schema\Blueprint
- UpdateTeamRequest
- extra
- ExternalNotificationWebhookController.php
- StoreOneOnOneSessionRequest
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- IndexPersonDeliveryMetricRequest
- PersonInvitationController.php
- IndexPersonExternalIdentityRequest
- IntegrationWebhookEventResource

## God Nodes (most connected - your core abstractions)
1. `User` - 167 edges
2. `Person` - 88 edges
3. `IntegrationWebhookEvent` - 82 edges
4. `IntegrationSystem` - 77 edges
5. `DeliveryCase` - 60 edges
6. `TenantRule` - 48 edges
7. `Controller` - 46 edges
8. `Team` - 41 edges
9. `PersonExternalIdentity` - 33 edges
10. `DailyMeeting` - 30 edges

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

## Communities (132 total, 42 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (19): DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, ExternalNotificationResource, IntegrationSystemResource, OneOnOneSessionResource, OneOnOneTemplateResource (+11 more)

### Community 1 - "GitHubDeliveryCaseProjector"
Cohesion: 0.18
Nodes (4): GitHubDeliveryCaseProjector, DeliveryMilestoneType, DeliveryParticipantRole, CarbonImmutable

### Community 2 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 3 - "Controller"
Cohesion: 0.08
Nodes (15): DailyMeetingController, DailyMeetingEntryController, DeliveryKpiController, DevelopmentPlanController, DevelopmentPlanItemController, ExternalNotificationController, OneOnOneSessionController, OneOnOneTemplateController (+7 more)

### Community 4 - "Team"
Cohesion: 0.10
Nodes (5): Team, TeamPolicy, DailyMeetingFactory, PersonFactory, tenantFixture()

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
Nodes (7): IndexDailyMeetingEntryRequest, IndexDevelopmentPlanItemRequest, StoreDevelopmentPlanItemRequest, IndexOneOnOneSessionRequest, IndexPersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 10 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (9): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Factories\HasFactory (+1 more)

### Community 11 - "PersonDeliveryMetric"
Cohesion: 0.12
Nodes (6): ProjectDeliveryCasesCommand, ResetDeliveryCollectionCommand, PersonDeliveryMetric, Illuminate\Console\Attributes\Description, Illuminate\Console\Attributes\Signature, Illuminate\Console\Command

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.12
Nodes (18): IntegrationSystemController, PersonController, TeamController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService() (+10 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.08
Nodes (12): DevelopmentPlanFactory, DevelopmentPlanItemFactory, ExternalNotificationFactory, IntegrationSystemFactory, IntegrationWebhookEventFactory, OneOnOneTemplateFactory, PersonDeliveryMetricFactory, PersonExternalIdentityFactory (+4 more)

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Illuminate\Validation\Rule"
Cohesion: 0.17
Nodes (3): IndexPersonRequest, StorePersonRequest, Illuminate\Validation\Rule

### Community 17 - "DeliveryStage.php"
Cohesion: 0.14
Nodes (14): fromClickUpStatus(), self, DeliveryCaseMilestone, DeliveryCaseParticipant, Carbon\CarbonImmutable, Carbon\CarbonInterface, DeliveryCaseMilestoneFactory, DeliveryCaseParticipantFactory (+6 more)

### Community 18 - "IntegrationSystem"
Cohesion: 0.14
Nodes (3): IntegrationSystem, IntegrationSystemPolicy, ExternalNotificationIngestService

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.15
Nodes (13): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, Daily Meeting Custom Store Service, Filterable Query Convention (+5 more)

### Community 20 - "DailyMeetingEntry"
Cohesion: 0.11
Nodes (4): DailyMeetingEntry, Attribute, DailyMeetingEntryPolicy, Carbon\Carbon

### Community 21 - "IntegrationWebhookEvent"
Cohesion: 0.15
Nodes (4): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, Illuminate\Database\Eloquent\SoftDeletes, githubMetricEvent()

### Community 22 - "AuthenticatedPersonController.php"
Cohesion: 0.29
Nodes (3): AuthenticatedPersonController, DevelopmentPlanResource, Symfony\Component\HttpKernel\Exception\NotFoundHttpException

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.11
Nodes (6): Person, Attribute, PersonPolicy, PersonInvitationFactory, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\Relations\HasOne

### Community 25 - "DeliveryCase"
Cohesion: 0.11
Nodes (5): DeliveryCase, DeliveryCasePolicy, static, static, static

### Community 27 - "User"
Cohesion: 0.08
Nodes (8): DevelopmentPlan, User, DevelopmentPlanPolicy, PersonDeliveryMetricPolicy, PersonExternalIdentityPolicy, LogoutService, Illuminate\Foundation\Auth\User, Laravel\Sanctum\PersonalAccessToken

### Community 28 - "DailyMeeting"
Cohesion: 0.12
Nodes (5): DailyMeeting, DailyMeetingPolicy, DailyMeetingAnnotationFactory, DailyMeetingEntryFactory, static

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): IndexDeliveryKpiRequest, UpdateIntegrationSystemRequest, StoreOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, IndexTeamRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "OneOnOneSession"
Cohesion: 0.07
Nodes (12): index(), ListParams, self, IntegrationWebhookEventController, IndexIntegrationWebhookEventRequest, OneOnOneSession, OneOnOneSessionPolicy, GenericIndexService (+4 more)

### Community 32 - "DeliveryKpiSummaryService"
Cohesion: 0.25
Nodes (4): DeliveryKpiSummaryService, DeliveryMilestoneType, DeliveryAssociationConfidence, Illuminate\Support\Collection

### Community 33 - "PersonInvitation"
Cohesion: 0.11
Nodes (10): PersonInvitation, AcceptPersonInvitationService, PersonInvitationCreateService, static, UserFactory, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Notifications\Notifiable, Illuminate\Support\Facades\Hash (+2 more)

### Community 42 - "OneOnOneTemplate"
Cohesion: 0.15
Nodes (3): OneOnOneTemplate, OneOnOneTemplatePolicy, OneOnOneSessionFactory

### Community 43 - "Illuminate\Support\Facades\DB"
Cohesion: 0.11
Nodes (13): store(), GenericStoreService, IntegrationSystemStoreService, PersonOneOnOneNoteStoreService, Illuminate\Http\Client\ConnectionException, Illuminate\Support\Arr, Illuminate\Support\Carbon, Illuminate\Support\Facades\DB (+5 more)

### Community 45 - "DeliveryCaseProjector"
Cohesion: 0.24
Nodes (4): DeliveryCaseProjector, DeliveryParticipantRole, DeliveryStage, Illuminate\Database\Eloquent\Collection

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "AuthController.php"
Cohesion: 0.10
Nodes (7): InvalidCredentialsException, AuthController, AcceptPersonInvitationRequest, LoginRequest, RegisterRequest, LoginService, Exception

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 52 - "Tenant"
Cohesion: 0.28
Nodes (5): Tenant, RegisterUserService, DeliveryCaseFactory, kpiCase(), githubEvent()

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

### Community 70 - "IntegrationWebhookTest.php"
Cohesion: 0.12
Nodes (9): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Support\Facades\Http, Illuminate\Testing\Fluent\AssertableJson, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum, githubNativePullRequestPayload() (+1 more)

### Community 79 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): DailyMeetingAnnotation, DeliveryCaseExternalLink, DeliveryCaseExternalLinkFactory, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 82 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 83 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.14
Nodes (3): IndexOneOnOneTemplateRequest, UpdateOneOnOneTemplateRequest, Illuminate\Contracts\Validation\ValidationRule

### Community 93 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 129 - "IntegrationWebhookEventResource"
Cohesion: 0.24
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

## Knowledge Gaps
- **80 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+75 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Team`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\Factory`, `DeliveryStage.php`, `IntegrationSystem`, `DailyMeetingEntry`, `IntegrationWebhookEvent`, `Person`, `DeliveryCase`, `DailyMeeting`, `OneOnOneSession`, `PersonInvitation`, `ExternalNotification`, `PersonOneOnOneNote`, `OneOnOneTemplate`, `AuthController.php`, `DatabaseSeeder.php`, `Tenant`, `DevelopmentPlanItem`, `IntegrationWebhookTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `IntegrationWebhookEvent` connect `IntegrationWebhookEvent` to `GitHubDeliveryCaseProjector`, `IntegrationWebhookTest.php`, `Illuminate\Database\Eloquent\Model`, `PersonDeliveryMetric`, `Illuminate\Support\Facades\DB`, `DeliveryCaseProjector`, `GitHubWebhookIngestService`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `DeliveryMetricIngestService`, `DeliveryStage.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Factories\Factory`, `Tenant`, `ClickUpWebhookIngestService`, `OneOnOneSession`?**
  _High betweenness centrality (0.091) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `ExternalNotification`, `Team`, `IntegrationWebhookTest.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Facades\DB`, `CrudControllerTrait.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `GitHubWebhookIngestService`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PersonDeliveryMetric`, `DeliveryStage.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Tenant`, `IntegrationWebhookEvent`, `DeliveryCase`, `Illuminate\Foundation\Http\FormRequest`, `ClickUpWebhookIngestService`, `ExternalNotificationWebhookController.php`?**
  _High betweenness centrality (0.085) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _80 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.08326530612244898 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._
- **Should `Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.08232118758434548 - nodes in this community are weakly interconnected._