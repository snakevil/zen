<?php
/**
 * 定义抽象控制器组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2016 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Controller;

use Zen\Core as ZenCore;
use Zen\Web\Application as ZenWebApp;
use Zen\View as ZenView;
use snakevil\zen;

/**
 * 抽象控制器组件。
 */
abstract class Web extends ZenWebApp\Controller\Controller
{
    /**
     * 静态页面文件缓存路径。
     *
     * @var string
     */
    const CACHE_PATH = '';

    /**
     * 静态页面文件缓存生命周期（单位：秒）。
     *
     * @var int
     */
    const CACHE_LIFETIME = 0;

    /**
     * 派发令牌实例。
     *
     * @var ZenCore\Application\IRouterToken
     */
    protected $token;

    /**
     * {@inheritdoc}
     *
     * @param ZenCore\Application\IRouterToken $token 派发令牌
     */
    final public function act(ZenCore\Application\IRouterToken $token)
    {
        $this->token = $token;
        $b_error = false;
        try {
            $this->onAct();
            $o_view = call_user_func(array($this, 'on'.$this->input['server:REQUEST_METHOD']));
        } catch (\Exception $ee) {
            $b_error = true;
            $o_view = $this->onError($ee);
        }
        if ($o_view instanceof zen\View\IJson) {
            $this->output->header('Content-Type', 'application/json');
        }
        if ($o_view instanceof ZenView\IView) {
            $a_options = array();
            if ($this->config['view']) {
                $a_options['__CONFIG__'] = $this->config['view'];
            }
            if ($o_view instanceof zen\View\Twig) {
                $a_options['__CACHE__'] = !$this->inDev() && isset($this->config['caching.twig'])
                    ? $this->config['caching.twig']
                    : false;
            }
            $this->onRespond($o_view);
            $s_out = $o_view->render($a_options);
            $p_cache = $this->getCachePath();
            if (!$b_error && 'GET' == $this->input['server:REQUEST_METHOD'] && $p_cache && 0 < static::CACHE_LIFETIME) {
                $this->cache(
                    $o_view,
                    $p_cache,
                    new ZenCore\Type\DateTime('+'.static::CACHE_LIFETIME.' sec')
                );
            }
            $this->output->write($s_out);
        }
        $this->onClose();
        $this->output->close();
    }

    /**
     * 控制逻辑开始事件。
     */
    protected function onAct()
    {
    }

    /**
     * HTTP OPTIONS 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onOPTIONS()
    {
        $this->output->state(405);
    }

    /**
     * HTTP GET 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onGET()
    {
        $this->output->state(405);
    }

    /**
     * HTTP HEAD 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onHEAD()
    {
        $this->output->state(405);
    }

    /**
     * HTTP POST 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onPOST()
    {
        $this->output->state(405);
    }

    /**
     * HTTP PUT 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onPUT()
    {
        $this->output->state(405);
    }

    /**
     * HTTP DELETE 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onDELETE()
    {
        $this->output->state(405);
    }

    /**
     * HTTP TRACE 请求事件。
     *
     * @return ZenView\IView|void
     */
    protected function onTRACE()
    {
        $this->output->state(405);
    }

    /**
     * 异常容错事件。
     *
     * @param \Exception $ee 捕获地异常
     *
     * @return ZenView\IView|void
     */
    protected function onError(\Exception $ee)
    {
        var_dump($ee);
    }

    /**
     * 响应事件。
     *
     * @param ZenView\IView $view
     */
    protected function onRespond(ZenView\IView $view)
    {
    }

    /**
     * 控制器输出结束事件。
     */
    protected function onClose()
    {
    }

    /**
     * 缓存指定视图。
     *
     * @param ZenView\IView        $view    待缓存地视图
     * @param string               $path    缓存文件路径
     * @param \DateTime|string|int $expires 可选。指定过期时间
     * @param \DateTime|string|int $mtime   可选。指定修改时间
     *
     * @return bool
     */
    protected function cache(ZenView\IView $view, $path, $expires = false, $mtime = false)
    {
        if ($this->inDev() || !isset($this->config['caching.solid'])) {
            return false;
        }
        $s_lob = (string) $view;
        if (!$s_lob) {
            return false;
        }
        zen\Utility\Cache::root($this->config['caching.solid']);
        $o_cache = new zen\Utility\Cache($path);
        $o_cache->store($s_lob, $mtime);
        if (false !== $expires) {
            $o_cache->expires($expires);
        }

        return true;
    }

    /**
     * 判断是否为开发模式。
     *
     * @return bool
     */
    protected function inDev()
    {
        return file_exists('@DEV');
    }

    /**
     * 获取静态缓存文件路径。
     *
     * @return string
     */
    protected function getCachePath()
    {
        return static::CACHE_PATH;
    }
}
