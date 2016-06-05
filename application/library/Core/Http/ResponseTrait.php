<?php

namespace Core\Http;

/**
 * 用于格式化的数据输出,目前支持JSON
 */
trait ResponseTrait
{
    /**
     * @var string Code Class的名称
     */
    protected $code = Code::class;

    /**
     * 输出成功
     *
     * @param array $data
     * @return bool
     */
    public function success(array $data = null)
    {
        $codes = $this->code;
        $code = $codes::SUCCESS;
        return $this->output($code, $this->codeMessage($code), $data);
    }

    /**
     * 输出错误
     *
     * $this->error(Code::PASSWORD);
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return bool
     */
    public function error($code, $msg = null, array $data = null)
    {
        $codes = $this->code;
        if (func_num_args() < 3 && ($msg === null || $msg === '' || is_array($msg))) {
            if (is_array($msg)) {
                $data = $msg;
            }

            if (is_numeric($code)) { // 不过于严格的检查,例如浮点数
                $msg = $this->codeMessage($code);
            } else {
                $msg = $code;
                $code = $codes::UNDEFINED_ERROR;
            }
        }

        return $this->output($code, $msg, $data);
    }

    /**
     * 输出结果并退出
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return bool
     */
    public function output($code, $msg = '', array $data = null)
    {
        $result = json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], $data === null ? JSON_FORCE_OBJECT : 0);


        if (isset($_GET['_call']) && $callback = $_GET['_call']) {
            echo htmlspecialchars($callback) . '(' . $result . ')';
        } else {
            echo $result;
        }

        return true;
    }

    /**
     * 返回Code对应的Message
     *
     * @param $code
     * @return mixed
     */
    private function codeMessage($code)
    {
        $codes = $this->code;
        if (isset($codes::$msg[$code])) {
            return $codes::$msg[$code];
        }

        if (isset(Code::$msg[$code])) {
            return Code::$msg[$code];
        }

        return $this->codeMessage(Code::UNDEFINED_ERROR);
    }
}
