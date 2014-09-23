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

## Configuration
List of copyconf parameters:
- **files**    - array  - array of files, that should be processed
- **dist_ext** - string - extension of files that should be processed (.dist - default)
- **reg_exp** - string - regular expression used in searching of placeholders

Example of placeholders:
- {{hostname}} - simple placeholder
- {{hostname|localhost}} - placeholder with default value 'localhost'