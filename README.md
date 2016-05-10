# Magestead

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/richdynamix/magestead/badges/quality-score.png?b=epic%2F2.0.0)](https://scrutinizer-ci.com/g/richdynamix/magestead/?branch=epic%2F2.0.0) [![Build Status](https://scrutinizer-ci.com/g/richdynamix/magestead/badges/build.png?b=master)](https://scrutinizer-ci.com/g/richdynamix/magestead/build-status/master) [![GitHub release](https://img.shields.io/badge/release-2.0--beta-blue.svg)](https://github.com/richdynamix/magestead)

The Magestead CLI provides a convenient installer for your magento applications using the pre-configured Vagrant development environment.

The CLI utility will also act as a proxy to the CLI tools installed on the Vagrant machine.

<p align="center">
  <img src="http://www.magestead.com/img/magestead-cli-screen.png" alt="Magestead Screenshot"/>
</p>

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### System Requirements

- VirtualBox 5+
- Vagrant 1.8.1+
- PHP 5.4+ (with mcrypt & intl extensions) installed
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

Vagrant Plugin - [vagrant-hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater) - *For updating your hosts file automatically with your project URL*

### Installing (2.0 Beta only)

During the beta release the installation is a manual process.

Choose a location to install the CLI app on your machine, your home directory would be fine for this situation. 

```
$ cd ~
$ git clone git@github.com:richdynamix/magestead.git magestead
$ cd magestead
$ git checkout -b 2.0.rc1 origin/release/2.0.rc1
```

Install all the required dependencies with Composer

```
composer install
```

Create an alias to your local installation.

```
alias magestead="~/magestead/magestead"
```

**Note:** *Please choose the correct location for your shell i.e.* `.bash_profile`, `config.fish`, `.zshrc`

### Upgrade to 2.0 RC 1

Change into your magestead installation directory and checkout out the new branch

```
$ cd ~/magestead
$ git checkout -b 2.0.rc1 origin/release/2.0.rc1
```

## Usage

Run the Magestead setup

```
$ magestead new project-name
```

Follow the on screen instructions to install the application and server you require.

Go grab a coffee, it cane take several minutes to run, depending on your settings.

## Caveats & Known Issues

While Magestead 2.0 is in Beta, please be aware that there may be unreported bugs. Please create a new issue for these and explain the version you are on.

#### PHP-FPM & PHP7

There is a known issue that prevents the restart of PHP-FPM when using PHP7. This is the case for preconfigured boxes as well as custom boxes. Until the bug has been resolved globally the only way to restart PHP-FPM is to manually kill the process then restart.

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

## License

TODO - Add licence notes

## Acknowledgments

Heavily inspired from the following -

* Laravel Homestead
* ScotchBox 2.0
