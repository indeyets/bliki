<?php

namespace MFS\Bliki;

class BlogHandler
{
    public function getData($mode, $fs_path, $args)
    {
        if ('article' === $mode)
            return $this->parseArticle($fs_path);

        if ('index' === $mode) {
            if (!empty($args))
                throw new Error404Exception();

            return $this->generateIndex($fs_path);
        }
    }

    private function _parseArticle($path)
    {
        if (!file_exists($path) or !is_file($path))
            throw new \Exception('not file: '.$path);

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

        return array($headers, substr($data, $div + 2));
    }

    public function parseArticle($path)
    {
        $data = $this->_parseArticle($path);

        return array(
            'title' => $data[0]['Title'],
            'content' => $data[1],
        );
    }

    public function generateIndex($path)
    {
        if (!is_dir($path))
            throw new \Exception();

        $articles = array();

        $i = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($i as $node) {
            if (!$node->isFile())
                continue;

            $_filename = $node->getFilename();
            $_extension = substr($_filename, strrpos($_filename, '.') + 1);

            if ('html' != $_extension)
                continue;

            list($headers, $body) = $this->_parseArticle($node->getPathname());
            $articles[] = array(
                'title' => $headers['Title'],
                'content' => $body
            );
        }

        return array('title' => 'Archive', 'articles' => $articles);
    }
}
