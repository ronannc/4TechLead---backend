# Graph Report - backend  (2026-08-27)

## Corpus Check
- 304 files · ~72,822 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1101 nodes · 2297 edges · 105 communities (64 shown, 41 thin omitted)
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
- PersonInvitationController.php
- DailyMeeting
- TestCase
- User
- DevelopmentPlanItem
- OneOnOneTemplate
- IntegrationSystem
- ListParams
- Illuminate\Foundation\Http\FormRequest
- AcceptPersonInvitationRequest
- ClickUpWebhookController.php
- Illuminate\Database\Eloquent\Model
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
- DevelopmentPlanFactory.php
- OneOnOneTemplateFactory
- IndexDailyMeetingEntryRequest
- IndexDevelopmentPlanRequest
- UpdateDevelopmentPlanItemRequest
- UpdateIntegrationSystemRequest
- UserFactory
- IntegrationSystemController.php
- IndexDailyMeetingRequest
- laravel-boost
- IndexDevelopmentPlanItemRequest
- StoreDevelopmentPlanItemRequest
- UpdateOneOnOneTemplateRequest
- PersonExternalIdentityFactory.php
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

## Communities (105 total, 41 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (18): TeamController, DailyMeetingAnnotationResource, DailyMeetingEntryResource, DailyMeetingResource, DevelopmentPlanItemResource, DevelopmentPlanResource, IntegrationSystemResource, OneOnOneSessionResource (+10 more)

### Community 1 - "DailyMeetingEntry"
Cohesion: 0.11
Nodes (4): DailyMeetingEntry, DailyMeetingEntryPolicy, PersonDailyStatsSummaryService, Carbon\Carbon

### Community 2 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 3 - "Controller"
Cohesion: 0.10
Nodes (12): DailyMeetingController, DailyMeetingEntryController, DevelopmentPlanController, DevelopmentPlanItemController, IntegrationWebhookController, OneOnOneSessionController, OneOnOneTemplateController, PersonDeliveryMetricController (+4 more)

### Community 4 - "Team"
Cohesion: 0.14
Nodes (3): Team, TeamPolicy, DailyMeetingFactory

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

### Community 11 - "IntegrationWebhookEvent"
Cohesion: 0.09
Nodes (4): IntegrationWebhookEvent, IntegrationWebhookEventPolicy, ClickUpWebhookIngestService, IntegrationWebhookIngestService

### Community 12 - "CrudControllerTrait.php"
Cohesion: 0.23
Nodes (13): PersonController, destroy(), findModel(), index(), resolveDeleteService(), resolveIndexService(), resolveStoreService(), resolveUpdateService() (+5 more)

### Community 13 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.11
Nodes (9): ExternalNotificationFactory, IntegrationSystemFactory, IntegrationWebhookEventFactory, PersonDeliveryMetricFactory, PersonFactory, PersonInvitationFactory, TeamFactory, TenantFactory (+1 more)

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.11
Nodes (9): InvalidCredentialsException, AuthController, AuthenticatedPersonController, ExternalNotificationController, ExternalNotificationWebhookController, ExternalNotificationResource, LoginService, Exception (+1 more)

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
Nodes (10): PersonExternalIdentity, PersonExternalIdentityPolicy, PersonExternalIdentityStoreService, Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Illuminate\Testing\Fluent\AssertableJson, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum (+2 more)

### Community 19 - "Generic CRUD Architecture"
Cohesion: 0.12
Nodes (16): After Method Cross-Field Validation, Form Request Validation, Validated Data for Mass Assignment, Architecture Testing, Browser Testing, Pest PHP Testing, External Actor Mapping, PR Metrics Webhook Payload (+8 more)

### Community 20 - "Person"
Cohesion: 0.11
Nodes (5): PersonGrowthSuggestionController, Person, Attribute, PersonPolicy, Illuminate\Database\Eloquent\Relations\HasOne

### Community 22 - "DailyMeeting"
Cohesion: 0.10
Nodes (5): DailyMeeting, DailyMeetingPolicy, DailyMeetingAnnotationFactory, DailyMeetingEntryFactory, static

### Community 23 - "TestCase"
Cohesion: 0.15
Nodes (7): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, TestCase

### Community 24 - "User"
Cohesion: 0.14
Nodes (5): User, PersonDeliveryMetricPolicy, LogoutService, Illuminate\Foundation\Auth\User, Laravel\Sanctum\PersonalAccessToken

### Community 26 - "OneOnOneTemplate"
Cohesion: 0.15
Nodes (3): OneOnOneTemplate, OneOnOneTemplatePolicy, OneOnOneSessionFactory

### Community 27 - "IntegrationSystem"
Cohesion: 0.05
Nodes (23): delete(), store(), IntegrationSystem, Tenant, IntegrationSystemPolicy, AcceptPersonInvitationService, RegisterUserService, DailyMeetingStoreService (+15 more)

### Community 28 - "ListParams"
Cohesion: 0.27
Nodes (5): index(), ListParams, GenericIndexService, Illuminate\Contracts\Pagination\LengthAwarePaginator, self

### Community 29 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): LoginRequest, RegisterRequest, IndexOneOnOneTemplateRequest, StoreOneOnOneTemplateRequest, UpdatePersonExternalIdentityRequest, StoreTeamRequest, Illuminate\Foundation\Http\FormRequest

### Community 31 - "ClickUpWebhookController.php"
Cohesion: 0.24
Nodes (3): ClickUpWebhookController, GitHubWebhookController, IntegrationWebhookEventResource

### Community 32 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.05
Nodes (25): update(), filterableFields(), scopeFilter(), scopeOrder(), scopeSearch(), searchableFields(), sortableFields(), DailyMeetingAnnotation (+17 more)

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

## Knowledge Gaps
- **82 isolated node(s):** `php`, `docker-entrypoint.sh script`, `php`, `$schema`, `name` (+77 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `DailyMeetingEntry`, `Team`, `ExternalNotification`, `IntegrationWebhookEvent`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Http\JsonResponse`, `DevelopmentPlan`, `PersonExternalIdentity`, `DatabaseSeeder.php`, `Person`, `DailyMeeting`, `DevelopmentPlanItem`, `OneOnOneTemplate`, `IntegrationSystem`?**
  _High betweenness centrality (0.159) - this node is a cross-community bridge._
- **Why does `Person` connect `Person` to `Illuminate\Database\Eloquent\Model`, `DailyMeetingEntry`, `Controller`, `Team`, `CrudControllerTrait.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `PersonExternalIdentityFactory.php`, `Illuminate\Contracts\Validation\ValidationRule`, `DevelopmentPlan`, `PersonExternalIdentity`, `PersonInvitationController.php`, `DailyMeeting`, `DevelopmentPlanFactory.php`, `OneOnOneTemplate`, `IntegrationSystem`?**
  _High betweenness centrality (0.080) - this node is a cross-community bridge._
- **Why does `IntegrationSystem` connect `IntegrationSystem` to `Illuminate\Database\Eloquent\Model`, `IntegrationSystemController.php`, `IntegrationWebhookEvent`, `Illuminate\Database\Eloquent\Factories\Factory`, `Illuminate\Http\JsonResponse`, `GitHubWebhookIngestService`, `PersonExternalIdentityFactory.php`, `PersonExternalIdentity`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **What connects `php`, `docker-entrypoint.sh script`, `php` to the rest of the system?**
  _82 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.08313725490196078 - nodes in this community are weakly interconnected._
- **Should `DailyMeetingEntry` be split into smaller, more focused modules?**
  _Cohesion score 0.11462450592885376 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.044444444444444446 - nodes in this community are weakly interconnected._