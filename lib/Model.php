<?php
/**
 * 定义抽象模型组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen;

use Zen\Model as ZenModel;

/**
 * 抽象模型组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
abstract class Model extends ZenModel\Model
{
    /**
     * 相关模型集合池。
     *
     * @var ZenModel\ISet[]
     */
    protected $pools;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function onSave()
    {
        if (!$this->__toString()) {
            $this->id = null;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    protected function listNonAttributes()
    {
        $a_ret = parent::listNonAttributes();
        $a_ret[] = 'pools';

        return $a_ret;
    }

    /**
     * 转换属性值类型。
     *
     * @param  string $property 属性名
     * @param  string $type     目标类名
     * @return void
     */
    final protected function castType($property, $type)
    {
        if (!$this->$property instanceof $type) {
            if (is_subclass_of($type, 'Zen\Model\IModel')) {
                $this->$property = $type::load($this->$property);
            } else {
                $this->$property = new $type($this->$property);
            }
        }
    }

    /**
     * 获取指定地集合。
     *
     * 当该集合不存在或强制更新时，则使用备选集合。
     *
     * @param  scalar       $key    键名
     * @param  ZenModel\Set $alter  备选集合
     * @param  bool         $update 可选。是否更新指定集合
     * @return ZenModel\Set
     */
    protected function fetchSet($key, ZenModel\Set $alter, $update = false)
    {
        if ($update || !isset($this->pools[$key])) {
            $this->pools[$key] = $alter;
        }

        return $this->pools[$key];
    }
}
