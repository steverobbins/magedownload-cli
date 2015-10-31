MageDownload CLI
===

[![Build Status](https://travis-ci.org/steverobbins/magedownload-cli.svg?branch=master)](https://travis-ci.org/steverobbins/magedownload-cli)

A PHP tool to automate Magento release and patch downloads

# Installation

### .phar

You can download the latest `.phar` from the [releases](https://github.com/steverobbins/magedownload-cli/releases) section.

### Source

* Clone this repository
* Install with composer

```
git clone https://github.com/steverobbins/magedownload-cli
cd magedownload-cli
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

# Usage

    $ magedownload.phar help

## Configuration

The configuration file is in `yaml` format an located at `~/.magedownload-cli.yaml`.

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

### `download`

    $ magedownload.phar download <file> <destination>

Downloads the specified file to the given destination.

If no destination is given, the file is downloaded to the directory the command is executed from.

### `info`

    $ magedownload.phar info <action>

Gets information about what releases and patches can be downloaded.

Available actions:

* files
* versions

# Support

Please [create an issue](https://github.com/steverobbins/magedownload-cli/issues/new) for all bugs and feature requests.

# Contributing

Fork this repository and send a pull request to the `dev` branch.

# License

[Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/)
