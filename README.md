# SrbTransLatin - Serbian Latinisation

[![Build Status](https://travis-ci.org/seebeen/SrbTransLatin.svg?branch=master)](https://travis-ci.org/seebeen/SrbTransLatin)
[![Maintainability](https://api.codeclimate.com/v1/badges/48c1c02c637f48135b1a/maintainability)](https://codeclimate.com/github/seebeen/SrbTransLatin/maintainability)

## Welcome to the SrbTransLatin GitHub Repository

Although the documentation for the [SrbTransLatin](https://wordpress.org/plugins/srbtranslatin) can be found [on SGi.io](https://rtfm.sgi.io/srbtranslatin), you can browser the source code here, discuss issues and feautres, and contribute yourself, if you want to 🤗

## Installation

You can install the plugin via WordPress plugin manager, or alternatively upload the zip file to **Add Plugin** interface

## Want to contribute?

### Prerequisites

I use specific tools to develop SrbTransLatin. You'll need the following tools installed before you can contribute.

* [Composer](https://getcomposer.org/)
* [npm](https://www.npmjs.com/)
* [gulp](https://gulpjs.com/)

### Getting started

After installing the tools listed above, you can use the steps below to acquire a development version of SrbTransLatin.
This will download the latest dev version of SrbTransLatin, and while it is mostly stable, please do not use it in production environment.

Within your WordPress dev installation, go to `wp-content/plugins` and run the following commands
```bash
git clone https://github.com/seebeen/SrbTransLatin.git
cd srbtranslatin
```

To install all the dependencies please run the following commands:
```bash
composer install
npm install
gulp
```

If you change anything in JS or SCSS files, you'll have to run `gulp` if it's a one-time change, or `gulp watch` if you want to continuously edit the files

## Support

This is a developer's section for SrbTransLatin and should not be used for support. Please visit the [support forums](https://wordpress.org/support/plugin/srbtranslatin) if you need assistance

## Reporting bugs

If you find an issue, please [let me know here](https://github.com/seebeen/SrbTransLatin/issues/new)!

## Contributions

Anyone is welcome to contribute to SrbTransLatin.