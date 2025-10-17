# Contributing to Forerunner

Thank you for considering contributing to Forerunner! This document outlines the process for contributing to this project.

## Code of Conduct

Be respectful and considerate of others. We're all here to build something great together.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- A clear and descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Your environment (PHP version, Laravel version, OS)
- Code samples if applicable

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- A clear and descriptive title
- A detailed description of the proposed functionality
- Examples of how it would be used
- Any potential drawbacks or challenges

### Pull Requests

1. Fork the repository
2. Create a new branch from `main` for your feature or fix
3. Write clear, concise commit messages
4. Include tests for new functionality
5. Ensure all tests pass (`composer test`)
6. Ensure code passes static analysis (`composer analyse`)
7. Ensure code follows style guidelines (`composer format`)
8. Update documentation as needed
9. Submit a pull request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/forerunner.git
cd forerunner

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse

# Format code
composer format
```

## Coding Standards

- Follow PSR-12 coding standards
- Use PHP 8.2+ features appropriately
- Write comprehensive PHPDoc comments
- Maintain PHPStan Level 9 compliance
- All public methods should have type hints and return types

## Testing Guidelines

- Write tests for all new features
- Maintain or improve code coverage
- Use descriptive test names that explain what is being tested
- Follow the existing test structure (Pest tests)
- Test both success and failure cases

## Documentation

- Update README.md for user-facing changes
- Update CLAUDE.md for architectural decisions
- Add examples for new features
- Keep documentation clear and concise

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Reference issues and pull requests when relevant
- First line should be 50 characters or less
- Include detailed explanation after a blank line if needed

Examples:
- `Add support for additionalProperties in schema builder`
- `Fix JSON encoding error handling in Builder::toJson()`
- `Update README with format constraint examples`

## Release Process

Releases are managed by the maintainers. Version numbers follow [Semantic Versioning](https://semver.org/).

## Questions?

Feel free to open an issue for questions or discussion about contributing.

## License

By contributing to Forerunner, you agree that your contributions will be licensed under the MIT License.
