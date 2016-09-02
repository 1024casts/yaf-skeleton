<?php

namespace Core\Views;

use Core\Views\Exceptions\BlockException;

/**
 *
 */
class Block
{
    /**
     * Blocks
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * 打开的块
     *
     * @var array
     */
    protected $opens = [];

    /**
     * Block 堆
     *
     * @var array
     */
    protected $stacks = [];

    /**
     * 拥有子块的块名称
     *
     * @var array
     */
    protected $hasChild = [];

    /**
     * 模板块开始
     *
     * @param string $name
     * @param string $type
     * @throws BlockException
     */
    public function begin($name, $type = 'replace')
    {
        $stacks = [$name, $type];
        if (!isset($this->opens[$name])) {
            $stacks[] = $this->placeholder($name);
        } elseif ($this->opens[$name]) {
            throw new BlockException($name, 'is opened');
        }

        $this->opens[$name] = true;
        $this->stacks[] = $stacks;
        ob_start();
    }

    /**
     * 获取指定父块的内容
     *
     * @param $name
     * @return string
     */
    public function parent($name)
    {
        $placeholder = $this->placeholder($name);
        return isset($this->blocks[$placeholder]) ? $this->blocks[$placeholder] : '';
    }

    /**
     * 结束一个块
     *
     * @param $name
     * @throws BlockException
     */
    public function end($name)
    {
        $this->lastOpen = null;

        $this->opens[$name] = false;
        $last = array_pop($this->stacks);
        if ($last[0] != $name) {
            throw new BlockException($name, "is unexpected, expected {$last[0]}");
        }

        $content = ob_get_clean();
        if (!isset($this->hasChild[$name])) {
            $content = $this->_replace($content);
        }

        $pre = count($this->stacks) - 1;
        if ($pre > -1 && $this->opens[$this->stacks[$pre][0]]) {
            $this->hasChild[$this->stacks[$pre][0]] = true;
        }

        switch ($last[1]) {
            case 'replace' : // 默认
                break;
            case 'append' : // 追加
                $content = $this->parent($name) . $content;
                break;
            case 'prepend' : // 前面增加
                $content .= $this->parent($name);
                break;
            case 'hide' : // 不存在就隐藏
                if (!isset($this->blocks[$name])) {
                    $content = '';
                }
                break;
            default :
                throw new BlockException($name, 'undefined action type');
        }

        $this->blocks[$this->placeholder($name)] = $content;

        // 输出占位符
        if (isset($last[2])) {
            echo $last[2];
        }
    }

    /**
     * 替换里面的块占位符
     *
     * @param $content
     * @return mixed
     */
    protected function _replace($content)
    {
        $content = trim($content, "\r\n ");
        if (!$this->blocks) {
            return $content;
        }

        return strtr($content, $this->blocks);
    }

    /**
     * 替换里面的块所有占位符
     *
     * @param $content
     * @return mixed
     */
    public function replace($content)
    {
        foreach ($this->hasChild as $name => $value) {
            $name = $this->placeholder($name);
            $this->blocks[$name] = $this->_replace($this->blocks[$name]);
        }

        return $this->_replace($content);
    }

    /**
     * 块占位符
     *
     * @param $name
     * @return string
     */
    protected function placeholder($name)
    {
        return "<!--TPL BLOCK {$name}-->";
    }
}