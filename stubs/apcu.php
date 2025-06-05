<?php

/**
 * APCu User Cache Stubs
 * 
 * This file provides stubs for APCu (APCu User Cache) functions
 * to improve IDE autocompletion and static analysis.
 * 
 * @see https://www.php.net/manual/en/book.apcu.php
 */

/**
 * Cache a variable in the data store
 *
 * @param string|array $key Store the variable using this name. Keys are cache-unique,
 *                          so attempting to use apcu_store() to store data with a key that already exists
 *                          will not overwrite the existing data, and will instead return FALSE.
 *                          Behavior is undefined if key is an array.
 * @param mixed $var The variable to store
 * @param int $ttl Time To Live; store var in the cache for ttl seconds.
 *                 After the ttl has passed, the stored variable will be expunged from the cache.
 *                 If no ttl is supplied (or if the ttl is 0), the value will persist until
 *                 it is removed from the cache manually, or otherwise fails to exist in the cache.
 * @return bool|array Returns TRUE on success or FALSE on failure.
 *                    Second syntax returns array with error keys.
 * @since 4.0.0
 */
function apcu_store($key, $var, int $ttl = 0): bool|array {}

/**
 * Fetch a stored variable from the cache
 *
 * @param string|array $key The key used to store the value (with apcu_store()).
 *                          If an array is passed then each element is fetched and returned.
 * @param bool &$success Set to TRUE in success and FALSE in failure.
 * @return mixed The stored variable or array of variables on success; FALSE on failure
 * @since 4.0.0
 */
function apcu_fetch($key, bool &$success = null): mixed {}

/**
 * Checks if APCu key exists
 *
 * @param string|array $keys A string, or an array of strings, that contain keys.
 * @return bool|array Returns TRUE if the key exists, otherwise FALSE
 *                    Or if an array was passed to keys, then an array is returned that
 *                    contains all existing keys, or an empty array if none exist.
 * @since 4.0.0
 */
function apcu_exists($keys): bool|array {}

/**
 * Removes a stored variable from the cache
 *
 * @param string|array $key A key used to store the value as a string for a single key,
 *                          or as an array of strings for several keys,
 *                          or as an APCUIterator object.
 * @return bool|array Returns TRUE on success or FALSE on failure. For array of keys returns list of deleted keys.
 * @since 4.0.0
 */
function apcu_delete($key): bool|array {}

/**
 * Clears the APCu cache
 *
 * @return bool Returns TRUE always.
 * @since 4.0.0
 */
function apcu_clear_cache(): bool {}

/**
 * Retrieves APCu Shared Memory Allocation information
 *
 * @param bool $limited When set to FALSE (default) apcu_sma_info() will
 *                      return a detailed information about each segment.
 * @return array|false Array of Shared Memory Allocation data; FALSE on failure
 * @since 4.0.0
 */
function apcu_sma_info(bool $limited = false): array|false {}

/**
 * Retrieves cached information from APCu's data store
 *
 * @param bool $limited If limited is TRUE, the return value will exclude
 *                      the individual list of cache entries. This is useful when trying
 *                      to optimize calls for statistics gathering.
 * @return array|false Array of cached data (and meta-data) or FALSE on failure
 * @since 4.0.0
 */
function apcu_cache_info(bool $limited = false): array|false {}

/**
 * Increase a stored number
 *
 * @param string $key The key of the value being increased.
 * @param int $step The step, or value to increase.
 * @param bool &$success Optionally pass the success or fail boolean value to this referenced variable.
 * @param int $ttl Time To Live; store var in the cache for ttl seconds.
 *                 After the ttl has passed, the stored variable will be expunged from the cache.
 *                 If no ttl is supplied (or if the ttl is 0), the value will persist until
 *                 it is removed from the cache manually, or otherwise fails to exist in the cache.
 * @return int|false Returns the current value of key's value on success, or FALSE on failure
 * @since 4.0.0
 */
function apcu_inc(string $key, int $step = 1, bool &$success = null, int $ttl = 0): int|false {}

/**
 * Decrease a stored number
 *
 * @param string $key The key of the value being decreased.
 * @param int $step The step, or value to decrease.
 * @param bool &$success Optionally pass the success or fail boolean value to this referenced variable.
 * @param int $ttl Time To Live; store var in the cache for ttl seconds.
 *                 After the ttl has passed, the stored variable will be expunged from the cache.
 *                 If no ttl is supplied (or if the ttl is 0), the value will persist until
 *                 it is removed from the cache manually, or otherwise fails to exist in the cache.
 * @return int|false Returns the current value of key's value on success, or FALSE on failure
 * @since 4.0.0
 */
function apcu_dec(string $key, int $step = 1, bool &$success = null, int $ttl = 0): int|false {}

/**
 * Atomically fetch or generate a cache entry
 *
 * @param string $key Identity of cache entry
 * @param callable $generator A callable that accepts key as the only argument and returns the value to cache.
 * @param int $ttl Time To Live; store var in the cache for ttl seconds.
 *                 After the ttl has passed, the stored variable will be expunged from the cache.
 *                 If no ttl is supplied (or if the ttl is 0), the value will persist until
 *                 it is removed from the cache manually, or otherwise fails to exist in the cache.
 * @return mixed Returns the cached data on success, or FALSE on failure
 * @since 5.1.0
 */
function apcu_entry(string $key, callable $generator, int $ttl = 0): mixed {}

/**
 * Updates an old value with a new value
 *
 * @param string $key The key of the value being updated.
 * @param int $old The old value (the value currently stored).
 * @param int $new The new value to update to.
 * @return bool Returns TRUE on success or FALSE on failure.
 * @since 4.0.0
 */
function apcu_cas(string $key, int $old, int $new): bool {}

/**
 * Whether APCu is usable in the current environment
 *
 * @return bool Returns TRUE if APCu is usable in the current environment, FALSE otherwise
 * @since 5.0.0
 */
function apcu_enabled(): bool {}

/**
 * Get APCu key information
 *
 * @param string $key The key to get information about
 * @return array|null Returns an array containing information about the key, or NULL if the key doesn't exist
 * @since 5.1.0
 */
function apcu_key_info(string $key): ?array {}

/**
 * Cache a variable in the data store, only if it's not already stored
 *
 * @param string|array $key Store the variable using this name. Keys are cache-unique.
 * @param mixed $var The variable to store
 * @param int $ttl Time To Live
 * @return bool|array Returns TRUE if something has effectively been added into the cache, FALSE otherwise.
 *                    Second syntax returns array with error keys.
 * @since 4.0.0
 */
function apcu_add($key, $var, int $ttl = 0): bool|array {}

/**
 * APCuIterator class
 *
 * @since 4.0.0
 */
class APCUIterator implements Iterator
{
    /**
     * Constructs an APCUIterator iterator object
     *
     * @param string|array|null $search A PCRE regular expression that matches against APCu key names,
     *                                  either as a string for a single regular expression,
     *                                  or as an array of regular expressions.
     *                                  Or NULL to skip the search.
     * @param int $format The desired format, as configured with one or more of the APC_ITER_* constants.
     * @param int $chunk_size The chunk size. Must be a value greater than 0. The default value is 100.
     * @param int $list The type to list. Either pass in APC_LIST_ACTIVE or APC_LIST_INACTIVE.
     */
    public function __construct($search = null, int $format = APC_ITER_ALL, int $chunk_size = 100, int $list = APC_LIST_ACTIVE) {}

    /**
     * Rewinds the iterator
     *
     * @return void
     */
    public function rewind(): void {}

    /**
     * Checks if current position is valid
     *
     * @return bool Returns TRUE if the current iterator position is valid, otherwise FALSE
     */
    public function valid(): bool {}

    /**
     * Gets the current item
     *
     * @return mixed Returns the current item on success, or FALSE if no more items or error
     */
    public function current(): mixed {}

    /**
     * Gets the current iterator key
     *
     * @return string|int|false Returns the current iterator key on success, or FALSE on failure
     */
    public function key(): string|int|false {}

    /**
     * Moves the iterator to the next entry
     *
     * @return void
     */
    public function next(): void {}

    /**
     * Gets the total number of cache entries
     *
     * @return int|false Returns the total number of cache entries on success, or FALSE on failure
     */
    public function getTotalCount(): int|false {}

    /**
     * Get the total number of cache hits
     *
     * @return int|false Returns the number of hits on success, or FALSE on failure
     */
    public function getTotalHits(): int|false {}

    /**
     * Gets the total cache size
     *
     * @return int|false Returns the total cache size on success, or FALSE on failure
     */
    public function getTotalSize(): int|false {}
}

/**
 * APCu constants
 */

/** @since 4.0.0 */
const APC_ITER_TYPE = 1;

/** @since 4.0.0 */
const APC_ITER_KEY = 2;

/** @since 4.0.0 */
const APC_ITER_VALUE = 4;

/** @since 4.0.0 */
const APC_ITER_NUM_HITS = 8;

/** @since 4.0.0 */
const APC_ITER_MTIME = 16;

/** @since 4.0.0 */
const APC_ITER_CTIME = 32;

/** @since 4.0.0 */
const APC_ITER_DTIME = 64;

/** @since 4.0.0 */
const APC_ITER_ATIME = 128;

/** @since 4.0.0 */
const APC_ITER_REFCOUNT = 256;

/** @since 4.0.0 */
const APC_ITER_MEM_SIZE = 512;

/** @since 4.0.0 */
const APC_ITER_TTL = 1024;

/** @since 4.0.0 */
const APC_ITER_NONE = 0;

/** @since 4.0.0 */
const APC_ITER_ALL = -1;

/** @since 4.0.0 */
const APC_LIST_ACTIVE = 1;

/** @since 4.0.0 */
const APC_LIST_INACTIVE = 2;
