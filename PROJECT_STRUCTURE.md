# Project Structure

This document describes the structure of the Mobile Lookup module repository.

## Directory Tree

```
mobile_lookup/
│
├── .github/                           # GitHub configuration
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md             # Bug report template
│   │   └── feature_request.md        # Feature request template
│   ├── workflows/
│   │   ├── ci.yml                    # Continuous Integration
│   │   └── release.yml               # Automated releases
│   └── PULL_REQUEST_TEMPLATE.md      # PR template
│
├── actions/                           # Xataface actions
│   └── mobile_lookup_search.php      # AJAX search endpoint
│
├── css/                               # Stylesheets
│   └── mobile-lookup.css             # Main widget styles
│
├── docs/                              # Documentation
│   ├── images/                        # Screenshots and images
│   │   └── .gitkeep
│   ├── EXAMPLES.md                    # Usage examples
│   └── README.md                      # Documentation index
│
├── HTML/QuickForm/                    # HTML QuickForm integration
│   └── mobile_lookup.php             # QuickForm element class
│
├── js/                                # JavaScript files
│   └── mobile-lookup.js              # Main widget logic
│
├── .gitignore                         # Git ignore rules
├── actions.ini.php                    # Xataface action registration
├── CHANGELOG.md                       # Version history
├── composer.json                      # Composer package metadata
├── CONTRIBUTING.md                    # Contribution guidelines
├── INSTALL.md                         # Installation guide
├── LICENSE                            # GNU GPL v2 license
├── mobile_lookup.php                  # Main module file
├── PROJECT_STRUCTURE.md               # This file
├── QuickForm_mobile_lookup.php        # Legacy QuickForm wrapper
├── README.md                          # Main documentation
├── RELEASE.md                         # Release process guide
├── version.txt                        # Current version number
└── widget.php                         # Widget handler (FormTool)
```

## File Descriptions

### Core Files

| File | Purpose | Critical |
|------|---------|----------|
| `mobile_lookup.php` | Main module initialization, registers widget handler | ✅ Yes |
| `widget.php` | FormTool widget handler, builds the widget | ✅ Yes |
| `HTML/QuickForm/mobile_lookup.php` | QuickForm element, renders HTML | ✅ Yes |
| `js/mobile-lookup.js` | Frontend JavaScript, Select2 integration | ✅ Yes |
| `css/mobile-lookup.css` | Widget styles, mobile optimizations | ✅ Yes |
| `actions/mobile_lookup_search.php` | AJAX search handler | ✅ Yes |

### Configuration Files

| File | Purpose |
|------|---------|
| `actions.ini.php` | Registers AJAX actions with Xataface |
| `composer.json` | Package metadata for Composer |
| `version.txt` | Current version number |
| `.gitignore` | Git ignore patterns |

### Documentation Files

| File | Purpose |
|------|---------|
| `README.md` | Main documentation, features, usage |
| `INSTALL.md` | Installation instructions |
| `CONTRIBUTING.md` | Contribution guidelines |
| `CHANGELOG.md` | Version history and changes |
| `RELEASE.md` | Release process documentation |
| `docs/EXAMPLES.md` | Real-world usage examples |
| `docs/README.md` | Documentation index |
| `PROJECT_STRUCTURE.md` | This file |

### GitHub Files

| File | Purpose |
|------|---------|
| `.github/ISSUE_TEMPLATE/bug_report.md` | Bug report template |
| `.github/ISSUE_TEMPLATE/feature_request.md` | Feature request template |
| `.github/PULL_REQUEST_TEMPLATE.md` | Pull request template |
| `.github/workflows/ci.yml` | Continuous Integration workflow |
| `.github/workflows/release.yml` | Automated release workflow |

### Legacy Files

| File | Purpose | Status |
|------|---------|--------|
| `QuickForm_mobile_lookup.php` | Old QuickForm wrapper | Deprecated, kept for compatibility |

## Key Components

### 1. Module Initialization (`mobile_lookup.php`)

- Loads dependencies (XataJax)
- Registers widget with FormTool
- Provides base URL for assets

### 2. Widget Handler (`widget.php`)

- `buildWidget()`: Constructs widget from field definition
- `pullValue()`: Extracts value from record to form
- `pushValue()`: Extracts value from form to record
- Handles preload strategies (selected, first100, none)
- Resolves dynamic filters

### 3. QuickForm Element (`HTML/QuickForm/mobile_lookup.php`)

- Extends `HTML_QuickForm_select`
- Renders HTML `<select>` with data attributes
- Includes CSS/JS resources
- Adds edit/new buttons

### 4. Frontend JavaScript (`js/mobile-lookup.js`)

- Loads Select2 from CDN
- Initializes widgets via decorator pattern
- Handles AJAX search
- Resolves dynamic filters from form fields
- Mobile optimizations (fullscreen mode)
- Event handlers (select, open, close)

### 5. CSS Styles (`css/mobile-lookup.css`)

- Mobile-first design
- Touch target sizes (44px)
- Responsive breakpoints
- Fullscreen modal styles
- Select2 customizations

### 6. AJAX Handler (`actions/mobile_lookup_search.php`)

- Processes search requests
- Server-side pagination (30 records/page)
- Security: SQL injection prevention, permission checks
- Supports filters and search across multiple fields
- Returns JSON response

## Data Flow

### Widget Rendering

```
1. Xataface loads form
2. FormTool calls widget.php::buildWidget()
3. widget.php creates QuickForm element
4. HTML/QuickForm/mobile_lookup.php::toHtml()
5. HTML rendered with data attributes
6. JavaScript decorator initializes Select2
```

### AJAX Search

```
1. User types in search box
2. Select2 triggers AJAX request
3. actions/mobile_lookup_search.php receives request
4. Query database with filters
5. Return JSON results
6. Select2 displays results
7. User selects option
8. Form updated with selected value
```

## Dependencies

### External Dependencies

- **Select2 4.1.0-rc.0**: Loaded from CDN
- **jQuery**: Required by Select2 (provided by Xataface)

### Xataface Dependencies

- **XataJax module**: For JavaScript/CSS tools
- **FormTool**: Widget registration
- **HTML QuickForm**: Form element system
- **Dataface_Table**: Table metadata
- **df_db()**: Database connection

## Development Workflow

### Adding a New Feature

1. Create feature branch: `git checkout -b feature/your-feature`
2. Implement feature in appropriate files
3. Update documentation (README.md, EXAMPLES.md)
4. Test on mobile and desktop
5. Update CHANGELOG.md
6. Create pull request

### Fixing a Bug

1. Create bugfix branch: `git checkout -b fix/bug-description`
2. Fix the bug
3. Test thoroughly
4. Update CHANGELOG.md
5. Create pull request

### Making a Release

1. Follow checklist in RELEASE.md
2. Update version.txt and composer.json
3. Update CHANGELOG.md
4. Commit and tag
5. Push tag to trigger GitHub Actions

## Testing Strategy

### Manual Testing

- [ ] Desktop browsers (Chrome, Firefox, Safari, Edge)
- [ ] Mobile browsers (iOS Safari, Chrome Mobile)
- [ ] New record form
- [ ] Edit record form
- [ ] Grid/related records
- [ ] AJAX search
- [ ] Dynamic filters
- [ ] Edit/new buttons
- [ ] Fullscreen mode (mobile)

### Browser Testing Matrix

| Browser | Desktop | Mobile |
|---------|---------|--------|
| Chrome | ✅ | ✅ |
| Firefox | ✅ | ✅ |
| Safari | ✅ | ✅ iOS |
| Edge | ✅ | N/A |

## Maintenance

### Regular Tasks

- Monitor GitHub issues
- Review pull requests
- Update dependencies (Select2 version)
- Test with new Xataface versions
- Update documentation

### Security Updates

- Review code for vulnerabilities
- Update dependencies if security issues found
- Test SQL injection prevention
- Test XSS prevention

## Build/Deployment

### No Build Step Required

This module doesn't require compilation or bundling. Files are used directly.

### Deployment

1. Copy module to `modules/mobile_lookup/`
2. Enable in `conf.ini`
3. Clear Xataface cache

### CDN Dependencies

Select2 is loaded from CDN:
- CSS: `https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css`
- JS: `https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js`

For offline installations, download and host locally.

## License

GNU GPL v2 - See [LICENSE](LICENSE)

---

**Last Updated**: February 2026  
**Maintainer**: Paolo Bonzini
