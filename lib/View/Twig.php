<?php
/**
 * 定义基于 Twig 的抽象视图组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\View;

use Zen\View as ZenView;

use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * 基于 Twig 的抽象视图组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
abstract class Twig extends ZenView\View
{
    /**
     * 模板文件根目录路径。
     *
     * @var string
     */
    const ROOT = 'share/twig';

    /**
     * 模板文件相对路径。
     *
     * @var string
     */
    const TWIG = 'base.twig';

    /**
     * 页面标题。
     *
     * @var string
     */
    const TITLE = '';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @param  mixed[] $params
     * @return string
     */
    final protected function onRender($params)
    {
        $params['__TWIG__'] = array(
            'id' => $this->getId(),
            'title' => $this->getTitle()
        );
        $o_twig = new Twig_Environment(
            new Twig_Loader_Filesystem(static::ROOT),
            array(
                'strict_variables' => true,
                'cache' => isset($params['__CACHE__']) ? $params['__CACHE__'] : false
            )
        );

        return $o_twig->render(static::TWIG, $params);
    }

    /**
     * 获取页面编号。
     *
     * @return string
     */
    protected function getId()
    {
        $s_orig = basename(str_replace('\\', '/', get_class($this)));
        $s_ret = '';
        for ($ii = 0, $jj = strlen($s_orig); $ii < $jj; $ii++) {
            $kk = ord($s_orig[$ii]);
            if (91 > $kk && 64 < $kk) {
                if ($ii) {
                    $s_ret .= '-';
                }
                $s_ret .= chr(32 + $kk);
            } else {
                $s_ret .= $s_orig[$ii];
            }
        }

        return $s_ret;
    }

    /**
     * 获取页面标题。
     *
     * @return string
     */
    protected function getTitle()
    {
        return static::TITLE;
    }
}
