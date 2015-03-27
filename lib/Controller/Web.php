<?php
/**
 * 定义抽象控制器组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Controller;

use Zen\Core as ZenCore;
use Zen\Web\Application as ZenWebApp;
use Zen\View as ZenView;

use snakevil\zen;

/**
 * 抽象控制器组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
abstract class Web extends ZenWebApp\Controller\Controller
{
    /**
     * 派发令牌实例。
     *
     * @var ZenCore\Application\IRouterToken
     */
    protected $token;

    /**
     * {@inheritdoc}
     *
     * @param  ZenCore\Application\IRouterToken $token 派发令牌
     * @return void
     */
    final public function act(ZenCore\Application\IRouterToken $token)
    {
        $this->token = $token;
        try {
            $this->onAct();
            $o_view = call_user_func(array($this, 'on' . $this->input['server:REQUEST_METHOD']));
        } catch (\Exception $ee) {
            $o_view = $this->onError($ee);
        }
        if ($o_view instanceof ZenView\IView) {
            $a_options = array();
            if ($o_view instanceof zen\View\Twig) {
                $a_options['__CACHE__'] = !$this->inDev() && isset($this->config['caching.twig'])
                    ? $this->config['caching.twig']
                    : false;
            }
            $this->onRespond($o_view);
            $s_out = $o_view->render($a_options);
            $this->output->write($s_out);
        }
        $this->onClose();
        $this->output->close();
    }

    /**
     * 控制逻辑开始事件。
     *
     * @return void
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
     * @param  \Exception         $ee 捕获地异常
     * @return ZenView\IView|void
     */
    protected function onError(\Exception $ee)
    {
        var_dump($ee);
    }

    /**
     * 响应事件。
     *
     * @param  ZenView\IView $view
     * @return void
     */
    protected function onRespond(ZenView\IView $view)
    {
    }

    /**
     * 控制器输出结束事件。
     *
     * @return void
     */
    protected function onClose()
    {
    }

    /**
     * 缓存指定视图。
     *
     * @param  ZenView\IView         $view  待缓存地视图
     * @param  string                $path  缓存文件路径
     * @param  ZenCore\Type\DateTime $mtime 可选。指定修改时间
     * @return bool
     */
    protected function cache(ZenView\IView $view, $path, ZenCore\Type\DateTime $mtime = null)
    {
        if (!$this->inDev() && isset($this->config['caching.solid'])) {
            $p_path = $this->config['caching.solid'] . '/' . $path;
            $p_dir = dirname($p_path);
            if (!is_dir($p_dir) && !mkdir($p_dir, 0755, true) || !file_put_contents($p_path, $view)) {
                throw new ExCachingDenied($path);
            }
            if (null !== $mtime) {
                touch($p_path, $mtime->getTimestamp());
            }
        }

        return false;
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
}
