# Examples

This document provides real-world examples of how to use the Mobile Lookup module.

## Table of Contents

- [Basic Examples](#basic-examples)
- [Advanced Examples](#advanced-examples)
- [Integration Examples](#integration-examples)
- [Performance Optimization](#performance-optimization)

## Basic Examples

### Simple Customer Lookup

```ini
[customer_id]
    widget:type=mobile_lookup
    widget:table=Customers
    widget:keycol=id
    widget:labelcol=company_name
    widget:placeholder="Select a customer..."
```

### Product Lookup with Search

```ini
[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:keycol=id
    widget:labelcol=name
    widget:searchFields=name,sku,barcode
    widget:placeholder="Search by name, SKU, or barcode..."
    widget:minimumInputLength=2
```

### Multi-Column Display

```ini
[contact_id]
    widget:type=mobile_lookup
    widget:table=Contacts
    widget:keycol=id
    widget:labelcol=first_name,last_name
    widget:searchFields=first_name,last_name,email,phone
    widget:placeholder="Search contact by name, email, or phone..."
```

## Advanced Examples

### Category-Filtered Products

```ini
; In fields.ini for OrderItems table

[category_id]
    widget:type=select
    vocabulary=categories

[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:keycol=id
    widget:labelcol=name,sku
    widget:searchFields=name,sku,description
    widget:filters[category_id]=$category_id
    widget:placeholder="Select product (filtered by category)..."
```

When user selects a category, the product dropdown will only show products from that category.

### Country/State Cascade

```ini
; Country selector
[country_code]
    widget:type=mobile_lookup
    widget:table=Countries
    widget:keycol=code
    widget:labelcol=name
    widget:placeholder="Select country..."

; State/Province selector - filtered by country
[state_code]
    widget:type=mobile_lookup
    widget:table=States
    widget:keycol=code
    widget:labelcol=name
    widget:searchFields=name,code
    widget:filters[country_code]=$country_code
    widget:placeholder="Select state/province..."
```

### Active Records Only

```ini
[supplier_id]
    widget:type=mobile_lookup
    widget:table=Suppliers
    widget:keycol=id
    widget:labelcol=company_name
    widget:searchFields=company_name,contact_name
    widget:filters[active]=1
    widget:placeholder="Select active supplier..."
```

### Exclude Current Record

```ini
[parent_category_id]
    widget:type=mobile_lookup
    widget:table=Categories
    widget:keycol=id
    widget:labelcol=name
    widget:searchFields=name,description
    widget:filters[id]=!$id
    widget:placeholder="Select parent category..."
```

The `!` prefix means "not equal to", preventing circular references.

## Integration Examples

### Order Entry System

**Orders table (fields.ini):**
```ini
[customer_id]
    widget:type=mobile_lookup
    widget:table=Customers
    widget:keycol=id
    widget:labelcol=company_name,contact_name
    widget:searchFields=company_name,contact_name,email,phone
    widget:placeholder="Search customer..."
    widget:minimumInputLength=2

[shipping_address_id]
    widget:type=mobile_lookup
    widget:table=CustomerAddresses
    widget:keycol=id
    widget:labelcol=address_line1,city
    widget:searchFields=address_line1,city,postal_code
    widget:filters[customer_id]=$customer_id
    widget:placeholder="Select shipping address..."
```

**OrderItems relationship (valuelists.ini):**
```ini
[products]
    __sql__ = "SELECT id, CONCAT(name, ' - ', sku) as display_name FROM Products WHERE active=1"
```

Then in OrderItems fields.ini:
```ini
[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:keycol=id
    widget:labelcol=name,sku
    widget:searchFields=name,sku,barcode,description
    widget:filters[active]=1
    widget:placeholder="Search product..."
    widget:preloadOptions=selected
```

### Inventory Management

```ini
[warehouse_id]
    widget:type=mobile_lookup
    widget:table=Warehouses
    widget:keycol=id
    widget:labelcol=name
    widget:placeholder="Select warehouse..."

[location_id]
    widget:type=mobile_lookup
    widget:table=WarehouseLocations
    widget:keycol=id
    widget:labelcol=aisle,rack,shelf
    widget:searchFields=aisle,rack,shelf,barcode
    widget:filters[warehouse_id]=$warehouse_id
    widget:placeholder="Select location in warehouse..."
```

### Multi-Level Categories

```ini
[category_level_1]
    widget:type=mobile_lookup
    widget:table=Categories
    widget:keycol=id
    widget:labelcol=name
    widget:filters[parent_id]=NULL
    widget:placeholder="Select main category..."

[category_level_2]
    widget:type=mobile_lookup
    widget:table=Categories
    widget:keycol=id
    widget:labelcol=name
    widget:filters[parent_id]=$category_level_1
    widget:placeholder="Select subcategory..."

[category_level_3]
    widget:type=mobile_lookup
    widget:table=Categories
    widget:keycol=id
    widget:labelcol=name
    widget:filters[parent_id]=$category_level_2
    widget:placeholder="Select final category..."
```

## Performance Optimization

### Large Dataset (1M+ records)

```ini
[customer_id]
    widget:type=mobile_lookup
    widget:table=Customers
    widget:keycol=id
    widget:labelcol=company_name
    widget:searchFields=company_name,customer_code
    widget:minimumInputLength=3
    widget:preloadOptions=none
    widget:placeholder="Type at least 3 characters to search..."
```

### Grid with Many Rows

When using mobile_lookup in a grid/related records with many rows, use `preloadOptions=selected`:

```ini
[product_id]
    widget:type=mobile_lookup
    widget:table=Products
    widget:keycol=id
    widget:labelcol=name
    widget:preloadOptions=selected
    widget:placeholder="Select product..."
```

This only loads the label for the currently selected value, not all options.

### Frequently Used Lookups

For small, frequently-used lookups (< 100 records), preload for better UX:

```ini
[status_id]
    widget:type=mobile_lookup
    widget:table=OrderStatuses
    widget:keycol=id
    widget:labelcol=name
    widget:preloadOptions=first100
```

## Mobile-Specific Features

### Fullscreen Mode on Mobile

For complex lookups on mobile devices, enable fullscreen mode:

```ini
[supplier_id]
    widget:type=mobile_lookup
    widget:table=Suppliers
    widget:keycol=id
    widget:labelcol=company_name,contact_name
    widget:searchFields=company_name,contact_name,city,country
    widget:allscreen=1
    widget:placeholder="Search supplier..."
```

This provides a better mobile experience with a fullscreen overlay.

### Optimized Mobile Search

```ini
[location_id]
    widget:type=mobile_lookup
    widget:table=ServiceLocations
    widget:keycol=id
    widget:labelcol=name,address
    widget:searchFields=name,address,postal_code
    widget:minimumInputLength=2
    widget:placeholder="Search location..."
    ; Add max-width for desktop
    widget:atts:maxWidth=500px
```

## Tips and Best Practices

1. **Use `preloadOptions=selected`** in grids and related records
2. **Set `minimumInputLength`** for large datasets
3. **Include relevant search fields** (codes, emails, phones)
4. **Use multi-column display** for better identification
5. **Add dynamic filters** to reduce result sets
6. **Set meaningful placeholders** for better UX
7. **Test on real mobile devices**, not just browser dev tools
8. **Consider fullscreen mode** for complex mobile lookups

## Need More Help?

Check out:
- [README.md](../README.md) - Full documentation
- [INSTALL.md](../INSTALL.md) - Installation guide
- [GitHub Issues](https://github.com/plcats/module-mobile_lookup/issues) - Report bugs
