<?php
/**
 * 定义静态缓存组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Utility;

use Zen\Core as ZenCore;

/**
 * 静态缓存组件。
 */
class Cache extends ZenCore\Component
{
    /**
     * 缓存存储根目录。
     *
     * @var string
     */
    protected static $root = '.';

    /**
     * 缓存路径。
     *
     * @var string
     */
    protected $dir;

    /**
     * 缓存文件名。
     *
     * @var string
     */
    protected $name;

    /**
     * 设置根目录。
     *
     * @param string $dir
     */
    public static function root($dir)
    {
        self::$root = $dir;
    }

    /**
     * 构造函数。
     *
     * @param string $path
     */
    public function __construct($path)
    {
        if ('/' != $path[0]) {
            $path = '/'.$path;
        }
        $i_pos = strrpos($path, '/');
        $this->dir = substr($path, 0, $i_pos);
        $this->name = substr($path, 1 + $i_pos);
    }

    /**
     * 是否存在。
     *
     * @return bool
     */
    public function exists()
    {
        $p_cache = self::$root.$this->dir.'/'.$this->name;

        return is_file($p_cache);
    }

    /**
     * 保存数据。
     *
     * @param string               $content
     * @param \DateTime|string|int $modTime Optional.
     */
    public function store($content, $modTime = false)
    {
        $this->mkdir($this->dir);
        $p_cache = self::$root.$this->dir.'/'.$this->name;
        @file_put_contents($p_cache, $content);
        if (!is_file($p_cache)) {
            throw new ExCacheWriteDenied($this->dir);
        }
        chmod($p_cache, 0640);
        if (false !== $modTime) {
            touch($p_cache, min(time(), $this->mktime($modTime)));
        }
    }

    /**
     * 创建目录。
     *
     * @param string $dir
     */
    protected function mkdir($dir)
    {
        $s_parent = dirname($dir);
        $p_parent = self::$root.$s_parent;
        if ('/' != $s_parent && !file_exists($p_parent)) {
            $this->mkdir($s_parent);
        }
        if (!is_dir($p_parent)) {
            throw new ExCacheWriteDenied($s_parent);
        }
        @mkdir(self::$root.$dir, 0750);
    }

    /**
     * 转化时间至数值。
     *
     * @param \DateTime|string|int $time
     *
     * @return int
     */
    protected function mktime($time)
    {
        if ($time instanceof \DateTime) {
            return $time->getTimestamp();
        }
        if (is_string($time)) {
            return strtotime($time);
        }
        if (is_int($time)) {
            return $time;
        }

        return time();
    }

    /**
     * 删除缓存文件。
     */
    public function purge()
    {
        if (!$this->exists()) {
            return;
        }
        $p_dir = self::$root.$this->dir;
        unlink($p_dir.'/'.$this->name);
        $p_expires = $p_dir.'/.'.$this->name.'.expires';
        if (file_exists($p_expires)) {
            unlink($p_expires);
        }
        $this->rmdir($this->dir);
    }

    /**
     * 逐级删除空目录。
     *
     * @param string $dir
     */
    protected function rmdir($dir)
    {
        if ('/' == $dir) {
            return;
        }
        $p_dir = self::$root.$dir;
        if (2 == count(scandir($p_dir))) {
            @rmdir($p_dir);
            $this->rmdir(dirname($dir));
        }
    }

    /**
     * 设置过期时间。
     *
     * @param \DateTime|string|int $time
     */
    public function expires($time)
    {
        if (!$this->exists()) {
            return;
        }
        $i_time = $this->mktime($time);
        if ($i_time <= time()) {
            return $this->purge();
        }
        $p_expires = self::$root.$this->dir.'/.'.$this->name.'.expires';
        if (!touch($p_expires, $i_time)) {
            throw new ExCacheWriteDenied($this->dir);
        }
    }
}
