# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.0] - 2021-02-15

### Added
- Add new tests

### Changed
- Update dependency rector/rector from ^0.8.56 to ^0.9.28 ([#41](https://github.com/crmplease/coder/pull/41))
- Update dependency phpstan/phpdoc-parser from ^0.4.9 to ^0.4.10 ([#41](https://github.com/crmplease/coder/pull/41))
- Update dependency symplify/set-config-resolver from ^8.3 to ^9.1 ([#41](https://github.com/crmplease/coder/pull/41))
- Update dependency symplify/smart-file-system from ^8.3 to ^9.1 ([#41](https://github.com/crmplease/coder/pull/41))
- Update dependency symplify/package-builder from ^8.3 to ^9.1 ([#41](https://github.com/crmplease/coder/pull/41))
- Update dev dependency phpunit/phpunit from ^9.4.3 to ^9.5 ([#41](https://github.com/crmplease/coder/pull/41))

## [0.2.0] - 2020-12-02

### Changed
- Update dependency rector/rector from ^0.7.1 to ^0.8.56 ([#38](https://github.com/crmplease/coder/pull/38))
- Update dev dependency phpunit/phpunit from ^9.0 to ^9.4.3 ([#38](https://github.com/crmplease/coder/pull/38))
- Fix next dependencies in [`composer.json`](composer.json) ([#39](https://github.com/crmplease/coder/pull/39)):
  - nikic/php-parser:^4.10
  - phpstan/phpdoc-parser:^0.4.9
  - symfony/dependency-injection:^5.2
  - symplify/set-config-resolver:^8.3
  - symplify/smart-file-system:^8.3
  - symplify/package-builder:^8.3
  - symfony/console:^5.2

## [0.1.1] - 2020-11-06

### Added
- Add closures support in next methods ([#36](https://github.com/crmplease/coder/pull/36)):
  - addToFileReturnArray
  - addToFileReturnArrayByKey
  - addToFileReturnArrayByOrder
  - addToReturnArray
  - addToReturnArrayByKey
  - addToReturnArrayByOrder

## [0.1.0] - 2020-06-15

### Added
- Class [`Config`](src/Config.php) with possibility to configure auto import classes, show progress bar and path to rector config ([#29](https://github.com/crmplease/coder/pull/29), [#31](https://github.com/crmplease/coder/pull/31)).
- Add support to remove trait from class `Coder::removeTraitFromClass()` ([#33](https://github.com/crmplease/coder/pull/33)).

### Removed
- Remove method [`Helper\NameNodeHelper::createNodeName()`](src/Helper/NameNodeHelper.php) because it did same as [`\PhpParser\Node\Name::prepareName()`](https://github.com/nikic/PHP-Parser/blob/v4.3.0/lib/PhpParser/Node/Name.php#L218) ([#29](https://github.com/crmplease/coder/pull/29)).
- Remove method [`Coder::setShowProgressBar()`](src/Coder.php). Now you can use [`Config::setShowProgressBar()`](src/Coder.php) ([#30](https://github.com/crmplease/coder/pull/30)).

### Fixed
- Fix convert constant to ast when auto import classes is enabled ([#29](https://github.com/crmplease/coder/pull/29)).
- Remove using global argv in [`RectorContainerConfigurator`](src/RectorContainerConfigurator.php) ([#34](https://github.com/crmplease/coder/pull/34)).

[unreleased]: https://github.com/crmplease/coder/compare/0.3.0...HEAD
[0.3.0]: https://github.com/crmplease/coder/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/crmplease/coder/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/crmplease/coder/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/crmplease/coder/compare/0.0.1...0.1.0
[0.0.1]: https://github.com/crmplease/coder/releases/tag/0.0.1
