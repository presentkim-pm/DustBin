# <img src="https://rawgit.com/PresentKim/SVG-files/master/plugin-icons/dustbin.svg" height="50" width="50"> DustBin  
__A plugin for [PMMP](https://pmmp.io) :: Put garbage in the dustbin!__  
  
[![license](https://img.shields.io/github/license/organization/DustBin-PMMP.svg?label=License)](LICENSE)
[![release](https://img.shields.io/github/release/organization/DustBin-PMMP.svg?label=Release)](../../releases/latest)
[![download](https://img.shields.io/github/downloads/organization/DustBin-PMMP/total.svg?label=Download)](../../releases/latest)
[![Build status](https://ci.appveyor.com/api/projects/status/xd18ryl4li9rc11m/branch/master?svg=true)](https://ci.appveyor.com/project/PresentKim/dustbin-pmmp/branch/master)
  
## What is this?   
It is a dustbin to open anytime and anywhere.  
  
  
## Features  
- [x] Support configurable things  
- [x] Check that the plugin is not latest version  
  - [x] If not latest version, show latest release download url  
  
  
## Configurable things  
- [x] Configure the language for messages  
  - [x] in `{SELECTED LANG}/lang.ini` file  
  - [x] Select language in `config.yml` file  
- [x] Configure the command (include subcommands)  
  - [x] in `config.yml` file  
- [x] Configure the permission of command  
  - [x] in `config.yml` file  
- [x] Configure the whether the update is check (default "false")
  - [x] in `config.yml` file  
  
The configuration files is created when the plugin is enabled.  
The configuration files is loaded  when the plugin is enabled.  
  
  
## Command  
Main command : `/dustbin`  
  
  
## Permission  
| permission  | default | description   |  
| ----------- | ------- | ------------- |  
| dustbin.cmd | USER    | main command  |  
  
  
## Demo  
![demo](/assets/screenshot/demo.gif?raw=true)  
