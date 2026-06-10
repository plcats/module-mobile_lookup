# Installation Guide

This guide provides detailed instructions for installing and configuring the Mobile Lookup module for Xataface.

## Requirements

- **Xataface**: 2.x or later
- **PHP**: 7.0 or later
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Browser**: Modern browser with JavaScript enabled
- **Internet connection**: Required for Select2 CDN (or you can host Select2 locally)

## Installation Methods

### Method 1: Manual Installation (Recommended)

1. **Download the module**

   Download and extract the module to your Xataface `modules` directory:
   ```bash
   cd /path/to/your/xataface/modules
   git clone https://github.com/plcats/module-mobile_lookup.git mobile_lookup
   ```

   Or download the ZIP file and extract it.

2. **Enable the module**

   Edit your application's `conf.ini` file and add:
   ```ini
   [_modules]
       modules_mobile_lookup=modules/mobile_lookup/mobile_lookup.php
   ```

3. **Clear cache**

   Clear your Xataface cache:
   ```bash
   rm -rf /path/to/your/app/templates_c/*
   ```

4. **Test the installation**

   Open your Xataface application and check the browser console for any errors.

### Method 2: Using Composer (Future)

```bash
composer require pbonzini/xataface-mobile-lookup
```

Then enable in `conf.ini` as shown in Method 1.

## Configuration

### Basic Configuration

In your table's `fields.ini` file, add:

```ini
[your_field_name]
    widget:type=mobile_lookup
    widget:table=TargetTable
    widget:keycol=id
    widget:labelcol=name
```

### Advanced Configuration

```ini
[customer_id]
    widget:type=mobile_lookup
    widget:table=Customers
    widget:keycol=id
    widget:labelcol=company_name,contact_name
    widget:searchFields=company_name,contact_name,email,phone
    widget:placeholder="Search customer by name, email, or phone..."
    widget:minimumInputLength=2
    widget:preloadOptions=selected
    widget:allscreen=0
```

### Configuration with Dynamic Filters

```ini
[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:keycol=id
    widget:labelcol=name
    widget:searchFields=name,sku
    widget:filters[category_id]=$category_id
    widget:filters[active]=1
```

## Configuration Options Reference

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `widget:table` | string | **required** | Target table name |
| `widget:keycol` | string | `id` | Primary key column |
| `widget:labelcol` | string | `name` | Display column(s), comma-separated for multiple |
| `widget:searchFields` | string | `labelcol` | Searchable columns, comma-separated |
| `widget:placeholder` | string | `"Seleziona..."` | Input placeholder text |
| `widget:minimumInputLength` | int | `0` | Minimum characters before search |
| `widget:preloadOptions` | string | `selected` | Preload mode: `selected`, `first100`, or `none` |
| `widget:filters[col]` | mixed | - | Static or dynamic (`$field`) filter |
| `widget:allscreen` | bool | `0` | Enable fullscreen modal on mobile |
| `widget:canEdit` | bool | auto | Show edit button (auto-detected from permissions) |
| `widget:canNew` | bool | auto | Show new button (auto-detected from permissions) |
| `widget:atts:maxWidth` | string | - | Max width CSS value (e.g., `500px`, `50%`) |

## Search Behavior (v1.1.0+)

The AJAX search splits the user input into words and requires **every word** to match at least one field listed in `widget:searchFields`.

This matches the native Xataface lookup (`RecordBrowser` / `QueryBuilder`) behavior.

Example:

```ini
[fkArticolo]
    widget:type=mobile_lookup
    widget:table=BBGListino_Valido
    widget:keycol=cod
    widget:labelcol=xxTitolo
    widget:searchFields=cod,desc_breve,descrizione,codFornitore
```

Query `tubol 120` matches a record titled `tubolare 120 x 40`.

## UX: Auto-focus

From v1.1.0, when the dropdown opens the search input receives focus automatically so the user can type immediately (including in `widget:allscreen=1` mode).

## Performance Optimization

### For Large Datasets

Use `preloadOptions=none` to avoid loading initial options:

```ini
[customer_id]
    widget:type=mobile_lookup
    widget:table=Customers
    widget:preloadOptions=none
    widget:minimumInputLength=2
```

### For Grid/Related Records

Use `preloadOptions=selected` (default) to only load the current value:

```ini
[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:preloadOptions=selected
```

## Troubleshooting

### Module not loading

1. Check that the module path in `conf.ini` is correct
2. Verify file permissions (should be readable by web server)
3. Clear templates cache: `rm -rf templates_c/*`
4. Check PHP error logs

### Widget not appearing

1. Open browser console (F12) and check for JavaScript errors
2. Verify Select2 CDN is accessible
3. Check that the field type is set correctly in `fields.ini`
4. Verify the user has `view` permission on the target table

### Search not working

1. Check database permissions (user needs SELECT on target table)
2. Verify `widget:searchFields` columns exist in the target table
3. Test the AJAX endpoint manually:
   ```
   https://yourdomain.com/yourapp/?-action=mobile_lookup_search&-table=YourTable&-search=test
   ```
4. Check PHP error logs

### Touch not working on iOS

1. Ensure font-size is at least 16px (automatic in this module)
2. Clear browser cache
3. Test on a real device (iOS Simulator may behave differently)

## Uninstallation

1. Remove the module line from `conf.ini`:
   ```ini
   ; [_modules]
   ;     modules_mobile_lookup=modules/mobile_lookup/mobile_lookup.php
   ```

2. Change all fields using `mobile_lookup` back to standard widgets in `fields.ini`

3. Clear cache:
   ```bash
   rm -rf /path/to/your/app/templates_c/*
   ```

4. Delete the module directory:
   ```bash
   rm -rf /path/to/xataface/modules/mobile_lookup
   ```

## Getting Help

- **Documentation**: [README.md](README.md)
- **Issues**: [GitHub Issues](https://github.com/plcats/module-mobile_lookup/issues)
- **Community**: Xataface forum

## License

This module is licensed under GNU GPL v2. See [LICENSE](LICENSE) for details.
