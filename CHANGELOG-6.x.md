# Change Log for OXID eShop IDE helper

## v6.4.1 - 2025-05-21

### Fixed
- Wrong format of oxid.meta.php file generated because of broken smarty to twig translation [PR-4](https://github.com/OXID-eSales/oxid-eshop-ide-helper/pull/4)

## v6.4.0 - 2025-04-10

### Added
- PHPUnit v11 support

### Changed
- Updated `DirectoryScanner` to **skip hidden directories and files** (e.g., `.git`, `.cache`, `.env`).

### Removed
- PHPUnit v10 support

### Deprecated
- `HelpFactory` and protected methods in `Generator`

## v6.3.0 - 2024-10-14

### Changed
- Replace Smarty with Twig template engine

### Removed
- PHP v8.1 support

## v6.2.0 - 2024-03-15

### Added
- PHPUnit v10 support

### Removed
- PHPUnit v9 support

### Changed
- License update

### Deprecated
- Smarty support

## v6.1.0 - 2023-04-20

### Removed
- Dependency to webmozart/path-util

## v6.0.0 - 2022-10-28

### Removed
- PHP v7 support

