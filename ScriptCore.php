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

    private $reg_exp = '/{{(.*?)}}/';

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

        foreach (array('dist_ext', 'reg_exp') as $var){
            if (isset($this->configs[$var]) && !empty($this->configs[$var])){
                $this->$var = $this->configs[$var];
            }
        }
    }

    public function processFiles(){
        $this->event->getIO()->write('CopyConf log:');
        if (isset($this->configs['files']) && count($this->configs['files'])){
            foreach ($this->configs['files'] as $k => $file){
                if (!file_exists($file . $this->dist_ext)){
                    $this->event->getIO()->write(sprintf('  File was not found: %s', $file . $this->dist_ext));
                }elseif (!file_exists($file) || $this->event->getIO()->askConfirmation(sprintf('  File %s exists. Override? (y/n): ', $file), false)){
                    if ($this->processFileContent($file)){
                        $this->event->getIO()->write(sprintf('  Overriding file %s success', $file));
                    }else{
                        $this->event->getIO()->write(sprintf('  Overriding file %s error', $file));
                    }
                }else{
                    $this->event->getIO()->write(sprintf('  Overriding file %s skipped', $file));
                }
            }
        }else{
            $this->event->getIO()->write('  Notice: nothing to process. Try to define array "files" in "copyconf-parameters in root composer.json" ');
        }
    }

    private function processFileContent($file){
        $this->event->getIO()->write(sprintf('  Process file %s', $file));

        $file_content = file_get_contents($file . $this->dist_ext);

        preg_match_all($this->reg_exp, $file_content, $res);

        if (count($res[1])){
            $res[0] = array_unique($res[0]);
            $res[1] = array_unique($res[1]);

            foreach ($res[1] as $k => $v){
                if (strpos($v, '|') !== false){
                    $ph = explode('|', $v);
                    $answer = $this->event->getIO()->ask(sprintf('  Value of %s (%s): ', $ph[0], $ph[1]), '');
                    if (!$answer){
                        $answer = $ph[1];
                    }
                }else {
                    $answer = $this->event->getIO()->ask(sprintf('  Value of %s: ', $v), '');
                }
                $file_content = str_replace($res[0][$k], $answer, $file_content);
            }
        }

        return file_put_contents($file, $file_content);
    }
}