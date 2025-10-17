# Security Policy

## Supported Versions

We release patches for security vulnerabilities for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 0.x     | :white_check_mark: |

## Reporting a Vulnerability

We take the security of Forerunner seriously. If you discover a security vulnerability, please follow these steps:

### Please Do Not

- Open a public GitHub issue for security vulnerabilities
- Disclose the vulnerability publicly before it has been addressed

### Please Do

1. **Email us privately** at mike.deeming@blaspsoft.com with details of the vulnerability
2. Include the following information:
   - Description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact
   - Suggested fix (if you have one)

### What to Expect

- **Acknowledgment**: We will acknowledge receipt of your vulnerability report within 48 hours
- **Updates**: We will provide regular updates about our progress
- **Timeline**: We aim to address critical vulnerabilities within 7 days
- **Credit**: If you wish, we will credit you for the discovery in our changelog

### Disclosure Policy

- We will investigate all legitimate reports and do our best to fix vulnerabilities quickly
- We will coordinate the release timing with you
- We will publicly disclose the vulnerability after a fix is released

## Security Best Practices for Users

When using Forerunner:

1. **Keep dependencies updated**: Regularly update the package and its dependencies
2. **Validate user input**: Never pass untrusted user input directly to schema definitions
3. **Sanitize dynamic schemas**: If building schemas from user input, validate and sanitize all data
4. **Review generated schemas**: Ensure generated JSON schemas match your security requirements
5. **Monitor for updates**: Watch the repository for security-related updates

## Known Security Considerations

- This package generates JSON schemas for validation purposes
- Never use user input directly in callback functions without validation
- Be cautious when dynamically generating schemas from external data sources
- JSON encoding is used throughout - ensure PHP's JSON extension is properly configured

## Questions?

If you have questions about security that are not sensitive in nature, feel free to open a GitHub issue or contact us at mike.deeming@blaspsoft.com.
