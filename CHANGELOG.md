# Release Notes for Rackspace Cloud Files for Craft CMS

## 1.1.0 - 2019-02-01

### Fixed
- Fixed an error that occurred when updating from Craft 2 to Craft 3.1 when using this plugin.

## 1.0.5 - 2018-04-30
**:warning: The "Subfolder" setting on Rackspace volumes was previously ignored. If you have this set, you'll need to manually move your files into the expected location in your Rackspace containers.**

### Fixed
- Fixed a bug where the subfolder settings was being ignored. ([#1](https://github.com/craftcms/rackspace/issues/1))

## 1.0.4 - 2018-01-02

### Added
- Rackspace Cloud Files volumes’ Base URL settings are now parsed for [aliases](http://www.yiiframework.com/doc-2.0/guide-concept-aliases.html) (e.g. `@web`).

## 1.0.3 - 2017-12-04

### Changed
- Loosened the Craft CMS version requirement to allow any 3.x version.

## 1.0.2 - 2017-08-15

### Changed
- Craft 3 Beta 24 compatibility.

### Fixed
- Fixed a bug where file operation were not possible on files that had filenames or paths that were not url-safe.

## 1.0.1 - 2017-02-17

### Fixed
- Fixed a bug where the asset bundle was trying to load a non-existing CSS file.
- Fixed compatibility with Craft >= 3.0.0-beta.4.

## 1.0.0 - 2017-02-06

Initial release.
