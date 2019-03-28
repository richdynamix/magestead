# [Magestead](http://www.magestead.com "Magestead")

# No longer maintained

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/richdynamix/magestead/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/richdynamix/magestead/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/richdynamix/magestead/badges/build.png?b=master)](https://scrutinizer-ci.com/g/richdynamix/magestead/build-status/master) [![GitHub release](https://img.shields.io/github/release/richdynamix/magestead.svg)](https://github.com/richdynamix/magestead)

Magestead 2.0 is the perfect development toolbox to manage and control your Magento development workflow. A command line utility that will not only get you a custom pre-configured vagrant development environment with the tools you want, but also install the latest version of Magento or Magento 2

### System Requirements

- VirtualBox 5+
- Vagrant 1.8.1+
- PHP 5.4+ (with mcrypt, intl & xsl extensions) installed
- Composer installed globally
- Mac or Linux (sorry no windows support yet)

**Note**: *For mac users missing the required extensions you could install the latest PHP version for your Mac OS version using [http://php-osx.liip.ch/](http://php-osx.liip.ch/).*

To test your PHP installation run the following in your terminal - 

`php -ini | grep intl`

You should see something similar -

``` /usr/local/php5/php.d/50-extension-intl.ini,
intl
intl.default_locale => no value => no value
intl.error_level => 0 => 0
intl.use_exceptions => 0 => 0 
```

`php -ini | grep mcrypt`

You should see something similar -

```
mcrypt
mcrypt support => enabled
mcrypt_filter support => enabled
mcrypt.algorithms_dir => no value => no value
mcrypt.modes_dir => no value => no value
```

#### Optional Requirements

- [vagrant-hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater) - *A Vagrant plugin for updating your hosts file automatically with your project URL*
- [vagrant-bindfs](https://github.com/gael-ian/vagrant-bindfs) - * - A Vagrant plugin to automate bindfs mount in the VM*

**Note: While these Vagrant plugins are only an optional requirement, they are highly recomended.**

### Installing

Magestead uses Composer to manage it's dependencies. It is important that you have this installed prior to trying to install Magestead.

Download and install Magestead globally using Composer: 

```
$ composer global require "richdynamix/magestead"
```

Make sure to place the `~/.composer/vendor/bin` directory in your PATH so the `magestead` executable can be located by your system.

### Updating

```
$ composer global update "richdynamix/magestead"
```


## Usage

Once installed, the `magestead new` command will start a fresh new development environment in the directory you specify. For instance, `magestead new my-project` will create a directory named `my-project` and start the setup process for your new development environment.

## Documentation

View the docs [here](http://www.magestead.com/#docs)

## Built With

* PuPHPet
* Vagrant
* Symfony Console

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## Authors

* **Steven Richardson** - *Core Author*

See also the list of [contributors](https://github.com/richdynamix/magestead/contributors) who participated in this project.

## History

See the previous [releases](https://github.com/richdynamix/magestead/releases) for project history

## Acknowledgments

Heavily inspired from the following -

* Laravel Homestead
* ScotchBox 2.0
