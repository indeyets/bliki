<?php

namespace MFS\Bliki;

use MFS\YAML\yaml;

class App
{
    private $_root;
    private $tpl_loader;
    private $tpl_engine;

    public function __construct()
    {
        $this->_root = realpath(__DIR__.'/..');

        $this->tpl_loader = new \sfTemplateLoaderFilesystem($this->_root.'/templates/%name%.php');
        $this->tpl_engine = new \sfTemplateEngine($this->tpl_loader);
    }

    public function __invoke($context)
    {
        $req = $this->parsePath($context['env']['REQUEST_URI']);

        return array(200, array('Content-type', 'text/html; charset=utf-8'), $this->template($req['space_type'], $req['data_path']));
    }

    public function parsePath($req_path)
    {
        $spc_root = $this->_root.'/spaces/';
        $parts = explode('/', substr($req_path, 1));

        if (!empty($parts[0]) and is_dir($spc_root.'/'.$parts[0])) {
            $space = array_shift($parts);
        } else {
            $settings = yaml::load_file($spc_root.'/settings.yaml');
            $space = $settings['default'];
        }

        $space_settings = yaml::load_file($spc_root.'/'.$space.'/settings.yaml');

        if (isset($parts[1])) {
            if (file_exists($spc_root.'/'.$space.'/'.implode('/', $parts))) {
                $mode = 'article';
            } else {
                throw new Exception();
            }
        } else {
            $mode = 'index';
        }

        return array(
            'space_type' => $space_settings['type'].'_'.$mode,
            'data_path' => '/blog/2009/12/02/hello_world.html'
        );
    }

    public function parseFile($path)
    {
        $data = file_get_contents($path);
        $div = strpos($data, "\n\n");

        $headers_strs = explode("\n", substr($data, 0, $div));
        $headers = array();

        array_walk(
            $headers_strs,
            function($elt) use (&$headers) {
                $div = strpos($elt, ': ');
                $headers[substr($elt, 0, $div)] = substr($elt, $div + 2);
            }
        );

        $body = substr($data, $div + 2);

        return array($headers, $body);
    }

    public function template($type, $path)
    {
        $res = $this->parseFile($this->_root.'/spaces'.$path);

        return $this->tpl_engine->render(
            $type,
            array(
                'title' => $res[0]['Title'],
                'content' => $res[1],
            )
        );
    }
}
