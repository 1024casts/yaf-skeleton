<?php

namespace Core\Views;

use Yaf\Registry;
use Yaf\View_Interface;
use Core\Views\Exceptions\CallException;
use Core\Views\Exceptions\RenderException;

/**
 * 扩展视图
 */
class View implements View_Interface
{
    /**
     * @var Block
     */
    public $block;

    /**
     * 注册的方法
     *
     * @var array
     */
    protected $callable = [];

    /**
     * 是否正在渲染
     *
     * @var bool
     */
    protected $isRending = false;

    /**
     * 模板数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 模板目录
     *
     * @var string
     */
    protected $path;

    /**
     * 模板后缀
     *
     * @var string
     */
    protected $ext = '.phtml';

    /**
     * 文件栈
     *
     * @var array
     */
    protected $stacks = [];

    public function __construct($viewPath = null)
    {
        $config = Registry::get('config');
        if (isset($config['view'])) {
            $config = $config['view'];
        }

        if ($viewPath) {
            $this->path = $viewPath;
        } elseif (isset($config['path'])) {
            $this->path = $config['path'];
        }

        if (isset($config['ext'])) {
            $this->ext = $config['ext'];
        }
    }

    /**
     * 注入方法
     *
     * @param $name
     * @param callable $callback
     */
    public function registerFunc($name, callable $callback)
    {
        $this->callable[$name] = $callback;
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        return $this->assign($name, $value);
    }

    /**
     * 注入方法调用
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws CallException
     */
    public function __call($name, $arguments)
    {
        if (isset($this->callable[$name])) {
            return call_user_func_array($this->callable[$name], $arguments);
        }

        throw new CallException($name);
    }

    /**
     * 模板赋值
     *
     * @param array|string $name
     * @param null|string $value
     * @return bool
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }

        return true;
    }

    /**
     * 设置模板目录
     *
     * @param string $tplDir
     * @return bool
     */
    public function setScriptPath($tplDir)
    {
        $this->path = $tplDir;
        return true;
    }

    /**
     * 设置模板目录
     *
     * @return string
     */
    public function getScriptPath()
    {
        if ($this->path === null) {
            $this->path = APP_PATH . '/views';
        }
        
        return $this->path;
    }

    /**
     * 渲染并输出
     *
     * @param string $tpl
     * @param array $data
     * @return bool
     * @throws RenderException
     */
    public function display($tpl, $data = array())
    {
        if ($this->isRending) {
            throw new RenderException('Display is called.');
        }

        $this->assign($data);

        echo $this->render($tpl, $data);

        return true;
    }

    /**
     * 渲染模板
     *
     * @param string $tpl
     * @param array $data
     * @return String
     * @throws RenderException
     */
    public function render($tpl, $data = [])
    {
        $isFirst = false;
        if (!$this->isRending) {
            $this->isRending = $isFirst = true;
            $this->block = new Block();
        }

        if (substr_compare($tpl, $this->ext, -strlen($this->ext)) !== 0) {
            $tpl .= $this->ext;
        }

        if ($this->stacks && $tpl[0] !== '/') {
            $path = dirname(current($this->stacks)) . '/' . $tpl;
        } else {
            $path = $this->getScriptPath() . '/' . $tpl;
        }

        $realPath = realpath($path);
        if ($realPath === false) {
            throw new RenderException('Not found the template: ' . $path);
        }

        array_unshift($this->stacks, $realPath);

        $content = $this->extractRender($realPath, $data);
        if ($isFirst) {
            $content = $this->block->replace($content);
        }

        array_shift($this->stacks);

        return $content;
    }

    /**
     * 释放变量渲染
     * 
     * @param string $tpl
     * @param array $data
     * @return string
     */
    protected function extractRender($tpl, $data)
    {
        ob_start();

        extract($data);
        include $tpl;

        return trim(ob_get_clean(), "\r\n ");
    }

    /**
     * 继承模板
     *
     * @param $tpl
     * @param $data
     */
    public function extend($tpl, array $data = [])
    {
        echo $this->render($tpl, array_merge($this->data, $data));
    }

    /**
     * 转义HTML
     *
     * @param string $string
     * @param string $flags html|url|json
     * @return mixed
     */
    public function escape($string, $flags = 'html')
    {
        switch ($flags) {
            case 'url' :
                return urlencode($string);
            case 'quotes' :
                return addcslashes($string, '"\'\\');
            case 'phone' :
                if (strlen($string) < 8) {
                    return substr($string, 0, -4) . '****';
                }
                return substr($string, 0, 3) . '****' . substr($string, -4);
            case 'json' :
                return json_encode($string);
            case 'html' :
            default :
                return htmlspecialchars($string, ENT_QUOTES|ENT_HTML5);
        }
    }

    /**
     * 格式化时间
     *
     * @param int $timestamp
     * @param string $format 格式
     * @return mixed
     */
    public function date($timestamp, $format = 'Y-m-d H:i:s')
    {
        return date($format, $timestamp);
    }
}