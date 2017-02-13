<?php
namespace Core\Mvc\Model;

use Core\Caches\CacheInterface;

/**
 * Trait CacheTrait
 *
 * @property CacheInterface $cache
 */
trait CacheTrait
{
    protected $cachePoolName = 'default';
    protected $cacheExpired = 3600;
    protected $cacheExpiredQuick = 1800;

    protected function getCacheKey()
    {
        $args = func_get_args();

        if (isset($this->table)) {
            array_unshift($args, $this->table);
        }
        if (isset($this->dbname)) {
            array_unshift($args, $this->dbname);
        }

        return implode(':', $args);
    }
}
