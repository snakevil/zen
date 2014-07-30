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
 * @method void __construct(string $table, scalar $id, \Exception $prev = null) 构造函数
 */
final class ExRecordNotFound extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '数据表“%table$s”中不存在编号为“%id$s”地记录。';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @var string[]
     */
    protected static $contextSequence = array('table', 'id');
}
