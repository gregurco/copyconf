<?php
/**
 * @copyright Copyright (c) 2013 Gregurco Vlad
 * @link https://github.com/gregurco
 * @license http://opensource.org/licenses/MIT MIT License
 */
namespace Gregurco\ParameterHandler;

use Composer\Script\Event;

/**
 * ScriptHandler
 *
 * @author Gregurco Vlad <gregurco.vlad@gmail.com>
 * @link https://github.com/gregurco
 * @package gregurco/copyconf
 */
class ScriptHandler
{
    public static function buildParameters(Event $event)
    {
        $core = new ScriptCore($event);
        $core->validateAndPrepare();
        $core->processFiles();
    }
}
