<?php

namespace Gregurco\ParameterHandler;

use Composer\Script\Event;
use Composer\IO\IOInterface;

class ScriptCore
{
    private $event;

    private $extras;

    private $configs;

    private $dist_ext = '.dist';

    function __construct(Event $event){
        $this->event = $event;
        $this->extras = $event->getComposer()->getPackage()->getExtra();
    }

    public function validateAndPrepare(){
        if (!isset($this->extras['copyconf-parameters'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.copyconf-parameters setting.');
        }

        $this->configs = $this->extras['copyconf-parameters'];

        if (!is_array($this->configs)) {
            throw new \InvalidArgumentException('The extra.copyconf-parameters setting must be an array or a configuration object.');
        }

        if (isset($this->configs['dist_ext']) && !empty($this->configs['dist_ext'])){
            $this->dist_ext = $this->configs['dist_ext'];
        }
    }

    public function processFiles(){
        foreach ($this->configs['files'] as $k => $file){
            if (file_exists($file) && $this->event->getIO()->askConfirmation(sprintf('File %s exists. Override? (y/n): ', $file)) == 'y'){
                copy($file . $this->dist_ext, $file);
            }
        }
    }
}