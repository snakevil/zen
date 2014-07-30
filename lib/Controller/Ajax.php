<?php
/**
 * 定义抽象 Ajax 接口控制器组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Controller;

use Zen\View as ZenView;
use snakevil\zen;

/**
 * 抽象 Ajax 接口控制器组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
abstract class Ajax extends Web
{
    /**
     * 授权域名。
     *
     * @var string
     */
    const DOMAIN = '*';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    final protected function onOPTIONS()
    {
        $s_headers = 'Authorization,Content-Type,Accept,Origin,User-Agent,DNT' .
            ',Cache-Control,X-Mx-ReqToken,Keep-Alive,X-Requested-With,If-Modified-Since';
        $this->output
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', $s_headers)
            ->header('Access-Control-Max-Age', 2592000)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Length', 0)
            ->state(204);
    }

    /**
     * {@inheritdoc}
     *
     * @param  \Exception    $error 捕获地错误
     * @return zen\View\Json
     */
    final protected function onError(\Exception $error)
    {
        return new zen\View\Json(array('error' => $error));
    }

    /**
     * {@inheritdoc}
     *
     * @param  ZenView\IView $view 结果视图
     * @return void
     */
    protected function onRespond(ZenView\IView $view)
    {
        if ($view instanceof zen\View\Json) {
            $s_callback = $this->input->expect('post:callback', $this->input->expect('get:callback', ''));
            if ('' != $s_callback) {
                $view['jsonp'] = $s_callback;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function onClose()
    {
        $s_origin = '*';
        if ('*' != static::DOMAIN) {
            $s_scheme = 'http' . (isset($this->input['server:HTTPS']) ? 's' : '');
            $s_origin = $this->input->expectMatch(
                'server:HTTP_ORIGIN',
                '#[\./]' . preg_quote(static::DOMAIN) . '$#',
                $s_scheme . '://' . static::DOMAIN
            );
        }
        $this->output
            ->header('Access-Control-Allow-Origin', $s_origin)
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
