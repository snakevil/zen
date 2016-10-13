<?php
/**
 * 定义当缓存文件无法写入时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Utility;

use snakevil\zen;

/**
 * 当缓存文件无法写入时抛出地异常。
 *
 * @method void __construct(string $dir, \Exception $prev = null) 构造函数
 */
final class ExCacheWriteDenied extends zen\Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '目录“%dir$s”不可写。';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @var string[]
     */
    protected static $contextSequence = array('dir');
}
