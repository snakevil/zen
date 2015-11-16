<?php
/**
 * 定义数据格式为 JSON 的视图组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2015 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\View;

use Zen\Core as ZenCore;
use Zen\View as ZenView;

/**
 * 数据格式为 JSON 的视图组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
final class Json extends ZenView\View implements IJson
{
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
        $s_jsonp = '';
        if (isset($params['jsonp'])) {
            $s_jsonp = $params['jsonp'];
            unset($params['jsonp']);
        }
        if (isset($params['error']) && $params['error'] instanceof \Exception) {
            $a_ret = array(
                'error' => $params['error']->getCode(),
                'reason' => $params['error']->getMessage()
            );
            if (!$a_ret['error']) {
                $a_ret['error'] = 1;
            }
            if ($params['error'] instanceof ZenCore\Exception) {
                $a_ret['context'] = $params['error']->getContext();
            } else {
                unset($params['error']);
                $a_ret['context'] = $params;
            }
        } else {
            $a_ret = $params;
            $a_ret['error'] = 0;
            $a_ret['success'] = true;
        }
        $s_ret = json_encode($a_ret);
        if ($s_jsonp) {
            $s_ret = $s_jsonp . '(' . $s_ret . ')';
        }

        return $s_ret;
    }
}
