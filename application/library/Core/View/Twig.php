<?php

namespace Core;

use \Twig_Environment;
use \Twig_Loader_Filesystem;

class Twig implements \Yaf\View_Interface
{
    /** @var \Twig_Loader_Filesystem */
    protected $loader;
    /** @var \Twig_Environment */
    protected $twig;
    protected $variables = array();

    /**
     * @param string|array $templateDir
     * @param array        $options
     */
    public function __construct($templateDir, array $options = array())
    {
        $this->loader = new Twig_Loader_Filesystem($templateDir);
        $this->twig = new Twig_Environment($this->loader, $options);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->variables[$name]);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->variables[$name];
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->variables[$name]);
    }

    /**
     * Return twig instance
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * If $name is a k=>v array, it will assign it to template
     *
     * @param string | array $name
     * @param mixed          $value
     * @return bool
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->variables[$k] = $v;
            }
        } else {
            $this->variables[$name] = $value;
        }
    }

    /**
     * @param string $template
     * @param array  $variables
     * @return bool
     */
    public function display($template, $variables = null)
    {
        echo $this->render($template, $variables);
    }

    /**
     * @return string
     */
    public function getScriptPath()
    {
        $paths = $this->loader->getPaths();

        return reset($paths);
    }

    /**
     * @param string $template
     * @param array  $variables
     * @return string
     */
    public function render($template, $variables = null)
    {
        if (is_array($variables)) {
            $this->variables = array_merge($this->variables, $variables);
        }

        return $this->twig->loadTemplate($template)->render($this->variables);
    }

    /**
     * @param string|array $templateDir
     * @return void
     */
    public function setScriptPath($templateDir)
    {
        $this->loader->setPaths($templateDir);
    }
}