# SrbTransLatin - Serbian Latinisation

[![Build Status](https://travis-ci.org/seebeen/SrbTransLatin.svg?branch=master)](https://travis-ci.org/seebeen/SrbTransLatin)
[![Latest Stable Version](https://poser.pugx.org/sgi/srbtranslatin/v/stable)](https://packagist.org/packages/sgi/srbtranslatin)
[![License](https://poser.pugx.org/sgi/srbtranslatin/license)](https://packagist.org/packages/sgi/srbtranslatin)
[![Maintainability](https://api.codeclimate.com/v1/badges/48c1c02c637f48135b1a/maintainability)](https://codeclimate.com/github/seebeen/SrbTransLatin/maintainability)


## Welcome to the SrbTransLatin GitHub Repository

Although the documentation for the [SrbTransLatin](https://wordpress.org/plugins/srbtranslatin) can be found [on SGi.io](https://rtfm.sgi.io/srbtranslatin), you can browser the source code here, discuss issues and feautres, and contribute yourself, if you want to 🤗

## Installation

You can install the plugin via WordPress plugin manager, or alternatively upload the zip file to **Add Plugin** interface

## Want to contribute?

### Prerequisites

I use specific tools to develop SrbTransLatin. You'll need the following tools installed before you can contribute.

* PHP 8.4.1–8.4.x and [Composer](https://getcomposer.org/)
* Node.js 26 and [npm](https://www.npmjs.com/)
* [Podman](https://podman.io/), WP-CLI, a MySQL client, curl and unzip for integration tests

### Getting started

After installing the tools listed above, you can use the steps below to acquire a development version of SrbTransLatin.
This will download the latest dev version of SrbTransLatin, and while it is mostly stable, please do not use it in production environment.

Within your WordPress dev installation, go to `wp-content/plugins` and run the following commands
```bash
git clone https://github.com/oblakstudio/srbtranslatin.git
cd srbtranslatin
```

To install all the dependencies please run the following commands:
```bash
composer install
npm ci
npm run build
```

Run `npm run build` after changing block assets, or `npm start` while developing.

Run the integration tests against WordPress with:

```bash
composer test:install
composer test
npm run test:package
composer test:env:stop
```

`composer test:install` starts the local database through `podman compose` and downloads WordPress and its matching test suite into `.cache`. The WordPress harness currently requires PHPUnit 9.6; its newest compatible release is pinned in `composer.lock`.

The settings page uses the native WordPress Settings API and reads existing 3.x options until the first save. Bundled Serbian translations cover the current PHP and block-editor strings.

Production packages require `composer install --no-dev --optimize-autoloader --classmap-authoritative`, built block assets, `src`, and `languages`. The release staging guard rejects incomplete packages, build-environment artifacts, symlinks and PHPUnit development dependencies. Node dependencies are never shipped. Transitive npm development-tool advisories remain upstream; do not use forced major overrides merely to make the audit report clean.

The scoped `copy-webpack-plugin` override upgrades the copy tool used by `@wordpress/scripts` to version 14, resolving its vulnerable serializer dependency. The Node requirement is already covered by the project's Node 26 target. Build output and block-metadata copying/transformation were checked for compatibility. `npm` still bundles an affected `undici` version; npm overrides do not replace that bundled copy.

## Support

This is a developer's section for SrbTransLatin and should not be used for support. Please visit the [support forums](https://wordpress.org/support/plugin/srbtranslatin) if you need assistance

## Reporting bugs

If you find an issue, please [let me know here](https://github.com/seebeen/SrbTransLatin/issues/new)!

## Contributions

Anyone is welcome to contribute to SrbTransLatin.
