# Release Guide

This document outlines the steps to create releases for the Forerunner package.

## Initial Pre-release (0.1.0) - Current

Starting with v0.1.0 gives us room to iterate with 0.2.0, 0.3.0, etc. before the stable 1.0.0 launch.

### Steps to Create Pre-release

1. **Commit all changes**:
   ```bash
   git add .
   git commit -m "Prepare v0.1.0 initial pre-release

   - Add advanced JSON Schema features
   - Add helper methods for common formats
   - Add comprehensive documentation
   - Add 31 new tests for advanced features"
   ```

2. **Create and push the tag**:
   ```bash
   git tag -a v0.1.0 -m "Initial pre-release v0.1.0

   This is the initial pre-release. Expect updates as we iterate towards 1.0.0.

   New Features:
   - additionalProperties support with strict() helper
   - format validation (email, url, uuid, date-time, etc.)
   - Helper methods: email(), url(), uuid(), datetime(), date(), time(), etc.
   - nullable field support
   - uniqueItems for arrays
   - Schema metadata (title, schemaVersion)
   - Comprehensive documentation and tests

   Technical:
   - 116 tests passing (377 assertions)
   - PHPStan Level 9 compliant
   - PHP 8.2-8.4 compatible
   - Laravel 11-12 compatible"

   git push origin v0.1.0
   ```

3. **Create GitHub Release**:
   - Go to: https://github.com/blaspsoft/forerunner/releases/new
   - Choose tag: `v0.1.0`
   - Release title: `v0.1.0 (Initial Pre-release)`
   - Check "This is a pre-release" box
   - Description:
     ```markdown
     # 🚀 Forerunner v0.1.0 - Initial Pre-release

     This is the **initial pre-release** version. We'll be iterating with 0.x versions as we gather feedback and add features before the 1.0.0 stable release.

     ## ✨ What's New

     ### Advanced JSON Schema Features
     - ✅ `additionalProperties` support with `strict()` helper
     - ✅ String format validation (email, uri, uuid, date-time, etc.)
     - ✅ Nullable field support with `["type", "null"]` syntax
     - ✅ `uniqueItems` constraint for arrays
     - ✅ Schema metadata (`title`, `$schema` version)

     ### 🎯 Helper Methods
     New convenience methods for common patterns:
     - `email()` - Email with format validation
     - `url()` - URL with uri format
     - `uuid()` - UUID with format validation
     - `datetime()` - ISO 8601 date-time
     - `date()`, `time()`, `ipv4()`, `ipv6()`, `hostname()`

     ### 📚 Documentation
     - CONTRIBUTING.md with contribution guidelines
     - SECURITY.md with vulnerability reporting
     - .editorconfig for consistent code style
     - Comprehensive README updates

     ### 🧪 Testing
     - 116 tests passing (377 assertions)
     - PHPStan Level 9 compliance
     - PHP 8.2, 8.3, 8.4 compatible
     - Laravel 11 & 12 compatible

     ## 📦 Installation

     ```bash
     composer require blaspsoft/forerunner:^0.1
     ```

     ## 🐛 Feedback & Iteration

     This is v0.1.0 - the start of our journey to 1.0.0! Expect frequent updates (0.2.0, 0.3.0, etc.) as we:
     - Gather feedback and fix bugs
     - Add new features
     - Refine the API

     Please report any issues or suggestions:
     - [Open an issue](https://github.com/blaspsoft/forerunner/issues)
     - [Security concerns](SECURITY.md)

     ## 📖 Documentation

     See the [README](README.md) for complete documentation and examples.

     ## 🗺️ Roadmap to 1.0.0

     - **v0.1.x** - Initial release with core features
     - **v0.2.x - v0.9.x** - Iterative improvements based on feedback
     - **v1.0.0** - Stable release with locked API
     ```

4. **Announce the pre-release** (optional):
   - Twitter/X
   - Laravel News
   - Reddit r/laravel
   - Your blog/newsletter

## Stable Release (1.0.0) - Future

### When to Release 1.0.0

Release the stable version after:
- [ ] Pre-release has been tested by community (1-2 weeks)
- [ ] No critical bugs reported
- [ ] All feedback addressed
- [ ] Documentation reviewed and finalized

### Steps for 1.0.0 Release

1. **Update CHANGELOG.md**:
   - Move changes from 0.9.0 to 1.0.0
   - Update date
   - Remove pre-release note
   - Add "First stable release" note

2. **Commit and tag**:
   ```bash
   git add CHANGELOG.md
   git commit -m "Release v1.0.0"
   git tag -a v1.0.0 -m "Release v1.0.0 - First stable release"
   git push origin main
   git push origin v1.0.0
   ```

3. **Create GitHub Release** (same process as pre-release, but without "pre-release" checkbox)

4. **Submit to Packagist**:
   - The package will auto-update via GitHub webhook
   - Verify at: https://packagist.org/packages/blaspsoft/forerunner

5. **Announce stable release**:
   - Update README badges if any
   - Social media announcements
   - Laravel News submission
   - Add to awesome-laravel lists

## Version Numbering

We follow [Semantic Versioning](https://semver.org/):

### Pre-1.0.0 Versions (Current: 0.x.x)
- **0.1.0** - Initial pre-release
- **0.2.0, 0.3.0, etc.** - New features, may include breaking changes
- **0.x.1, 0.x.2** - Bug fixes and patches

During the 0.x phase, we reserve the right to make breaking changes between minor versions (0.1 → 0.2) as we refine the API based on feedback.

### Post-1.0.0 Versions (Future)
- **MAJOR** (1.x.x): Breaking changes
- **MINOR** (x.1.x): New features, backwards compatible
- **PATCH** (x.x.1): Bug fixes, backwards compatible

## Release Checklist

Before any release:
- [ ] All tests passing
- [ ] PHPStan Level 9 passing
- [ ] Code formatted with Pint
- [ ] CHANGELOG.md updated
- [ ] README.md updated
- [ ] Version tag matches CHANGELOG
- [ ] Git working directory clean

## Packagist Auto-Update

The package is configured to auto-update on Packagist when you push tags to GitHub. No manual action needed on Packagist.
