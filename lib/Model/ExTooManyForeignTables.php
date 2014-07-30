<?php
/**
 * 定义当外联表超过一张时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

/**
 * 当外联表超过一张时抛出地异常。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 *
 * @method void __construct(string $table, scalar $id, \Exception $prev = null) 构造函数
 */
final class ExTooManyForeignTables extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '查询表“%table$s”时附带了过多的外联表。';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @var string[]
     */
    protected static $contextSequence = array('table', 'conditions');
}
