# Magestead

#### Introduction
While there is no perfect vagrant box setup that will fit the needs of everyone, there are many out there that do the job brilliantly. Inspired by the amazing [Laravel Homestead](http://laravel.com/docs/5.1/homestead "Laravel Homestead") & [ScotchBox](https://box.scotch.io/ "ScotchBox"), Magestead will fit the needs of most Magento developers who may also work in multiple different frameworks and don’t want the hassle of switching boxes.

Magestead is designed to be configurable like Homestead using the YAML configuration while maintaining a much easier per project workflow that ScotchBox offers. The most obvious change in Magestead is the switch from an Ubuntu box to a CentOS box. This switch is to accommodate the developers who work in a Red Hat/CentOS environment everyday and prefer the package managers and comfort of a system they know.

#### What's in the box?
With a CentOS 6.6 core server using NGINX, PHP 5.6, MySQL 5.5, Redis & Memcached, you know you have a development environment with performance!

#### The Full Stack
- CentOS 6.6
- NGINX
- PHP 5.6
- MySQL 5.5
- Redis
- Memcached
- PHPUnit
- xDebug
- Blackfire Profiler
- Magerun
- Modgit
- Modman
- Laravel Installer
- Envoy Installer
- Magento Composer
- Composer
- GIT
- NodeJs
- NPM
- Bower
- Grunt
- Gulp
- Yeoman
- PM2

#### Installation & Setup
There are a few obvious prerequisites before you can use your Magestead environment, you must install [VirtualBox](https://www.virtualbox.org/wiki/Downloads "VirtualBox") and [Vagrant](http://www.vagrantup.com/downloads.html "Vagrant"). Please install the latest versions of these before continuing.

Since Magestead is a per project based vagrant solution we need to clone the Magestead GitHub repository into a new project folder -

`git clone git@github.com:richdynamix/magestead.git my-project-dir `

Once the download is complete you can simply run vagrant up in your project directory i.e.

```
cd my-project-dir; 
vagrant up; 
```

On the very first run of `vagrant up` the vagrant box will need to download, this may take a while depending on your network connection. Grab a coffee…. Check your Facebook.

When vagrant has finished provisioning your box you can access your site at [http://192.168.47.10/](http://192.168.47.10/ "http://192.168.47.10/")

#### Configuring Magestead
Anyone familiar with Laravel Homestead will recognise the Magestead.yaml file. This is pretty much a direct copy of the Homestead.yaml file with some obvious changes and cutbacks.

The first section hasn’t changed other than the default IP address. This was done to resolve any clashes you may have with Laravel Homestead or ScotchBox.

At present Magestead has only been configured to work with virtualbox. This is simply because its free and easy to use. I don’t use any virtualisation software to try and test however you are free to submit a pull request.

The `authorize:` setting will require you to provide your SSH public key, this is used for connecting to the box without the need for a password. Usually you won’t need to edit this as it is configured to use default settings.

The database magestead has been defined for you in `databases:` setting, you are free to add as many of these as you like. Perhaps you need a separate database for a blog.

The `bootstrap:` setting is a new setting to magestead, when activated it will install Magento 1.9.1.0 CE into your public directory using composer. The additional composer libraries like PHPUnit, PhpSpec & Magento Hackathon Composer Installer will also be installed along with the autoloader patch.

#### Launching The Vagrant Box

|                             	|                   	|
|-------------------------------------------	|-------------------	|
| Start or resume your server               	| `vagrant up`      	|
| Pause your server (saving memory state)   	| `vagrant suspend` 	|
| Pause your server (removing memory state) 	| `vagrant halt`    	|
| Delete your server                        	| `vagrant destroy` 	|
| SSH into your server                      	| `vagrant ssh`     	|

#### Blackfire Profiler
[Blackfire Profiler](https://blackfire.io/ "Blackfire Profiler") by SensioLabs automatically gathers data about your code's execution, such as RAM, CPU time, and disk I/O. Homestead makes it a breeze to use this profiler for your own applications.

All of the proper packages have already been installed on your Magestead box, you simply need to set a Blackfire Server ID and token in your Magestead.yaml file:

```
blackfire:
- id: your-server-id
token: your-server-token
client-id: your-client-id
client-token: your-client-token
```

Once you have configured your Blackfire settings, re-provision the box using `vagrant provision`. Before you can use Blackfire you will need to install the companion extension for your web browser. The Blackfire documentation to will explain how to install the Blackfire companion extension
