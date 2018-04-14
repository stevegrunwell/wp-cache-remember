# WP Cache Remember

[![Build Status](https://travis-ci.org/stevegrunwell/wp-cache-remember.svg?branch=develop)](https://travis-ci.org/stevegrunwell/wp-cache-remember)
[![Coverage Status](https://coveralls.io/repos/github/stevegrunwell/wp-cache-remember/badge.svg?branch=develop)](https://coveralls.io/github/stevegrunwell/wp-cache-remember?branch=develop)
[![GitHub release](https://img.shields.io/github/release/stevegrunwell/wp-cache-remember.svg)](https://github.com/stevegrunwell/wp-cache-remember/releases)

WP Cache Remember is a simple WordPress include to introduce convenient new caching functions.

Well-built WordPress plugins know when to take advantage of the object cache and/or transients, but they often end up with code that looks like this:

```php
function do_something() {
    $cache_key = 'some-cache-key';
    $cached    = wp_cache_get( $cache_key );

    // Return the cached value.
    if ( $cached ) {
        return $cached;
    }

    // Do all the work to calculate the value.
    $value = a_whole_lotta_processing();

    // Cache the value.
    wp_cache_set( $cache_key, $value );

    return $value;
}
```

That pattern works well, but there's a lot of repeated code. This package draws inspiration from [Laravel's `Cache::remember()` method](https://laravel.com/docs/5.6/cache#cache-usage); using `wp_cache_remember()`, the same code from above becomes:

```php
function do_something() {
    return wp_cache_remember( 'some-cache-key', function () {
        return a_whole_lotta_processing();
    } );
}
```

## Installation

The best way to install this package is [via Composer](https://getcomposer.org/):

```sh
$ composer require stevegrunwell/wp-cache-remember
```

The package ships with the [`composer/installers` package](https://github.com/composer/installers), enabling you to control where you'd like the package to be installed. For example, if you're using WP Cache Remember in a WordPress plugin, you might store the file in an `includes/` directory. To accomplish this, add the following to your plugin's `composer.json` file:

```json
{
    "extra": {
        "installer-paths": {
            "includes/{$name}/": ["stevegrunwell/wp-cache-remember"]
        }
    }
}
```

Then, from within your plugin, simply include or require the file:

```php
require_once __DIR__ . '/includes/wp-cache-remember/wp-cache-remember.php';
```

### Using as a plugin

If you'd prefer, the package also includes the necessary file headers to be used as a WordPress plugin.

After downloading or cloning the package, move `wp-cache-remember.php` into either your `wp-content/mu-plugins/` (preferred) or `wp-content/plugins/` directory. If you chose the regular plugins directory, you'll need to activate the plugin manually via the Plugins &rsaquo; Installed Plugins page within WP Admin.

### Bundling within a plugin or theme

WP Cache Remember has been built in a way that it can be easily bundled within a WordPress plugin or theme, even commercially.

Each function declaration is wrapped in appropriate `function_exists()` checks, ensuring that multiple copies of the library can co-exist in the same WordPress environment.

## Usage

WP Cache Remember provides the following functions for WordPress:

* [`wp_cache_remember()`](#wp_cache_remember)
* [`wp_cache_forget()`](#wp_cache_forget)
* [`remember_transient()`](#remember_transient)
* [`forget_transient()`](#forget_transient)
* [`remember_site_transient()`](#remember_site_transient)
* [`forget_site_transient()`](#forget_site_transient)

Each function checks the response of the callback for a `WP_Error` object, ensuring you're not caching temporary errors for long periods of time. PHP Exceptions will also not be cached.

### wp_cache_remember()

Retrieve a value from the object cache. If it doesn't exist, run the `$callback` to generate and cache the value.

#### Parameters

<dl>
    <dt>(string) $key</dt>
    <dd>The cache key.</dd>
    <dt>(callable) $callback</dt>
    <dd>The callback used to generate and cache the value.</dd>
    <dt>(string) $group</dt>
    <dd>Optional. The cache group. Default is empty.</dd>
    <dt>(int) $expire</dt>
    <dd>Optional. The number of seconds before the cache entry should expire. Default is 0 (as long as possible).</dd>
</dl>

#### Example

```php
function get_latest_posts() {
    return wp_cache_remember( 'latest_posts', function () {
        return new WP_Query( array(
            'posts_per_page' => 5,
            'orderby'        => 'post_date',
            'order'          => 'desc',
        ) );
    }, 'my-cache-group', HOUR_IN_SECONDS );
}
```

### wp_cache_forget()

Retrieve and subsequently delete a value from the object cache.

#### Parameters

<dl>
    <dt>(string) $key</dt>
    <dd>The cache key.</dd>
    <dt>(string) $group</dt>
    <dd>Optional. The cache group. Default is empty.</dd>
    <dt>(mixed) $default</dt>
    <dd>Optional. The default value to return if the given key doesn't exist in the object cache. Default is null.</dd>
</dl>

#### Example

```php
function show_error_message() {
    $error_message = wp_cache_forget( 'form_errors', 'my-cache-group', false );

    if ( $error_message ) {
        echo 'An error occurred: ' . $error_message;
    }
}
```

### remember_transient()

Retrieve a value from transients. If it doesn't exist, run the `$callback` to generate and cache the value.

#### Parameters

<dl>
    <dt>(string) $key</dt>
    <dd>The cache key.</dd>
    <dt>(callable) $callback</dt>
    <dd>The callback used to generate and cache the value.</dd>
    <dt>(int) $expire</dt>
    <dd>Optional. The number of seconds before the cache entry should expire. Default is 0 (as long as possible).</dd>
</dl>

#### Example

```php
function get_tweets() {
    $user_id = get_current_user_id();
    $key     = 'latest_tweets_' . $user_id;

    return remember_transient( $key, function () use ( $user_id ) {
        return get_latest_tweets_for_user( $user_id );
    }, 15 * MINUTE_IN_SECONDS );
}
```

### forget_transient()

Retrieve and subsequently delete a value from the transient cache.

#### Parameters

<dl>
    <dt>(string) $key</dt>
    <dd>The cache key.</dd>
    <dt>(mixed) $default</dt>
    <dd>Optional. The default value to return if the given key doesn't exist in transients. Default is null.</dd>
</dl>

### remember_site_transient()

Retrieve a value from site transients. If it doesn't exist, run the `$callback` to generate and cache the value.

This function shares arguments and behavior with [`remember_transient()`](#remember_transient), but works network-wide when using WordPress Multisite.

### forget_site_transient()

Retrieve and subsequently delete a value from the site transient cache.

This function shares arguments and behavior with [`forget_transient()`](#forget_transient), but works network-wide when using WordPress Multisite.

## License

Copyright 2018 Steve Grunwell

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
