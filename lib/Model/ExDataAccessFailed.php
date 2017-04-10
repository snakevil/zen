<?php
/**
 * 定义当数据操作失败时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2017 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

/**
 * 当数据源未绑定时抛出地异常。
 *
 * @method void __construct(\Exception $prev = null) 构造函数
 */
final class ExDataSourceMissing extends Exception
{
    /**#@+
     * @ignore
     */
    protected static $template = '数据库错误：%error$s';

    protected static $contextSequence = array('sql', 'params', 'error');
    /**#@-*/
}
