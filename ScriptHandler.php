<?php

namespace Gregurco\ParameterHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildParameters(Event $event)
    {
        $core = new ScriptCore($event);
        $core->validateAndPrepare();
        $core->processFiles();
    }
}
