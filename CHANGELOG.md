# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-06-10

### Added
- Auto-focus sulla casella di ricerca all'apertura del dropdown (standard e modalità `allscreen`)
- Ricerca multi-parola con logica **AND** tra token (allineata a `Dataface_QueryBuilder` / lookup standard)
  - Esempio: `tubol 120` trova "tubolare 120 x 40"
- Documentazione migrazione `lookup` → `mobile_lookup` (esempi BBG Point)

### Changed
- `actions/mobile_lookup_search.php`: ogni parola della query deve matchare almeno un `searchField` (OR tra campi, AND tra parole)
- `js/mobile-lookup.js`: helper `focusLookupSearchField()` con `preventScroll` per mobile

### Fixed
- Comportamento ricerca meno permissivo della lookup nativa quando l'utente digita più parole separate da spazio
- Focus ricerca in modalità fullscreen dopo mount del pannello (`requestAnimationFrame`)
- Testo selezionato troncato troppo presto (`COMP...` con spazio vuoto): clear Select2 (×) non usa più `float:right` nel layout

[1.1.0]: https://github.com/plcats/module-mobile_lookup/releases/tag/v1.1.0

## [1.0.0] - 2026-02-05

### Added
- 🎉 Initial release
- Mobile-friendly lookup widget using Select2 4.1.0-rc.0
- Touch-optimized interface with 44px touch targets (iOS standard)
- AJAX search with server-side pagination (30 records per page)
- Responsive design for mobile/tablet/desktop
- Security features: prepared statements, input validation, permission checks
- iOS-friendly: 16px font to prevent auto-zoom
- Dynamic filters support with `$fieldName` resolution
- Full Italian localization
- Edit/New buttons for selected records
- Custom CSS mobile-optimized
- Xataface decorator pattern integration
- HTML QuickForm custom element
- `preloadOptions` parameter for grid performance optimization
- Support for multiple label columns with `CONCAT_WS`
- Xataface `titleColumn()` support for automatic label resolution
- Dark mode support (forced light theme for consistency)
- Fullscreen modal mode for mobile devices (`widget:allscreen=1`)

### Features
- Select2 4.1.0-rc.0 CDN integration
- Native Xataface database connection via `df_db()`
- AJAX action for record search (`mobile_lookup_search`)
- Permission checks for edit/new buttons
- Search across multiple fields
- Custom placeholders
- Minimum input length configuration
- Client-side caching

### Technical
- Compatible with Xataface 2.x
- Requires PHP 7.0+
- MySQL/MariaDB support
- No external dependencies except Select2 (loaded from CDN)

[1.0.0]: https://github.com/plcats/module-mobile_lookup/releases/tag/v1.0.0
