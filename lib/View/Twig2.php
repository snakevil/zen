<?php
/**
 * 定义基于 Twig2 的抽象视图组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2017 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\View;

use Zen\View as ZenView;
use Twig_ExtensionInterface;
use Twig_Loader_Filesystem;
use Twig_Environment;

abstract class Twig2 extends ZenView\View implements Twig_ExtensionInterface
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
     * 页面关键词。
     *
     * @var string
     */
    const KEYWORDS = '';

    /**
     * 页面描述。
     *
     * @var string
     */
    const DESCRIPTION = '';

    /**
     * {@inheritdoc}
     *
     * @internal
     *
     * @param mixed[] $params
     *
     * @return string
     */
    final protected function onRender($params)
    {
        $params['$page'] = array(
            'id' => $this->getId($params),
            'title' => $this->getTitle($params),
            'keywords' => $this->getKeywords($params),
            'description' => $this->getDescription($params),
        );
        $o_twig = new Twig_Environment(
            new Twig_Loader_Filesystem(static::ROOT),
            array(
                'strict_variables' => true,
                'cache' => $params['$config']['cache'],
            )
        );
        $o_twig->addExtension($this);

        return $o_twig->render(static::TWIG, $params);
    }

    /**
     * 获取页面编号。
     *
     * @param array $params
     *
     * @return string
     */
    protected function getId($params)
    {
        $s_orig = basename(str_replace('\\', '/', get_class($this)));
        $s_ret = '';
        for ($ii = 0, $jj = strlen($s_orig); $ii < $jj; ++$ii) {
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
     * @param array $params
     *
     * @return string
     */
    protected function getTitle($params)
    {
        return static::TITLE;
    }

    /**
     * 获取页面关键词。
     *
     * @param array $params
     *
     * @return string
     */
    protected function getKeywords($params)
    {
        return static::KEYWORDS;
    }

    /**
     * 获取页面描述。
     *
     * @param array $params
     *
     * @return string
     */
    protected function getDescription($params)
    {
        return static::DESCRIPTION;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return array();
    }
}
