CopyConf
===========

CopyConf - extension for copying config files.

## Usage

Add the following in your root composer.json file:
```json
{
    "require": {
        "gregurco/copyconf": "v1.1"
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
- **files**       - array  - array of files, that should be processed
- **dist_ext**    - string - extension of files that should be processed (.dist - default)
- **reg_exp**     - string - regular expression used in searching of placeholders
- **backup_mode** - string - by default **"false"** - not backup overwritten files. Can be set value of **"ask"** or **"true"** to ask on overwriting files or not overwrite in silent mode.
- **backup_dir**  - string - directory where to write backups (backup/ - default)

Example of placeholders:
- {{hostname}} - simple placeholder
- {{hostname|localhost}} - placeholder with default value 'localhost'

## Examples
Example of "extra" with backup:
```json
{
    ...
    "extra": {
        "copyconf-parameters" : {
            "files": {
                "main_config": "protected/config/config.php"
            },
            "backup_mode": "ask",
            "backup_dir" : "backup/"
        }
    }
}
```