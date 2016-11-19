<?php
/**
 * 定义抽象模型集合组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

use Zen\Model as ZenModel;

abstract class Set extends ZenModel\Set
{
    /**
     * 清除所有已获取地模型实例。
     *
     * @return self
     */
    protected function clear()
    {
        $this->items = array();
        $this->cursor = -1;

        return $this;
    }
}
