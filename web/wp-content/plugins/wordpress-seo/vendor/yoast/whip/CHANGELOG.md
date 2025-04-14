# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] - 2023-12-28
### Added
* Tested against PHP 8.3. [#138], [#150]

### Changed
* All the source classes are now namespaced under `Yoast\WHIPv2`. The version number in the namespaced will be bumped up with every major version. [#157]
    The classes have also been renamed to remove the `Whip_` prefix, and the folders' names have been capitalized to follow the PSR-4 standard.
* The `Requirement` interface now explicitly declares the following two additional methods: `version() ` and `operator()` and classes implementing the interface should ensure these methods are available. [#146]
* General housekeeping.

### Removed
* The deprecated `Whip_WPMessagePresenter:register_hooks()` method has been removed. [#158]

### Fixed
* Compatibility with PHP >= 8.2: prevent a deprecation notice about dynamic properties usage from being thrown in the `RequirementsChecker` class. [#117]
* Security hardening: added sanitization to the notification dismiss action. [#131]

[#158]: https://github.com/Yoast/whip/pull/158
[#157]: https://github.com/Yoast/whip/pull/157
[#150]: https://github.com/Yoast/whip/pull/150
[#146]: https://github.com/Yoast/whip/pull/146
[#138]: https://github.com/Yoast/whip/pull/138
[#131]: https://github.com/Yoast/whip/pull/131
[#117]: https://github.com/Yoast/whip/pull/117

## [1.2.0] - 2021-07-20

:warning: This version drops support for PHP 5.2!

### Changed
* PHP 5.2 is no longer supported. The minimum supported PHP version for the WHIP library is now PHP 5.3. [#96]
* The previous solution to prevent duplicate messages as included in v1.0.2 has been improved upon and made more stable. Props [Drew Jaynes]. [#44]
* The `Whip_InvalidOperatorType::__construct()` method now has a second, optional `$validOperators` parameter. [#62]
    If this parameter is not passed, the default set of valid operators, as was used before, will be used.
* Improved protection against XSS in localizable texts. [#50]
* Improved support for translating localizable texts (I18n). [#59]
* The distributed package will no longer contain development-related files. [#45]
* General housekeeping.

### Deprecated
* The `public` `Whip_WPMessagePresenter:register_hooks()` method has been deprecated in favour of the new `Whip_WPMessagePresenter:registerHooks()`. [#52], [#107]

### Fixed
* The text of the exception message thrown via the `Whip_InvalidType` exception was sometimes garbled. [#61]
* Compatibility with PHP >= 7.4: prevent a deprecation notice from being thrown (fatal error on PHP 8.0). [#88]

[#44]:  https://github.com/Yoast/whip/pull/44
[#45]:  https://github.com/Yoast/whip/pull/45
[#50]:  https://github.com/Yoast/whip/pull/50
[#52]:  https://github.com/Yoast/whip/pull/52
[#59]:  https://github.com/Yoast/whip/pull/59
[#61]:  https://github.com/Yoast/whip/pull/61
[#62]:  https://github.com/Yoast/whip/pull/62
[#88]:  https://github.com/Yoast/whip/pull/88
[#96]:  https://github.com/Yoast/whip/pull/96
[#107]: https://github.com/Yoast/whip/pull/107

[Drew Jaynes]: https://github.com/DrewAPicture

## [1.1.0] - 2017-08-08
### Added
* Allow WordPress messages to be dismissed for a period of 4 weeks.

## [1.0.2] - 2017-06-27
### Fixed
* When multiple plugins containing whip are activated, the message is no longer shown multiple times, props [Andrea](https://github.com/sciamannikoo).

## [1.0.1] - 2017-03-21
### Fixed
* Fix a missing link when the PHP message is switched to the WordPress.org hosting page.

## [1.0.0] - 2017-03-21
### Changed
* Updated screenshot in README

## [1.0.0-beta.2] - 2017-03-11
### Added
* Complete PHP version message

### Changed
* Refactor code architecture.
* Use PHP version constant instead of function.

### Fixed
* Fix broken version reconciliation.

## 1.0.0-beta.1 - 2017-02-21
* Initial pre-release of whip. A package to nudge users to upgrade their software versions.

[Unreleased]: https://github.com/yoast/whip/compare/1.2.0...HEAD
[1.2.0]: https://github.com/yoast/whip/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/yoast/whip/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/yoast/whip/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/yoast/whip/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/yoast/whip/compare/1.0.0-beta.2...1.0.0
[1.0.0-beta.2]: https://github.com/yoast/whip/compare/1.0.0-beta.1...1.0.0-beta.2
