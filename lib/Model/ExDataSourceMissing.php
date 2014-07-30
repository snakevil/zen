<?php
/**
 * 定义当数据源未绑定时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

/**
 * 当数据源未绑定时抛出地异常。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 *
 * @method void __construct(\Exception $prev = null) 构造函数
 */
final class ExDataSourceMissing extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '数据源未绑定。';
}
