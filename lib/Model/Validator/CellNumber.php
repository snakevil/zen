<?php
/**
 * 定义手机号码验证器组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen;

use Zen\Model as ZenModel;

/**
 * 手机号码验证器组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
class CellNumber extends ZenModel\Validator\Pattern
{
    /**
     * {@inheritdoc}
     *
     * @param string $attribute 属性名
     */
    public function __construct($attribute)
    {
        parent::__construct($attribute, '#^1([38]\d\d|5[0-35-9]\d|7[678]\d|70[059])\d{7}$#');
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $value 待验证地值
     * @return bool
     */
    public function verify($value)
    {
        try {
            return parent::verify($value);
        } catch (\Exception $ee) {
            throw new ExIllegalCellNumber($this->attribute, $value);
        }
    }
}
