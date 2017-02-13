<?php
namespace Core\Rpc\Server;

use Core\Mvc\ControllerApi;

/**
 * RPC服务端JSON实现-controller入口
 *
 * usage sample:
 * // 加入某module/controllers下即可
 * controller RpcController extends Json
 * {
 * }
 */
class Json extends ControllerApi implements ServerInterface
{
    /**
     * 请求唯一ID
     * @var string
     */
    private $id;

    /**
     * 请求模块, 一般为service类名, 可通过DI获取即可
     * @var string
     */
    private $module;

    /**
     * 请求方法
     * @var string
     */
    private $fun;

    /**
     * 请求参数
     * @var array
     */
    private $args;

    /**
     * 初始化, 解析请求参数为属性
     */
    public function init()
    {
        parent::init();

        $params = json_decode(urldecode($this->get('params')), true);

        $props = ['id', 'module', 'fun'];
        foreach ($props as $prop) {
            $this->{$prop} = isset($params[$prop]) ? $params[$prop] : '';
        }

        $this->args = isset($params['args']) ? $params['args'] : [];
    }

    /**
     * RPC请求入口action
     */
    public function callAction()
    {
        $info = ['id' => $this->id, 'module' => $this->module, 'fun' => $this->fun, 'args' => $this->args];
        $this->eventsManager->fire('rpc:beforeHandle', $this, $info);

        try {
            $rs = $this->handle();
            $info['rs'] = $rs;
            $this->eventsManager->fire('rpc:afterHandled', $this, $info);
        } catch (\Exception $e) {
            $info['exception'] = $e;
            $this->eventsManager->fire('rpc:onException', $this, $info);

            $rs = $this->buildFromException($e);
        }

        $this->success($rs);
    }

    /**
     * 请求处理
     * @return array
     */
    public function handle()
    {
        @ob_start();
        $ret = call_user_func_array([$this->di->get($this->module), $this->fun], $this->args);
        $out = ob_get_clean();
        ob_end_clean();

        return $this->packResult(['return' => $ret, 'output' => $out ?: '']);
    }

    /**
     * 拼装异常结果
     * @param \Exception $e
     *
     * @return array
     */
    protected function buildFromException(\Exception $e)
    {
        $rs = [
            'return' => false, 'status' => 500,
            'exception' => [
                'msg'  => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
        ];

        return $this->packResult($rs);
    }

    /**
     * 拼装处理结果
     * @param array $rs
     *
     * @return array
     */
    protected function packResult($rs)
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'fun' => $this->fun,
            'args' => $this->args,
            'return' => isset($rs['return']) ? $rs['return'] : null,
            'status' => isset($rs['status']) ? $rs['status'] : 200,
            'output' => isset($rs['output']) ? $rs['output'] : '',
            'exception' => isset($rs['exception']) ? $rs['exception'] : [],
        ];
    }

}
