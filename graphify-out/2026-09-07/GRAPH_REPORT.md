# Graph Report - backend  (2026-09-06)

## Corpus Check
- 315 files · ~72,298 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1151 nodes · 2415 edges · 117 communities (70 shown, 47 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `447c7076`
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
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuthenticatedPersonController.php
- CrudControllerTrait.php
- Illuminate\Database\Eloquent\Factories\Factory
- Illuminate\Http\JsonResponse
- devDependencies
- Illuminate\Contracts\Validation\ValidationRule
- Illuminate\Database\Eloquent\Relations\HasMany
- PersonExternalIdentity
- Generic CRUD Architecture
- ExternalNotification
- IntegrationSystem
- Person.php
- TestCase
- Person
- DevelopmentPlanItem
- PersonDeliveryMetric
- DevelopmentPlan
- DailyMeeting
- Illuminate\Foundation\Http\FormRequest
- AcceptPersonInvitationRequest
- User
- OneOnOneSession
- IntegrationWebhookEvent
- StoreDailyMeetingRequest
- User.php
- OneOnOneTemplate
- PersonInvitation
- UpdateDevelopmentPlanRequest.php
- Illuminate\Database\Migrations\Migration
- Illuminate\Database\Schema\Blueprint
- UpdatePersonRequest
- TenantIsolationTest.php
- DevelopmentPlanFactory.php
- AppServiceProvider
- UpdateTeamRequest
- Laravel Cloud Production Deployment
- Illuminate\Database\Eloquent\Model
- Illuminate\Database\Eloquent\Factories\HasFactory
- logging.php
- IntegrationWebhookEventFactory.php
- DatabaseSeeder.php
- StorePersonRequest
- PersonDeliveryMetricFactory.php
- PersonOneOnOneNoteFactory.php
- IndexDailyMeetingEntryRequest
- IndexDevelopmentPlanRequest
- StoreDevelopmentPlanRequest
- ExternalNotificationIngestService
- StoreOneOnOneSessionRequest
- StoreDevelopmentPlanItemRequest
- IntegrationSystemController.php
- IndexDailyMeetingRequest
- UpdatePersonExternalIdentityRequest
- laravel-boost
- IndexPersonOneOnOneNoteRequest
- IndexDevelopmentPlanItemRequest
- UpdateDevelopmentPlanItemRequest
- StoreIntegrationSystemRequest
- Filterable.php
- UpdateIntegrationSystemRequest
- UpdateOneOnOneTemplateRequest
- Illuminate\Support\Facades\Schema
- IndexPersonExternalIdentityRequest
- UpdatePersonOneOnOneNoteRequest
- console.php
- laravel-boost
- Application
- docker-entrypoint.sh
- Symfony\Component\HttpFoundation\BinaryFileResponse
- Validation and Form Requests Best Practices
- ExternalNotificationController.php
- IndexTeamRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 156 edges
2. `Person` - 67 edges
3. `IntegrationSystem` - 55 edges
4. `TenantRule` - 46 edges
5. `Controller` - 42 edges
6. `Team` - 35 edges
7. `DailyMeeting` - 30 edges
8. `DailyMeetingEntry` - 29 edges
9. `PersonExternalIdentity` - 27 edges
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

## Communities (117 total, 47 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.07
Nodes (20): ClickUpWebhookController, GitHubWebhookController, TeamController, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, IntegrationWebhookEventResource (+12 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.11
Nodes (4): DailyMeetingEntry, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 3 - "Controller"
Cohesion: 0.09
Nodes (13): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, ExternalNotificationWebhookController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController (+5 more)

### Community 4 - "Team"
Cohesion: 0.12
Nodes (5): Team, TeamPolicy, DailyMeetingEntryFactory, static, DailyMeetingFactory

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
Cohesion: 0.11
Nodes (7): IndexExternalNotificationRequest, IndexOneOnOneSessionRequest, UpdateOneOnOneSessionRequest, IndexPersonDeliveryMetricRequest, StorePersonOneOnOneNoteRequest, TenantRule, Illuminate\Validation\Rules\Exists

### Community 11 - "AuthenticatedPersonController.php"
Cohesion: 0.29
Nodes (3): AuthenticatedPersonController, DevelopmentPlanResource, Symfony\Component\HttpKernel\Exception\NotFoundHttpException

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.29
Nodes (13): destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService(), resolveUpdateService(), resourceClass() (+5 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.11
Nodes (9): DailyMeetingAnnotationFactory, DevelopmentPlanItemFactory, IntegrationSystemFactory, OneOnOneSessionFactory, OneOnOneTemplateFactory, PersonFactory, TeamFactory, TenantFactory (+1 more)

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.15
Nodes (6): InvalidCredentialsException, AuthController, PersonController, PersonInvitationController, Exception, Illuminate\Http\JsonResponse

### Community 15 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 16 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.19
Nodes (4): StoreExternalNotificationWebhookRequest, IndexPersonRequest, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 18 - "PersonExternalIdentity"
Cohesion: 0.05
Nodes (21): store(), PersonExternalIdentity, PersonExternalIdentityPolicy, AcceptPersonInvitationService, DailyMeetingStoreService, GenericStoreService, OneOnOneSessionStoreService, PersonExternalIdentityStoreService (+13 more)

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.12
Nodes (16): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, External Actor Mapping, PR Metrics Webhook Payload (+8 more)

### Community 20 - "ExternalNotification"
Cohesion: 0.08
Nodes (10): ExternalNotification, ExternalNotificationPolicy, Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Illuminate\Routing\Middleware\ThrottleRequests, Illuminate\Testing\Fluent\AssertableJson, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum (+2 more)

### Community 21 - "IntegrationSystem"
Cohesion: 0.11
Nodes (5): IntegrationSystem, IntegrationSystemPolicy, IntegrationSystemStoreService, ExternalNotificationFactory, PersonExternalIdentityFactory

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "Person"
Cohesion: 0.10
Nodes (5): PersonGrowthSuggestionController, Person, Attribute, PersonPolicy, Illuminate\Database\Eloquent\Relations\HasOne

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (8): LoginRequest, RegisterRequest, IndexIntegrationSystemRequest, IndexOneOnOneTemplateRequest, StoreOneOnOneTemplateRequest, StorePersonInvitationRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "User"
Cohesion: 0.12
Nodes (6): User, PersonOneOnOneNotePolicy, LoginService, LogoutService, Illuminate\Foundation\Auth\User, Laravel\Sanctum\PersonalAccessToken

### Community 32 - "OneOnOneSession"
Cohesion: 0.10
Nodes (8): index(), ListParams, OneOnOneSession, OneOnOneSessionPolicy, GenericIndexService, OneOnOneSessionIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 33 - "IntegrationWebhookEvent"
Cohesion: 0.08
Nodes (4): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, ClickUpWebhookIngestService, GitHubWebhookIngestService

### Community 35 - "User.php"
Cohesion: 0.50
Nodes (3): Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens

### Community 37 - "PersonInvitation"
Cohesion: 0.27
Nodes (3): PersonInvitation, PersonInvitationCreateService, PersonInvitationFactory

### Community 38 - "UpdateDevelopmentPlanRequest.php"
Cohesion: 0.29
Nodes (3): UpdateDevelopmentPlanRequest, Carbon\CarbonImmutable, Illuminate\Validation\Validator

### Community 42 - "TenantIsolationTest.php"
Cohesion: 0.33
Nodes (3): Tenant, RegisterUserService, tenantFixture()

### Community 46 - "Laravel Cloud Production Deployment"
Cohesion: 0.40
Nodes (6): Local Backend Docker Stack, Laravel Cloud Production Deployment, Open Crawling Policy, Backend API Surface, Laravel Cloud Checklist, Scalar API Reference UI

### Community 47 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (5): delete(), update(), DailyMeetingAnnotation, GenericDeleteService, Illuminate\Database\Eloquent\Model

### Community 49 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 51 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 69 - "IntegrationSystemController.php"
Cohesion: 0.29
Nodes (4): IntegrationSystemController, IntegrationSystemResource, IntegrationSystemTokenService, Illuminate\Support\Facades\URL

### Community 77 - "Filterable.php"
Cohesion: 0.42
Nodes (7): filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), Illuminate\Database\Eloquent\Builder

## Knowledge Gaps
- **82 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+77 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DailyMeetingEntry`, `Team`, `PersonExternalIdentity`, `ExternalNotification`, `IntegrationSystem`, `Person`, `DevelopmentPlanItem`, `PersonDeliveryMetric`, `DevelopmentPlan`, `DailyMeeting`, `OneOnOneSession`, `IntegrationWebhookEvent`, `User.php`, `OneOnOneTemplate`, `PersonInvitation`, `TenantIsolationTest.php`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `DatabaseSeeder.php`, `PersonOneOnOneNoteFactory.php`?**
  _High betweenness centrality (0.152) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `DailyMeetingEntry`, `Controller`, `Team`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Http\JsonResponse`, `Illuminate\Contracts\Validation\ValidationRule`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PersonExternalIdentity`, `ExternalNotification`, `IntegrationSystem`, `Person.php`, `DevelopmentPlan`, `DailyMeeting`, `OneOnOneSession`, `PersonInvitation`, `TenantIsolationTest.php`, `DevelopmentPlanFactory.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `IntegrationWebhookEventFactory.php`, `PersonDeliveryMetricFactory.php`, `PersonOneOnOneNoteFactory.php`, `Filterable.php`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `IntegrationWebhookEvent`, `Controller`, `IntegrationSystemController.php`, `TenantIsolationTest.php`, `Filterable.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PersonExternalIdentity`, `IntegrationWebhookEventFactory.php`, `ExternalNotification`, `PersonDeliveryMetricFactory.php`, `ExternalNotificationIngestService`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _82 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.07330827067669173 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.11462450592885376 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.044444444444444446 - nodes in this community are weakly interconnected._