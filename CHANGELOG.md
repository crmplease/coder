# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Class [`Config`](src/Config.php) with possibility to configure auto import classes.

### Removed
- Remove method [`Helper\NameNodeHelper::createNodeName()`](src/Helper/NameNodeHelper.php) because it did same as [`\PhpParser\Node\Name::prepareName()`](https://github.com/nikic/PHP-Parser/blob/v4.3.0/lib/PhpParser/Node/Name.php#L218).

### Fixed
- Fix convert constant to ast when auto import classes is enabled.
