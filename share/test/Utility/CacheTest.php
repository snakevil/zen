<?php
/*
 * 静态缓存组件的单元测试。
 *
 * @author Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license LGPL-3.0+
 */

namespace snakevil\zen\Utility;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs;
use snakevil\zen\Utility\Cache as Unit;

/**
 * 静态缓存组件的单元测试。
 */
class CacheTest extends PHPUnit_Framework_TestCase
{
    private $vfs;

    private $path;

    private $unit;

    protected function setUp()
    {
        date_default_timezone_set('PRC');
        $this->vfs = vfs\vfsStream::setup('CacheTest', 0755, array());
        Unit::root($this->vfs->url());
        $this->path = '/'.implode('/', str_split(md5(microtime()), 8));
        $this->unit = new Unit($this->path);
    }

    public function testCheckExistance()
    {
        $this->assertFalse($this->unit->exists());
    }

    public function testGenerate()
    {
        $s_content = microtime();
        $this->unit->store($s_content);
        $this->assertFileExists($this->vfs->url().$this->path);
        $this->assertEquals($s_content, file_get_contents($this->vfs->url().$this->path));
    }

    /**
     * @expectedException snakevil\zen\Utility\ExCacheWriteDenied
     */
    public function testGenerateWriteDenied()
    {
        $p_sub = $this->vfs->url().'/'.time();
        mkdir($p_sub, 0500);
        Unit::root($p_sub);
        $this->unit->store(microtime());
    }

    public function testGenerateWithModificationTime()
    {
        $i_time = strtotime('-'.rand(1, 9).'mins');
        $this->unit->store(microtime(), $i_time);
        $this->assertFileExists($this->vfs->url().$this->path);
        clearstatcache();
        $this->assertEquals(filemtime($this->vfs->url().$this->path), $i_time);
    }

    public function testOverwritingUpdateModificationTime()
    {
        $i_time = time();
        $this->unit->store(microtime(), $i_time);
        sleep(1);
        $this->unit->store(microtime());
        clearstatcache();
        $this->assertGreaterThan($i_time, filemtime($this->vfs->url().$this->path));
    }

    public function testModificationTimeCannotBeFuture()
    {
        $i_time = strtotime('9hours');
        $this->unit->store(microtime(), $i_time);
        clearstatcache();
        $this->assertLessThan($i_time, filemtime($this->vfs->url().$this->path));
    }

    public function testPurgingRemoveEmptySubFolders()
    {
        $this->unit->store(microtime());
        $this->unit->purge();
        $a_parts = explode('/', $this->path);
        clearstatcache();
        $this->assertFalse(file_exists($this->vfs->url().'/'.$a_parts[1]));
    }

    public function testPurgingKeepSubFoldersWithOtherCache()
    {
        $this->unit->store(microtime());
        $a_parts = explode('/', $this->path);
        touch($this->vfs->url().'/'.$a_parts[1].'/123');
        $this->unit->purge();
        clearstatcache();
        $this->assertDirectoryExists($this->vfs->url().'/'.$a_parts[1]);
    }

    public function testExpiration()
    {
        $i_time = strtotime('1hour');
        $this->unit->store(microtime());
        $this->unit->expires($i_time);
        $a_parts = explode('/', $this->path);
        $a_parts[4] = '.'.$a_parts[4].'.expires';
        $p_expires = implode('/', $a_parts);
        clearstatcache();
        $this->assertFileExists($this->vfs->url().$p_expires);
        $this->assertEquals(filemtime($this->vfs->url().$p_expires), $i_time);
    }

    public function testPastExpirationMakePurge()
    {
        $i_time = strtotime('-1min');
        $this->unit->store(microtime());
        $this->unit->expires(i_time);
        $a_parts = explode('/', $this->path);
        clearstatcache();
        $this->assertFalse(file_exists($this->vfs->url().'/'.$a_parts[1]));
    }
}
