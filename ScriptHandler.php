<?php

namespace Gregurco\ParameterHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildParameters(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['copyconf-parameters'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.copyconf-parameters setting.');
        }

        $configs = $extras['copyconf-parameters'];

        if (!is_array($configs)) {
            throw new \InvalidArgumentException('The extra.copyconf-parameters setting must be an array or a configuration object.');
        }

        foreach ($configs['files'] as $k => $file){
            copy($file . '.dist', $file);
        }
    }
}
