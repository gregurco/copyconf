<?php
/**
 * @copyright Copyright (c) 2013 Gregurco Vlad
 * @link https://github.com/gregurco
 * @license http://opensource.org/licenses/MIT MIT License
 */
namespace Gregurco\ParameterHandler;

use Composer\Script\Event;
use Composer\IO\IOInterface;

/**
 * ScriptCore
 *
 * @author Gregurco Vlad <gregurco.vlad@gmail.com>
 * @link https://github.com/gregurco
 * @package gregurco/copyconf
 */
class ScriptCore
{
    /**
     * @var object Event
     */
    private $event;

    /**
     * @var object getExtra()
     */
    private $extras;

    /**
     * @var array configs from composer.json extra section
     */
    private $configs;

    /**
     * @var string the extension of the processing files (optional)
     */
    private $dist_ext = '.dist';

    /**
     * @var string the regular expression used on finding placeholders (optional)
     */
    private $reg_exp = '/{{(.*?)}}/';

    /**
     * @var string the mode of backup (optional)
     */
    private $backup_mode = 'false';

    /**
     * @var string the directory where to backup files (optional)
     */
    private $backup_dir = 'backup/';

    function __construct(Event $event){
        $this->event = $event;
        $this->extras = $event->getComposer()->getPackage()->getExtra();
    }

    /**
     * Validate composer extra parameters and prepare them to processing
     */
    public function validateAndPrepare(){
        if (!isset($this->extras['copyconf-parameters'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.copyconf-parameters setting.');
        }

        $this->configs = $this->extras['copyconf-parameters'];

        if (!is_array($this->configs)) {
            throw new \InvalidArgumentException('The extra.copyconf-parameters setting must be an array or a configuration object.');
        }

        foreach (array('dist_ext', 'reg_exp', 'backup_mode', 'backup_dir') as $var){
            if (isset($this->configs[$var]) && !empty($this->configs[$var])){
                $this->$var = $this->configs[$var];
            }
        }
    }

    /**
     * Process files
     */
    public function processFiles(){
        $this->event->getIO()->write('CopyConf log:');
        if (isset($this->configs['files']) && count($this->configs['files'])){
            foreach ($this->configs['files'] as $k => $file){
                if (!file_exists($file . $this->dist_ext)){
                    $this->event->getIO()->write(sprintf('  File was not found: %s', $file . $this->dist_ext));
                }else{
                    if (!file_exists($file)){
                        $this->processFileContent($file);
                    }else{
                        if ($this->event->getIO()->askConfirmation(sprintf('  File %s exists. Override? (y/n): ', $file), false)){
                            if ($this->backup_mode == 'ask'){
                                if ($this->event->getIO()->askConfirmation(sprintf('  Backup file? (y/n): ', $file), false)) {
                                    $this->backup_file($file);
                                }
                            }elseif($this->backup_mode == 'true'){
                                $this->backup_file($file);
                            }

                            if ($this->processFileContent($file)){
                                $this->event->getIO()->write(sprintf('  Overriding file %s success', $file));
                            }else{
                                $this->event->getIO()->write(sprintf('  Overriding file %s error', $file));
                            }
                        }else{
                            $this->event->getIO()->write(sprintf('  Overriding file %s skipped', $file));
                        }
                    }
                }
                $this->event->getIO()->write('-------------------------------------');
            }
        }else{
            $this->event->getIO()->write('  Notice: nothing to process. Try to define array "files" in "copyconf-parameters in root composer.json" ');
        }
    }

    /**
     * Process file content
     *
     * @param string $file the file that should be backuped
     * @return bool
     */
    private function processFileContent($file){
        $this->event->getIO()->write(sprintf('  Process file: %s', $file));

        $file_content = file_get_contents($file . $this->dist_ext);

        preg_match_all($this->reg_exp, $file_content, $res);

        if (count($res[1])){
            $res[0] = array_unique($res[0]);
            $res[1] = array_unique($res[1]);

            foreach ($res[1] as $k => $v){
                if (strpos($v, '|') !== false){
                    $ph = explode('|', $v);
                    $answer = $this->event->getIO()->ask(sprintf('    Value of %s (%s): ', $ph[0], $ph[1]), '');
                    if (!$answer){
                        $answer = $ph[1];
                    }
                }else {
                    $answer = $this->event->getIO()->ask(sprintf('    Value of %s: ', $v), '');
                }
                $file_content = str_replace($res[0][$k], $answer, $file_content);
            }
        }

        return file_put_contents($file, $file_content);
    }

    /**
     * Backup file
     *
     * @param string $file the file that should be backuped
     * @return bool
     */
    private function backup_file($file){
        if (!is_dir($this->backup_dir)){
            if (!mkdir($this->backup_dir)){
                $this->event->getIO()->write(sprintf('  Error: can not create backup directory (%s) " ', $this->backup_dir));
                return false;
            }
        }

        $backup_file = $this->backup_dir . basename($file) . ' - ' . date('d-m-Y H:I:s');

        if (copy($file, $backup_file)){
            $this->event->getIO()->write(sprintf('  File was backuped from "%s" to "%s"', $file, $backup_file));
            return false;
        }else{
            $this->event->getIO()->write(sprintf('  Error: can not backup file from "%s" to "%s"', $file, $backup_file));
            return true;
        }
    }
}