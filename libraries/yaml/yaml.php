<?php

namespace MFS\YAML;

class yaml
{
    public static function load($input)
    {
        if (function_exists('syck_load')) {
            if (is_resource($input))
                return syck_load(stream_get_contents($input));
            elseif (is_string($input))
                return syck_load($input);
        } else {
            throw new \Exception();
        }
    }

    public function load_file($path)
    {
        if (function_exists('syck_load')) {
            return syck_load(file_get_contents($path));
        } else {
            throw new \Exception();
        }
    }

    public static function dump($data)
    {
        if (function_exists('syck_dump')) {
            return syck_dump($data);
        } else {
            throw new \Exception();
        }
    }
}