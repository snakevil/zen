<?php
/**
 * 定义基于 Twig 的抽象视图组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\View;

use Zen\View as ZenView;
use Twig_ExtensionInterface;
use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * 基于 Twig 的抽象视图组件。
 */
abstract class Twig extends ZenView\View implements Twig_ExtensionInterface
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
        $params['__TWIG__'] = array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'keywords' => $this->getKeywords(),
            'description' => $this->getDescription(),
        );
        $o_twig = new Twig_Environment(
            new Twig_Loader_Filesystem(static::ROOT),
            array(
                'strict_variables' => true,
                'cache' => isset($params['__CACHE__']) ? $params['__CACHE__'] : false,
            )
        );
        $o_twig->addExtension($this);

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
     * @return string
     */
    protected function getTitle()
    {
        return static::TITLE;
    }

    /**
     * 获取页面关键词。
     *
     * @return string
     */
    protected function getKeywords()
    {
        return static::KEYWORDS;
    }

    /**
     * 获取页面描述。
     *
     * @return string
     */
    protected function getDescription()
    {
        return static::DESCRIPTION;
    }

    /**
     * {@inheritdoc}
     */
    final public function initRuntime(Twig_Environment $environment)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
    }
}
