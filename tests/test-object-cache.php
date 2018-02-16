<?php
/**
 * Tests for the object cache functions
 *
 * @package Wp_Cache_Remember
 */

/**
 * Sample test case.
 */
class ObjectCacheTest extends WP_UnitTestCase {

	function test_remembers_value() {
		$key      = 'some-cache-key-' . uniqid();
		$callback = function () {
			return uniqid();
		};

		$value = wp_cache_remember( $key, $callback );

		$this->assertEquals(
			$value,
			wp_cache_remember( $key, $callback ),
			'Expected the same value to be returned on subsequent requests.'
		);
		$this->assertEquals( $value, wp_cache_get( $key ) );
	}

	function test_remember_pulls_from_cache() {
		$key   = 'some-cache-key-' . uniqid();
		$value = uniqid();

		wp_cache_set( $key, $value );

		$this->assertEquals(
			$value,
			wp_cache_remember( $key, '__return_false' ),
			'Expected the cache value to be returned.'
		);
	}

	function test_forget_deletes_cached_item() {
		$key = 'some-cache-key-' . uniqid();

		wp_cache_set( $key, 'some value' );

		$this->assertEquals( 'some value', wp_cache_forget( $key ), 'Expected to receive the cached value.' );
		$this->assertFalse( wp_cache_get( $key ), 'Expected the cached value to be removed.' );
	}

	function test_forget_falls_back_to_default() {
		$key = 'some-cache-key-' . uniqid();

		$this->assertEquals( 'some value', wp_cache_forget( $key, null, 'some value' ), 'Expected to receive the default value.' );
		$this->assertFalse( wp_cache_get( $key ), 'Expected the cached value to remain empty.' );
	}
}
