MageDownload CLI
===

[![Build Status](https://travis-ci.org/steverobbins/magedownload-cli.svg?branch=master)](https://travis-ci.org/steverobbins/magedownload-cli)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/steverobbins/magedownload-cli/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steverobbins/magedownload-cli/?branch=master)

A PHP tool to automate Magento release and patch downloads

# Installation

### .phar

Download the `.phar` with `wget`

```
wget http://magedownload.steverobbins.com/download/latest/magedownload.phar
```

Or with `cURL`

```
curl -O http://magedownload.steverobbins.com/download/latest/magedownload.phar
```

It's now ready to use: `php magedownload.phar help`

If you want to make it available anywhere on your system

```
chmod +x magedownload.phar
mv magedownload.phar /usr/local/bin/magedownload
```

And can be used: `magedownload info`

[See all releases](http://magedownload.steverobbins.com/download/)

### Source

* Clone this repository
* Install with composer

```
git clone https://github.com/steverobbins/magedownload-cli
cd magedownload-cli
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

### n98 magerun

*Requires version 1.97.6 or higher.*

* Clone to your modules directory
* Install with composer

```
mkdir -p ~/.n98-magerun/modules
cd ~/.n98-magerun/modules
git clone https://github.com/steverobbins/magedownload-cli
cd magedownload-cli
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

# Usage

    $ php magedownload.phar help

## Configuration

The configuration file is in `yaml` format and located at `~/.magedownload-cli.yaml`.

Example:

```
user:
    id: MAG000000000
    token: abcdef1234567890abcdef1234567890abcdef12
```

To generate an access token, login to your account on magento.com and generate it in **Account Settings** -> **Downloads Access Token**.

## Commands

#### Options

All commands have the following options

##### `--id=ID` (optional)

Your Magento account ID.

##### `--token=TOKEN` (optional)

Your Magento access token.

### `configure`

    $ php magedownload.phar configure

or

    $ php n98-magerun.phar download:configure

Configures your account ID and access token.

### `file`

    $ php magedownload.phar file <name> <destination>

or

    $ php n98-magerun.phar download:file <name> <destination>

Downloads the specified file to the given destination.

If no destination is given, the file is downloaded to current directory.

### `info`

    $ php magedownload.phar info <action>

or

    $ php n98-magerun.phar download:info <action>

Gets information about what releases and patches can be downloaded.

Available actions:

* files
* versions

# To Do

* `--extract` option when downloading, unzip/tar/etc contents after download
* Add a progress bar while downloading

# Support

Please [create an issue](https://github.com/steverobbins/magedownload-cli/issues/new) for all bugs and feature requests.

# Contributing

Fork this repository and send a pull request to the `dev` branch.

# License

[Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/)
