<?php

declare(strict_types=1);
use Illuminate\Support\Facades\File;

beforeEach(function () {
    // Clean up any test files
    $this->testFilePath = app_path('Structures/TestUserStruct.php');
    if (File::exists($this->testFilePath)) {
        File::delete($this->testFilePath);
    }
});

afterEach(function () {
    // Clean up test files after each test
    if (isset($this->testFilePath) && File::exists($this->testFilePath)) {
        File::delete($this->testFilePath);
    }
});

it('can generate a struct class', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    expect(File::exists($this->testFilePath))->toBeTrue();
});

it('generates struct with correct namespace', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain('namespace App\Structures;');
});

it('generates struct with correct class name', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain('class TestUserStruct');
});

it('generates struct with correct imports', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain('use Blaspsoft\Forerunner\Schema\Struct;')
        ->and($content)->toContain('use Blaspsoft\Forerunner\Schema\Builder;');
});

it('generates struct with schema method', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain('public static function schema(): array')
        ->and($content)->toContain("Struct::define('test_user_struct'")
        ->and($content)->toContain('->toArray()');
});

it('generates struct with strict mode by default', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain('$builder->strict()');
});

it('converts PascalCase to snake_case for struct name', function () {
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $content = File::get($this->testFilePath);

    expect($content)->toContain("Struct::define('test_user_struct'");
});

it('can generate struct with force flag when file exists', function () {
    // Create file first
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    // Try to create again with force flag
    $this->artisan('make:struct', ['name' => 'TestUserStruct', '--force' => true])
        ->assertExitCode(0);

    expect(File::exists($this->testFilePath))->toBeTrue();
});

it('does not overwrite existing struct without force flag', function () {
    // Create file first
    $this->artisan('make:struct', ['name' => 'TestUserStruct'])
        ->assertExitCode(0);

    $originalContent = File::get($this->testFilePath);

    // Modify the file
    File::put($this->testFilePath, $originalContent."\n// Modified");

    // Try to create again without force flag - should not overwrite
    $this->artisan('make:struct', ['name' => 'TestUserStruct']);

    $newContent = File::get($this->testFilePath);

    // Content should still have our modification
    expect($newContent)->toContain('// Modified');
});
