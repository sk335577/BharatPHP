<?php

namespace BharatPHP;

use BharatPHP\Request;
use BharatPHP\Config;

class View {

//    protected $request = null;
//    protected $viewtype = 'frontend';
//    protected $layout = 'default';
//    public string $page_title = '';
    protected $params = [];
    protected $injected_templates = [];

    public function renderViewOnly($view, array $params, $viewtype = 'frontend') {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();

        include_once BharatPHP_VIEW_PATH . "/$viewtype/$view.phtml";
        return ob_get_clean();
    }

    public function renderView($view, array $params, $layout = 'layouts/default', $viewtype = 'frontend') {
        $this->params = array_merge($this->params, $params);
        $layout = config('paths.views.' . $viewtype) . '/' . $layout . '.phtml';
        $viewContent = $this->renderViewOnly($view, $params, $viewtype);

        ob_start();

        include_once $layout;
        $layoutContent = ob_get_clean();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function getTemplatePart($part, array $params = [], $viewtype = 'frontend') {

        foreach ($this->params as $key => $value) {
            $$key = $value;
        }

        foreach ($params as $key => $value) {
            $$key = $value;
        }
        $template = config('paths.views.' . $viewtype) . '/' . $part . '.phtml';

        ob_start();
        include_once $template;
        return ob_get_clean();
    }

    public function injectTemplate($position, $template, $viewtype = 'frontend') {
        if (!isset($this->injected_templates[$position])) {
            $this->injected_templates[$position] = [];
        }
        $this->injected_templates[$position][] = $viewtype . "/" . $template;
    }

    public function printInjectedTemplates($position) {

        if (isset($this->injected_templates[$position])) {
            foreach ($this->injected_templates[$position] as $t) {
                if (is_file(BharatPHP_VIEW_PATH . "/" . $t . ".phtml")) {
                    include_once BharatPHP_VIEW_PATH . "/" . $t . ".phtml";
                }
            }
        }
    }

//    public function getTemplatePart($part, $viewtype = 'default') {
//        $template = config('paths.views.' . $viewtype) . '/' . $part . '.phtml';
//
//        ob_start();
//        include_once $template;
//        return ob_get_clean();
//    }
}
