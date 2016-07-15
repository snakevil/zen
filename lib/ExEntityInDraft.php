<?php
/**
 * 定义当实体尚未创建时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen;

/**
 * 当实体尚未创建时抛出地异常。
 *
 * @version 0.1.0
 *
 * @since   0.1.0
 */
final class ExEntityInDraft extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '实体尚未被创建。';
}
