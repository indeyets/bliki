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

        try {
            $body = $this->template($req['space_type'], $req['space_mode'], $req['data_path'], $req['args']);
            $status = 200;
        } catch (Error404Exception $e) {
            $body = $this->template('Error', '404', '', array());
            $status = 404;
        } catch (\Exception $e) {
            $body = $this->template('Error', '500', '', array($e->getMessage()));
            $status = 500;
        }

        return array(
            $status,
            array('Content-type', 'text/html; charset=utf-8'),
            $body
        );
    }

    // routing
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

        if (isset($parts[0])) {
            $path = '';

            while (count($parts) > 0) {
                if (!file_exists($spc_root.$space.'/'.$path.'/'.$parts[0])) {
                    break;
                }

                if (!empty($path))
                    $path .= '/';
                $path .= array_shift($parts);
            }

            $_path = $spc_root.'/'.$space.'/'.$path;
            if (is_dir($_path)) {
                $mode = 'index';
            } else {
                $mode = 'article';
            }

            $args = $parts;
        } else {
            $mode = 'index';
            $args = array();
        }

        return array(
            'space_type' => $space_settings['type'],
            'space_mode' => $mode,
            'data_path' => $space.'/'.$path,
            'args' => $args
        );
    }

    public function template($type, $mode, $path, array $args)
    {
        $class = __NAMESPACE__.'\\'.$type.'Handler';
        $handler = new $class();

        $data = $handler->getData($mode, $this->_root.'/spaces/'.$path, $args);

        return $this->tpl_engine->render(
            $type.'_'.$mode,
            $data
        );
    }
}
