<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5.0
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version-specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests that have a lot of duplicated data. This is often the case when testing validation rules, so consider this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>

=== pest/v4 rules ===

## Pest 4

- Pest 4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest 4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>

=== tailwindcss/core rules ===

## Tailwind CSS

- Use Tailwind CSS classes to style HTML; check and use existing Tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc.).
- Think through class placement, order, priority, and defaults. Remove redundant classes, add classes to parent or child carefully to limit repetition, and group elements logically.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing; don't use margins.

<code-snippet name="Valid Flex Gap Spacing Example" lang="html">
    <div class="flex gap-8">
        <div>Superior</div>
        <div>Michigan</div>
        <div>Erie</div>
    </div>
</code-snippet>

### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

=== tailwindcss/v4 rules ===

## Tailwind CSS 4

- Always use Tailwind CSS v4; do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.

<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>

=== laravel/best-practices rules ===

## Laravel Best Practices

### Naming Conventions
- **Classes**: PascalCase — `UserController`, `CreateUserAction`, `UserRepository`
- **Methods/Functions**: camelCase — `getFullName()`, `isVerified()`, `sendWelcomeEmail()`
- **Variables/Properties**: camelCase — `$firstName`, `$isActive`, `$userRepository`
- **Database Tables**: snake_case plural — `users`, `password_reset_tokens`, `course_student`
- **Database Columns**: snake_case — `first_name`, `is_active`, `email_verified_at`
- **Relationships**: camelCase matching the related model — `user()`, `posts()`, `latestPost()`
- **Routes**: kebab-case — `/user-profiles`, `/course-enrollments`
- **Route Names**: dot notation — `users.show`, `courses.enrollments.store`
- **Controllers**: singular PascalCase — `UserController`, `CourseCategoryController`
- **Models**: singular PascalCase — `User`, `CourseCategory`, `PasswordResetToken`
- **Form Requests**: singular with action — `StoreUserRequest`, `UpdateCourseRequest`
- **Migrations**: descriptive — `add_avatar_to_users_table`
- **Middleware**: camelCase with intent — `isAdmin`, `forceJson`, `setLocale`
- **Config Keys**: snake_case with vendor prefix — `app.name`, `services.stripe.key`
- **Env Variables**: UPPER_SNAKE_CASE — `APP_NAME`, `STRIPE_KEY`
- **Resources/Collections**: singular/plural — `UserResource`, `UserCollection`
- **Policies**: singular Model+Policy — `UserPolicy`, `CoursePolicy`
- **Observers**: singular Model+Observer — `UserObserver`, `CourseObserver`
- **Events**: past tense — `UserRegistered`, `CourseCreated`
- **Listeners**: descriptive with intent — `SendWelcomeEmail`, `LogLoginAttempt`
- **Notifications**: descriptive — `WelcomeNotification`, `PaymentConfirmed`
- **Jobs**: descriptive with action — `ProcessVideo`, `GenerateReport`
- **Rules**: descriptive — `ValidPhoneNumber`, `StrongPassword`
- **Scopes**: descriptive camelCase — `scopePopular()`, `scopeActive()`, `scopeByCategory()`

### Query Best Practices (Eloquent)
- Always use Eloquent, never raw SQL unless absolutely unavoidable.
- Use subquery select to avoid N+1: `User::addSelect(['last_post_id' => Post::select('id')->whereColumn('user_id', 'users.id')->latest()->take(1)])`
- Use `withCount()`, `withExists()`, `withAvg()`, `withSum()` for aggregated eager loading.
- Use `whereHas()` / `whereDoesntHave()` instead of looping in PHP for existence checks.
- Use `cursor()` or `chunk()` for large datasets instead of `get()`.
- Use `lazy()` for memory-efficient iteration over large collections.
- Always prefer `find()` / `findOrFail()` over `where('id', $id)->first()`.
- Use `firstOrCreate()` / `firstOrNew()` / `updateOrCreate()` to avoid race conditions.
- Use `pluck()` when you only need specific values, not full models.
- Use `when()` for conditional query scoping instead of if/else blocks.
- Use `mapInto()` when hydrating results into specific objects.
- Use `load()` / `loadCount()` for conditional eager loading after query execution.
- Avoid `all()` — always chain at least one filter.
- Use `whereHas()` with nested relations for deep filtering.
- Use `with(['relation' => fn ($q) => $q->select('id', 'name')])` to limit columns on eager loads.
- Use `whereRelation()` / `whereDoesntHaveRelation()` for cleaner syntax on relationship conditions.

<code-snippet name="Query Best Practice Example" lang="php">
// Good
$users = User::query()
    ->with('profile')
    ->whereHas('posts', fn ($q) => $q->where('is_published', true))
    ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
    ->latest()
    ->paginate();

// Bad
$users = User::all();
foreach ($users as $user) {
    $profile = DB::table('profiles')->where('user_id', $user->id)->first();
}
</code-snippet>

### Clean Architecture Patterns

#### Action Pattern (Single Action Classes)
- Use for single-responsibility actions that encapsulate one business operation.
- Store in `app/Actions/` directory.
- Invocable via `__invoke()` — single method class.
- Keep them small and focused on one task.

<code-snippet name="Action Pattern Example" lang="php">
namespace App\Actions;

class RegisterUserAction
{
    public function __construct(
        protected CreateUserAction $createUser,
        protected SendWelcomeEmailAction $sendWelcomeEmail,
    ) {}

    public function __invoke(array $data): User
    {
        $user = ($this->createUser)($data);
        ($this->sendWelcomeEmail)($user);

        return $user;
    }
}
</code-snippet>

#### Service Pattern
- Use for grouping related business logic that doesn't fit a single action.
- Store in `app/Services/` directory.
- Services should not know about HTTP (no Request/Response).
- Services can compose multiple actions.

<code-snippet name="Service Pattern Example" lang="php">
namespace App\Services;

class PaymentService
{
    public function __construct(
        protected PaymentGateway $gateway,
        protected InvoiceService $invoiceService,
    ) {}

    public function processPayment(Order $order, array $paymentData): Payment
    {
        $charge = $this->gateway->charge($order->total, $paymentData);
        $this->invoiceService->generate($order, $charge);

        return $order->payments()->create([
            'charge_id' => $charge->id,
            'amount' => $charge->amount,
            'status' => $charge->status,
        ]);
    }
}
</code-snippet>

#### Repository Pattern
- Use only when you need to abstract data access (complex queries, multiple data sources).
- Store in `app/Repositories/` directory.
- Define interfaces in `app/Contracts/Repositories/` if multiple implementations exist.
- For simple CRUD, use Eloquent directly (Eloquent IS the repository).
- Do NOT create repositories for every model — only when abstraction adds value.

<code-snippet name="Repository Pattern Example" lang="php">
namespace App\Repositories;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function findActiveUsersWithPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('is_active', true)
            ->whereHas('posts')
            ->withCount('posts')
            ->latest()
            ->paginate($perPage);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}
</code-snippet>

#### Folder Structure for Clean Architecture
```
app/
├── Actions/
│   ├── Auth/
│   │   ├── LoginUserAction.php
│   │   └── RegisterUserAction.php
│   ├── Course/
│   │   ├── CreateCourseAction.php
│   │   └── EnrollStudentAction.php
│   └── ...
├── Services/
│   ├── PaymentService.php
│   ├── NotificationService.php
│   └── ...
├── Repositories/
│   ├── UserRepository.php
│   ├── CourseRepository.php
│   └── ...
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Enums/
├── Events/
├── Listeners/
├── Jobs/
├── Mail/
├── Notifications/
├── Policies/
├── Rules/
└── Providers/
```

#### Best Practices for Clean Architecture in Laravel
- **Controllers are thin** — they only validate, call actions/services, and return responses.
- **Models are fat** — they contain relationships, scopes, accessors, mutators, and casts.
- **No business logic in controllers** — delegate to Actions or Services.
- **No business logic in service providers** — use them only for binding and registration.
- **Actions for single operations** — Service for grouped/related operations.
- **Form Requests** handle ALL validation — never validate in controller.
- **API Resources** handle ALL response transformation.
- **Use dependency injection** through constructors, not `app()` helper.
- **Use `route()` for URLs** — never hardcode paths.
- **Avoid `dd()`/`dump()` in committed code** — use logging or `$exception->getMessage()`.

### Helper Pattern
- Custom helpers go in `app/Helpers/` directory.
- Use static methods on helper classes, not global functions (unless Laravel's built-in helpers).
- Register via Composer autoload `files` key if global helpers are absolutely needed.
- Prefer invocable classes over helper functions for complex logic.

<code-snippet name="Helper Pattern Example" lang="php">
namespace App\Helpers;

class NumberHelper
{
    public static function formatCurrency(float $amount, string $currency = 'IDR'): string
    {
        return match ($currency) {
            'IDR' => 'Rp ' . number_format($amount, 0, ',', '.'),
            'USD' => '$ ' . number_format($amount, 2),
            default => number_format($amount),
        };
    }
}
</code-snippet>

### General Code Quality Rules
- Always use strict types: `declare(strict_types=1);`
- Return type declarations on ALL methods.
- Use constructor property promotion.
- Use enums for fixed sets of values.
- Use `match()` instead of `switch()`.
- Use `str()` / `Str::` instead of raw PHP string functions.
- Use `Arr::`, `Str::` facade helpers instead of raw PHP array/string functions.
- Prefer `Collection` methods over `foreach` loops.
- Use `#[Route]` attributes on controllers if the app uses them.
- Use `Carbon` for all date manipulation.
- Never use `env()` outside config files.
- Cache config with `php artisan config:cache` in production.

### Database / Migration
- Always use `foreignId()` for FK columns — `$table->foreignId('user_id')->constrained()->cascadeOnDelete();`
- Use `ulid()` or `uuid()` for public-facing IDs instead of auto-increment.
- Use `softDeletes()` for data that should not be permanently deleted.
- Use `index()` on columns used in `WHERE` / `JOIN` / `ORDER BY`.
- Use `fullText()` for searchable text columns.
- Add `->comment()` for columns that need explanation.
- Migrations should be reversible — implement `down()` properly.
- Use batchable migrations for large data migrations.

<code-snippet name="Migration Best Practice" lang="php">
Schema::create('courses', function (Blueprint $table) {
    $table->ulid();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
    $table->decimal('price', 12, 2)->default(0);
    $table->boolean('is_published')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->softDeletes();
    $table->timestamps();

    $table->fullText(['title', 'description']);
    $table->index('is_published');
});
</code-snippet>

### Relationships / Eloquent
- Always define `ForeignKey` and `localKey` explicitly in relationship methods.
- Use `belongsTo()` for inverse relationships — always define them.
- Use `hasManyThrough()` for deep nested relationships.
- Use `morphMany()` / `morphTo()` for polymorphic relationships.
- Define `withDefault()` for optional belongs-to to avoid null checks.
- Use `latest()` / `oldest()` global scopes on relationships when ordering matters.

<code-snippet name="Relationship Best Practice" lang="php">
class Course extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_student')
            ->withPivot('enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function latestLessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->latest()->limit(5);
    }
}
</code-snippet>

### Testing Best Practices
- One test class per class being tested.
- Use descriptive test names: `it_can_create_a_course`, `it_prevents_duplicate_enrollment`.
- Use `Dataset` for testing multiple validation rules.
- Use `RefreshDatabase` for feature tests.
- Use factories with meaningful states: `User::factory()->unverified()->create()`.
- Test happy path, validation errors, authorization errors, edge cases.
- Use `assertDatabaseHas()` / `assertDatabaseMissing()` for data assertions.
- Use `Notification::fake()`, `Event::fake()`, `Mail::fake()`, `Queue::fake()`.

### Validation
- Use Form Request classes always.
- Use custom Rule classes for complex validation logic.
- Use `bail()` to stop validation on first failure.
- Use `sometimes()` for conditional validation.
- Use `exists:table,column` for existence checks; use Rule::exists for dynamic tables.
- Use `unique:table,column` for uniqueness; ignore current ID on update: `Rule::unique('users')->ignore($this->user)`.
- Use `prohibited_if()` / `prohibits()` for mutually exclusive fields.
- Use `array` + `*` notation for nested validation.

<code-snippet name="Form Request Example" lang="php">
class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Course::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul course wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
        ];
    }
}
</code-snippet>
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option; use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
