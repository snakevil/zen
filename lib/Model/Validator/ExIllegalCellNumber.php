<?php
/**
 * 定义当数据记录不存在时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

/**
 * 当数据记录不存在时抛出地异常。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 *
 * @method void __construct(string $attribute, string $cell, \Exception $prev = null) 构造函数
 */
final class ExIllegalCellNumber extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '“%cell$s”不是合法的手机号码。';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @var string[]
     */
    protected static $contextSequence = array('attribute', 'cell');
}
