<?php

namespace MFS\Bliki;

class ErrorHandler
{
    public function getData($mode, $fs_path, $args)
    {
        if ('404' === $mode)
            return array();

        if ('500' === $mode)
            return array('content' => $args[0]);
    }
}
