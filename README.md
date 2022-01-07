# Terminus Power Tools Plugin

Terminus Power Tools Tools is a Terminus Plugin that contains a collection of commands and configuration useful for Decoupled projects using Terminus Build Tools.

Learn more about Terminus Plugins in the
[Terminus Plugins documentation](https://pantheon.io/docs/terminus/plugins)


## Provided Commands
* `terminus build:addons:enable`     Enables Pantheon site plan New Relic and Redis add-ons.
* `terminus  build:lando:setup`         Sets up and configures Lando local development environment from template.



## Default Configuration
This plugin provided enhanced default Terminus Build Tools configuration used in the `terminus build:project:create` command. Any configuration can be overriden by passing the desired value in the cli or via the methods described in https://github.com/pantheon-systems/terminus-build-tools-plugin#configuration.




## Installation

To install this plugin using Terminus 3:
```
terminus self:plugin:install lcatlett/terminus-power-tools
```

