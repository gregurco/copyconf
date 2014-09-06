CopyConf
===========

CopyConf - extension for copying config files.

## Usage

Add the following in your root composer.json file:
```json
{
    "require": {
        "gregurco/copyconf": "dev-master"
    },
    "scripts": {
        "post-install-cmd": [
            "Gregurco\\ParameterHandler\\ScriptHandler::buildParameters"
        ],
        "post-update-cmd": [
            "Gregurco\\ParameterHandler\\ScriptHandler::buildParameters"
        ]
    },
    "extra": {
        "copyconf-parameters" : {
            "files": {
                "main_config": "protected/config/config.php"
            }
        }
    }
}
```
