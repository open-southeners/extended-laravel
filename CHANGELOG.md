# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2024-09-09

### Fixed

- Command `make:migration` broken from container injection

### Added

- Number mixin methods `toShort` to make numbers shorter

## [0.3.0] - 2024-08-19

### Added

- `Helpers::getCacheLockOwner` method to get cache atomic locks owner or false otherwise if no lock found

## [0.2.1] - 2024-08-16

### Fixed

- Namespace for `OpensGeneratedFiles` trait

## [0.2.0] - 2024-08-16

### Added

- `OpensGeneratedFiles` console utility class that opens the generated files with the IDE configured (through `APP_IDE` environment variable)

### Changed

- All Laravel framework's commands that extends from `GeneratorCommand` now got replaced from the container using now `FileGeneratorCommand` (includes all `artisan make:` commands)

### Fixed

- `Stringable::emailDomain()` requiring an argument when none needed usable as `Str::of('joe@example.org')->emailDomain()->value()`

## [0.1.3] - 2024-08-16

### Fixed

- Composer PSR autoloading with models functions (moved to the `OpenSoutheners\ExtendedLaravel\Helpers` class)

## [0.1.2] - 2024-08-16

### Fixed

- Registering commands that doesn't exists

## [0.1.0] - 2024-08-15

### Added

- Initial release!
