# Contributing to Mobile Lookup Module

First off, thank you for considering contributing to Mobile Lookup Module! 🎉

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find that you don't need to create one. When creating a bug report, please include as many details as possible:

* **Use a clear and descriptive title**
* **Describe the exact steps to reproduce the problem**
* **Provide specific examples** (field configurations, browser/device info)
* **Describe the behavior you observed** and what you expected to see
* **Include screenshots or GIFs** if possible
* **Include your environment details**: Xataface version, PHP version, browser, OS

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

* **Use a clear and descriptive title**
* **Provide a step-by-step description** of the suggested enhancement
* **Provide specific examples** to demonstrate the steps
* **Describe the current behavior** and explain the behavior you'd like to see
* **Explain why this enhancement would be useful**

### Pull Requests

1. Fork the repo and create your branch from `main`
2. If you've added code that should be tested, add tests
3. If you've changed APIs, update the documentation
4. Ensure your code follows the existing style
5. Write a clear commit message

## Development Setup

### Prerequisites

* Xataface 2.x installation
* PHP 7.0+
* MySQL/MariaDB database
* Modern browser for testing

### Local Development

1. Clone your fork:
   ```bash
   git clone https://github.com/plcats/module-mobile_lookup.git
   ```

2. Copy to your Xataface modules directory:
   ```bash
   cp -r xataface-mobile-lookup /path/to/xataface/modules/mobile_lookup
   ```

3. Enable in `conf.ini`:
   ```ini
   [_modules]
       modules_mobile_lookup=modules/mobile_lookup/mobile_lookup.php
   ```

4. Test in a real Xataface application

### Testing

Please test your changes on:
- ✅ Desktop browsers (Chrome, Firefox, Safari, Edge)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile, Firefox Mobile)
- ✅ Different screen sizes (mobile, tablet, desktop)
- ✅ Both new and edit forms
- ✅ Grid/related records context

## Style Guidelines

### PHP Code Style

* Use 4 spaces for indentation (not tabs)
* Follow PSR-1 and PSR-2 coding standards where possible
* Use meaningful variable names
* Add comments for complex logic
* Use `@param` and `@return` docblocks

Example:
```php
/**
 * Retrieve record label from database
 * 
 * @param string $tableName Target table name
 * @param string $keyValue Primary key value
 * @return string Record label or key value on error
 */
function getRecordLabel($tableName, $keyValue) {
    // Implementation
}
```

### JavaScript Code Style

* Use 4 spaces for indentation
* Use semicolons
* Use single quotes for strings
* Use meaningful variable names
* Add JSDoc comments for functions

Example:
```javascript
/**
 * Initialize mobile lookup widget
 * 
 * @param {jQuery} $select - Select element to enhance
 * @param {Object} options - Configuration options
 */
function initMobileLookup($select, options) {
    // Implementation
}
```

### CSS Code Style

* Use 4 spaces for indentation
* Group related properties
* Use meaningful class names with `xf-` prefix
* Add comments for sections
* Mobile-first approach (base styles for mobile, media queries for desktop)

Example:
```css
/* ============================================
   Touch targets (mobile-first)
   ============================================ */

.xf-mobile-lookup-button {
    min-height: 44px; /* iOS touch target */
    padding: 12px 16px;
}

/* Desktop refinement */
@media (min-width: 768px) {
    .xf-mobile-lookup-button {
        min-height: 32px;
        padding: 6px 12px;
    }
}
```

## Commit Messages

* Use the present tense ("Add feature" not "Added feature")
* Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
* Limit the first line to 72 characters
* Reference issues and pull requests after the first line

Examples:
```
Add support for multi-select mode

Closes #123
```

```
Fix zoom issue on iOS Safari

- Increase font-size to 16px in search input
- Add viewport meta tag check
- Update mobile detection logic

Fixes #45
```

## Documentation

* Update README.md if you change functionality
* Update CHANGELOG.md following [Keep a Changelog](https://keepachangelog.com/) format
* Add code comments for complex logic
* Update inline documentation for new parameters

## Questions?

Feel free to open an issue with the `question` label, or reach out to the maintainer.

## License

By contributing, you agree that your contributions will be licensed under the GNU GPL v2 License.
