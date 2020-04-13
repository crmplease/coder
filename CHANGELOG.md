# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Class [`Config`](src/Config.php) with possibility to configure auto import classes, show progress bar and path to rector config.

### Removed
- Remove method [`Helper\NameNodeHelper::createNodeName()`](src/Helper/NameNodeHelper.php) because it did same as [`\PhpParser\Node\Name::prepareName()`](https://github.com/nikic/PHP-Parser/blob/v4.3.0/lib/PhpParser/Node/Name.php#L218).
- Remove method [`Coder::setShowProgressBar()`](src/Coder.php). Now you can use [`Config::setShowProgressBar()`](src/Coder.php).

### Fixed
- Fix convert constant to ast when auto import classes is enabled.
