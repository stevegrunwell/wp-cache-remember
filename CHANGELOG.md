# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

* Bypass the caching operation if a callback either throws an Exception or returns a `WP_Error` object.

## [1.0.0] - 2018-02-16

Initial public release of the package, including the following functions:

* `wp_cache_remember()`
* `wp_cache_forget()`
* `remember_transient()`
* `forget_transient()`
* `remember_site_transient()`
* `forget_site_transient()`

[Unreleased]: https://github.com/stevegrunwell/wp-cache-remember/compare/master...develop
[1.0.0]: https://github.com/stevegrunwell/wp-cache-remember/releases/tag/v1.0.0
