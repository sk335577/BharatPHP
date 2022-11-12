<?php

namespace BharatPHP;

use BharatPHP\Request;
use BharatPHP\Config;

class View {

    protected $request = null;
    protected $viewtype = 'default';
    protected $layout = 'default';
    public string $page_title = '';

    public function renderViewOnly($view, array $params, $viewtype = 'default') {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();

        include_once BharatPHP_VIEW_PATH . "/$viewtype/$view.phtml";
        return ob_get_clean();
    }

    public function renderView($view, array $params, $layout = 'layouts/default', $viewtype = 'default') {

        $layout = config('paths.views.' . $viewtype) . '/' . $layout . '.phtml';
        $viewContent = $this->renderViewOnly($view, $params, $viewtype);

        ob_start();

        include_once $layout;
        $layoutContent = ob_get_clean();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function getTemplatePart($part, array $params = [], $viewtype = 'default') {

        foreach ($params as $key => $value) {
            $$key = $value;
        }
        $template = config('paths.views.' . $viewtype) . '/' . $part . '.phtml';

        ob_start();
        include_once $template;
        return ob_get_clean();
    }

//    public function getTemplatePart($part, $viewtype = 'default') {
//        $template = config('paths.views.' . $viewtype) . '/' . $part . '.phtml';
//
//        ob_start();
//        include_once $template;
//        return ob_get_clean();
//    }
}
