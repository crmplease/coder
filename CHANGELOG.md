# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[unreleased]: https://github.com/crmplease/coder/compare/0.1.0...HEAD
[0.1.0]: https://github.com/crmplease/coder/compare/0.0.1...0.1.0
[0.0.1]: https://github.com/crmplease/coder/releases/tag/0.0.1
