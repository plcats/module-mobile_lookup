# Mobile Lookup Module Documentation

Welcome to the Mobile Lookup module documentation!

## Documentation Files

- **[EXAMPLES.md](EXAMPLES.md)** - Real-world usage examples
- **[API.md](API.md)** - Technical API reference (coming soon)
- **[MIGRATION.md](MIGRATION.md)** - Migrating from standard lookup (coming soon)

## Quick Links

- [Installation Guide](../INSTALL.md)
- [Main README](../README.md)
- [Contributing Guidelines](../CONTRIBUTING.md)
- [Changelog](../CHANGELOG.md)

## Architecture Overview

### Components

```
mobile_lookup/
├── mobile_lookup.php          # Main module file
├── widget.php                 # Widget handler (FormTool)
├── QuickForm_mobile_lookup.php # QuickForm element (deprecated)
├── HTML/QuickForm/
│   └── mobile_lookup.php      # QuickForm element (active)
├── actions/
│   └── mobile_lookup_search.php # AJAX search handler
├── js/
│   └── mobile-lookup.js       # Frontend JavaScript
└── css/
    └── mobile-lookup.css      # Styles
```

### Data Flow

```
User Types → Select2 AJAX → mobile_lookup_search action
                               ↓
                          Query Database (df_db)
                               ↓
                          JSON Response
                               ↓
                          Select2 Display → User Selection
```

### Integration Points

1. **Xataface Module System**: Registered in `conf.ini`
2. **FormTool**: Widget handler registered
3. **Decorator Pattern**: JavaScript initialization on DOM ready
4. **AJAX Actions**: Custom action handler for search
5. **HTML QuickForm**: Custom element type

## Technical Details

### Select2 Configuration

The module uses Select2 4.1.0-rc.0 with custom configuration:

- **AJAX transport**: Custom data adapter for Xataface
- **Template functions**: `formatResult()` and `formatSelection()`
- **Mobile optimizations**: Touch targets, font sizes, responsive CSS
- **Language**: Italian localization

### Security

1. **SQL Injection Prevention**: Uses `mysqli_prepare()` and parameter binding
2. **Input Validation**: Whitelist validation for table/column names
3. **Permission Checks**: Respects Xataface table permissions
4. **XSS Prevention**: HTML escaping in JavaScript

### Performance Considerations

- **Lazy Loading**: Only loads selected value by default
- **Pagination**: 30 records per page
- **Client-side Caching**: Select2 caches AJAX responses
- **Prepared Statements**: Database query optimization

### Browser Support

| Browser | Version | Support |
|---------|---------|---------|
| Chrome | 90+ | ✅ Full |
| Firefox | 88+ | ✅ Full |
| Safari | 14+ | ✅ Full |
| Edge | 90+ | ✅ Full |
| iOS Safari | 14+ | ✅ Full |
| Chrome Mobile | 90+ | ✅ Full |
| IE 11 | - | ❌ Not supported |

## Customization

### Custom CSS

Override styles in your application's CSS:

```css
/* Increase touch targets */
.xf-mobile-lookup-dropdown .select2-results__option {
    min-height: 60px !important;
}

/* Custom colors */
.select2-results__option--highlighted {
    background-color: #your-brand-color !important;
}
```

### Custom JavaScript

Hook into events:

```javascript
jQuery(document).ready(function($) {
    $('.xf-mobile-lookup').on('select2:select', function(e) {
        console.log('Selected:', e.params.data);
    });
});
```

## Troubleshooting

### Debug Mode

Enable debug output in JavaScript console:

```javascript
// Add to your application's JavaScript
window.xfMobileLookupDebug = true;
```

### Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| Widget not showing | Module not loaded | Check `conf.ini`, clear cache |
| Search not working | AJAX endpoint blocked | Check `.htaccess`, server logs |
| Wrong labels | Cache issue | Clear browser cache, check `labelcol` |
| Performance slow | Large dataset | Use `minimumInputLength`, `preloadOptions=none` |

## API Reference

### Widget Parameters

All parameters are set in `fields.ini` with `widget:` prefix.

#### Required Parameters

- `widget:type` - Must be `mobile_lookup`
- `widget:table` - Target table name

#### Optional Parameters

See [README.md](../README.md) for full parameter reference.

### JavaScript API

```javascript
// Get Select2 instance
var $select = $('.xf-mobile-lookup');
var select2 = $select.data('select2');

// Programmatically open
$select.select2('open');

// Programmatically close
$select.select2('close');

// Get current value
var value = $select.val();

// Set value
$select.val('123').trigger('change');
```

### PHP API

```php
// Access widget properties in delegate class
function field__product_id__widget(&$record, &$field) {
    $widget = $field['widget'];
    $widget['filters']['active'] = 1;
    return $widget;
}
```

## Performance Tuning

### Database Indexes

Add indexes on searched columns:

```sql
ALTER TABLE Products ADD INDEX idx_search (name, sku);
ALTER TABLE Customers ADD INDEX idx_search (company_name, email);
```

### Caching Strategy

The module uses multi-level caching:

1. **Browser cache**: Select2 AJAX responses (30s)
2. **PHP static cache**: Table metadata (per request)
3. **Option cache**: Seed options (per request)

### Optimization Guidelines

- Use `preloadOptions=selected` in grids (default)
- Set `minimumInputLength` for large datasets
- Add database indexes on search columns
- Limit `searchFields` to indexed columns
- Use specific filters to reduce result sets

## Contributing

See [CONTRIBUTING.md](../CONTRIBUTING.md) for development guidelines.

## License

GNU GPL v2 - See [LICENSE](../LICENSE)
