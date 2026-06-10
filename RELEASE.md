# Release Checklist

This document provides a checklist for releasing a new version of the Mobile Lookup module.

## Pre-Release Checklist

### Code Quality

- [ ] All tests pass (manual testing on mobile/desktop)
- [ ] No console errors in browser
- [ ] Code follows style guidelines
- [ ] All TODOs resolved or documented
- [ ] No debug code left (console.log, var_dump, etc.)

### Documentation

- [ ] README.md updated with new features
- [ ] CHANGELOG.md updated with version changes
- [ ] EXAMPLES.md updated if API changed
- [ ] INSTALL.md reviewed and up-to-date
- [ ] Inline code comments added/updated

### Version Updates

- [ ] Update `version.txt` with new version number
- [ ] Update `composer.json` version
- [ ] Update version number in main file docblocks
- [ ] Update year in LICENSE if needed

### Testing

- [ ] Tested on Xataface 2.x
- [ ] Tested on PHP 7.0, 7.4, 8.0
- [ ] Tested on Chrome, Firefox, Safari
- [ ] Tested on iOS Safari
- [ ] Tested on Android Chrome
- [ ] Tested in new record form
- [ ] Tested in edit record form
- [ ] Tested in grid/related records
- [ ] Tested with various configurations

## Release Process

### 1. Update Version Files

```bash
# Update version.txt
echo "X.Y.Z" > version.txt

# Update composer.json
# Edit manually or use jq
jq '.extra.xataface.version = "X.Y.Z"' composer.json > tmp.json && mv tmp.json composer.json
```

### 2. Update CHANGELOG.md

Add a new section at the top:

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Added
- New feature description

### Changed
- Changed feature description

### Fixed
- Bug fix description

[X.Y.Z]: https://github.com/plcats/module-mobile_lookup/releases/tag/vX.Y.Z
```

### 3. Commit Changes

```bash
git add .
git commit -m "Release version X.Y.Z"
git push origin main
```

### 4. Create Git Tag

```bash
git tag -a vX.Y.Z -m "Release version X.Y.Z"
git push origin vX.Y.Z
```

### 5. GitHub Release

The GitHub Action will automatically:
- Create release archives (.zip and .tar.gz)
- Extract changelog notes
- Create GitHub Release

Alternatively, create manually:
1. Go to GitHub Releases
2. Click "Create a new release"
3. Select tag `vX.Y.Z`
4. Title: "Version X.Y.Z"
5. Copy changelog from CHANGELOG.md
6. Attach archives if not auto-generated
7. Publish release

### 6. Post-Release

- [ ] Announce on Xataface forum
- [ ] Tweet about release (if applicable)
- [ ] Update documentation website
- [ ] Monitor GitHub issues for bugs

## Version Numbering

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (X.0.0): Breaking changes
- **MINOR** (x.Y.0): New features, backward compatible
- **PATCH** (x.y.Z): Bug fixes, backward compatible

### Examples

- `1.0.0` → `1.0.1`: Bug fix
- `1.0.1` → `1.1.0`: New feature (backward compatible)
- `1.1.0` → `2.0.0`: Breaking change (API change, PHP requirement change)

## Release Types

### Patch Release (X.Y.Z)

Minor bug fixes, no new features:
```bash
# Fix bug
git commit -m "Fix: Description of bug fix"
# Update version
echo "1.0.1" > version.txt
# Update CHANGELOG.md
# Tag and release
git tag -a v1.0.1 -m "Release version 1.0.1"
```

### Minor Release (X.Y.0)

New features, backward compatible:
```bash
# Add feature
git commit -m "Add: Description of new feature"
# Update version
echo "1.1.0" > version.txt
# Update CHANGELOG.md
# Tag and release
git tag -a v1.1.0 -m "Release version 1.1.0"
```

### Major Release (X.0.0)

Breaking changes:
```bash
# Make breaking changes
git commit -m "Breaking: Description of breaking change"
# Update version
echo "2.0.0" > version.txt
# Update CHANGELOG.md with migration notes
# Tag and release
git tag -a v2.0.0 -m "Release version 2.0.0"
```

## Hotfix Process

For critical bugs in production:

1. Create hotfix branch from tag:
   ```bash
   git checkout -b hotfix-1.0.1 v1.0.0
   ```

2. Fix the bug and test thoroughly

3. Update version and changelog

4. Commit and tag:
   ```bash
   git commit -m "Hotfix: Critical bug description"
   git tag -a v1.0.1 -m "Hotfix release 1.0.1"
   ```

5. Merge back to main:
   ```bash
   git checkout main
   git merge hotfix-1.0.1
   git push origin main
   git push origin v1.0.1
   ```

## Rollback Process

If a release has critical issues:

1. Delete the tag:
   ```bash
   git tag -d vX.Y.Z
   git push origin :refs/tags/vX.Y.Z
   ```

2. Delete GitHub Release

3. Fix issues and re-release with incremented patch version

## Distribution

The module can be distributed via:

1. **GitHub Releases**: Download .zip or .tar.gz
2. **Composer** (future): `composer require pbonzini/xataface-mobile-lookup`
3. **Direct Download**: Clone repository
4. **Packagist** (future): Once published

## Support Channels

After release, monitor:
- GitHub Issues
- Xataface Forum posts
- Email support requests

## Changelog Template

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Added
- New feature 1
- New feature 2

### Changed
- Changed behavior 1
- Updated dependency X to version Y

### Deprecated
- Feature X (will be removed in vN.0.0)

### Removed
- Removed deprecated feature Y

### Fixed
- Fixed bug #123: Description
- Fixed issue with mobile detection

### Security
- Fixed XSS vulnerability in widget rendering
- Updated dependency to patch security issue

[X.Y.Z]: https://github.com/plcats/module-mobile_lookup/releases/tag/vX.Y.Z
```

## Quick Release Commands

```bash
# Quick patch release
./scripts/release.sh patch

# Quick minor release
./scripts/release.sh minor

# Quick major release
./scripts/release.sh major
```

(Create `scripts/release.sh` script for automation)
