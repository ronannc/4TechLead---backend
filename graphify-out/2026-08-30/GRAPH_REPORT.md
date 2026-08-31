# Graph Report - backend  (2026-08-27)

## Corpus Check
- 304 files · ~72,830 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1102 nodes · 2298 edges · 112 communities (69 shown, 43 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5e024c9f`
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
- Illuminate\Database\Eloquent\Factories\Factory
- Illuminate\Http\JsonResponse
- devDependencies
- Illuminate\Contracts\Validation\ValidationRule
- DevelopmentPlan
- PersonExternalIdentity
- Generic CRUD Architecture
- Person
- Illuminate\Database\Eloquent\Relations\HasMany
- DailyMeeting
- TestCase
- User
- DevelopmentPlanItem
- Illuminate\Database\Eloquent\Relations\BelongsTo
- IntegrationSystem
- ListParams
- Illuminate\Foundation\Http\FormRequest
- AcceptPersonInvitationRequest
- ClickUpWebhookController.php
- OneOnOneSession
- IndexIntegrationSystemRequest
- IndexPersonRequest
- IndexOneOnOneSessionRequest
- IndexPersonExternalIdentityRequest
- StorePersonInvitationRequest
- UpdateDevelopmentPlanRequest.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Schema\Blueprint
- StoreIntegrationSystemRequest
- StoreIntegrationWebhookRequest
- AppServiceProvider
- UpdateTeamRequest
- Laravel Cloud Production Deployment
- GitHubWebhookIngestService
- IndexTeamRequest
- logging.php
- StoreDailyMeetingRequest
- DatabaseSeeder.php
- StorePersonRequest
- Illuminate\Database\Eloquent\Factories\HasFactory
- Illuminate\Support\Facades\DB
- IndexDailyMeetingEntryRequest
- IndexDevelopmentPlanRequest
- StoreServiceContract.php
- UpdateDevelopmentPlanItemRequest
- UpdateIntegrationSystemRequest
- Illuminate\Database\Eloquent\Model
- Person.php
- PersonInvitation
- IntegrationSystemController.php
- IndexDailyMeetingRequest
- ClickUpWebhookIngestService
- laravel-boost
- IntegrationWebhookIngestService
- IndexDevelopmentPlanItemRequest
- StoreDevelopmentPlanItemRequest
- UpdateOneOnOneTemplateRequest
- Filterable.php
- ExternalNotificationController.php
- Illuminate\Support\Str
- IndexPersonDeliveryMetricRequest
- Illuminate\Database\Migrations\Migration
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices

## God Nodes (most connected - your core abstractions)
1. `User` - 146 edges
2. `Person` - 65 edges
3. `IntegrationSystem` - 55 edges
4. `Controller` - 42 edges
5. `TenantRule` - 40 edges
6. `Team` - 35 edges
7. `DailyMeeting` - 30 edges
8. `DailyMeetingEntry` - 29 edges
9. `IntegrationWebhookEvent` - 29 edges
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

## Communities (112 total, 43 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (17): DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, IntegrationSystemResource, OneOnOneSessionResource, OneOnOneTemplateResource (+9 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.12
Nodes (4): DailyMeetingEntry, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 3 - "Controller"
Cohesion: 0.09
Nodes (13): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, ExternalNotificationWebhookController, IntegrationWebhookController, OneOnOneSessionController, PersonDeliveryMetricController (+5 more)

### Community 4 - "Team"
Cohesion: 0.15
Nodes (4): Team, TeamPolicy, DailyMeetingEntryFactory, static

### Community 5 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

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
Nodes (8): StoreDevelopmentPlanRequest, IndexExternalNotificationRequest, StoreOneOnOneSessionRequest, UpdateOneOnOneSessionRequest, UpdatePersonRequest, StorePersonExternalIdentityRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.20
Nodes (14): OneOnOneTemplateController, TeamController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService() (+6 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.05
Nodes (15): OneOnOneTemplate, OneOnOneTemplatePolicy, DailyMeetingFactory, DevelopmentPlanFactory, ExternalNotificationFactory, IntegrationSystemFactory, IntegrationWebhookEventFactory, OneOnOneSessionFactory (+7 more)

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.12
Nodes (8): InvalidCredentialsException, AuthController, AuthenticatedPersonController, PersonController, LoginService, Exception, Illuminate\Http\JsonResponse, Symfony\Component\HttpKernel\Exception\NotFoundHttpException

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.24
Nodes (3): StoreExternalNotificationWebhookRequest, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 17 - "DevelopmentPlan"
Cohesion: 0.13
Nodes (3): DevelopmentPlan, DevelopmentPlanPolicy, DevelopmentPlanItemFactory

### Community 18 - "PersonExternalIdentity"
Cohesion: 0.07
Nodes (11): PersonExternalIdentity, PersonExternalIdentityPolicy, PersonExternalIdentityStoreService, Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Testing\Fluent\AssertableJson, Laravel\Sanctum\Http\Middleware\AuthenticateSession (+3 more)

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.12
Nodes (16): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, External Actor Mapping, PR Metrics Webhook Payload (+8 more)

### Community 20 - "Person"
Cohesion: 0.13
Nodes (4): PersonGrowthSuggestionController, Person, PersonInvitationFactory, tenantFixture()

### Community 21 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.13
Nodes (3): Tenant, RegisterUserService, Illuminate\Database\Eloquent\Relations\HasMany

### Community 22 - "DailyMeeting"
Cohesion: 0.11
Nodes (3): DailyMeeting, DailyMeetingPolicy, DailyMeetingAnnotationFactory

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "User"
Cohesion: 0.10
Nodes (6): User, PersonDeliveryMetricPolicy, PersonPolicy, LogoutService, Illuminate\Foundation\Auth\User, Laravel\Sanctum\PersonalAccessToken

### Community 26 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.17
Nodes (3): DailyMeetingAnnotation, PersonDeliveryMetric, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 27 - "IntegrationSystem"
Cohesion: 0.16
Nodes (3): IntegrationSystem, IntegrationSystemPolicy, ExternalNotificationIngestService

### Community 28 - "ListParams"
Cohesion: 0.27
Nodes (5): index(), ListParams, GenericIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): LoginRequest, RegisterRequest, IndexOneOnOneTemplateRequest, StoreOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "ClickUpWebhookController.php"
Cohesion: 0.22
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

### Community 38 - "UpdateDevelopmentPlanRequest.php"
Cohesion: 0.29
Nodes (3): UpdateDevelopmentPlanRequest, Carbon\CarbonImmutable, Illuminate\Validation\Validator

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 54 - "Illuminate\Support\Facades\DB"
Cohesion: 0.35
Nodes (6): Illuminate\Support\Arr, Illuminate\Support\Carbon, Illuminate\Support\Facades\DB, Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException, Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException, Throwable

### Community 57 - "StoreServiceContract.php"
Cohesion: 0.20
Nodes (4): store(), DailyMeetingStoreService, GenericStoreService, IntegrationSystemStoreService

### Community 63 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.33
Nodes (5): delete(), update(), GenericDeleteService, GenericUpdateService, Illuminate\Database\Eloquent\Model

### Community 65 - "Person.php"
Cohesion: 0.18
Nodes (4): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\Relations\HasOne

### Community 68 - "PersonInvitation"
Cohesion: 0.11
Nodes (10): PersonInvitation, AcceptPersonInvitationService, PersonInvitationCreateService, static, UserFactory, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Notifications\Notifiable, Illuminate\Support\Facades\Hash (+2 more)

### Community 77 - "Filterable.php"
Cohesion: 0.42
Nodes (7): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Builder

## Knowledge Gaps
- **82 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+77 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **43 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OneOnOneSession`, `DailyMeetingEntry`, `PersonInvitation`, `Team`, `ExternalNotification`, `IntegrationWebhookEvent`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Http\JsonResponse`, `DevelopmentPlan`, `PersonExternalIdentity`, `DatabaseSeeder.php`, `Person`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `DailyMeeting`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DevelopmentPlanItem`, `IntegrationSystem`?**
  _High betweenness centrality (0.159) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `Person.php`, `DailyMeetingEntry`, `Controller`, `PersonInvitation`, `Team`, `Filterable.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Contracts\Validation\ValidationRule`, `DevelopmentPlan`, `PersonExternalIdentity`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Facades\DB`, `User`, `DailyMeeting`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.080) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `Controller`, `IntegrationSystemController.php`, `ClickUpWebhookIngestService`, `IntegrationWebhookIngestService`, `Filterable.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `GitHubWebhookIngestService`, `PersonExternalIdentity`, `Person`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Facades\DB`, `StoreServiceContract.php`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _82 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.0927536231884058 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.12121212121212122 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.044444444444444446 - nodes in this community are weakly interconnected._