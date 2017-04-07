# SuluValidationBundle

[![Build Status](https://travis-ci.org/sulu/SuluValidationBundle.svg)](https://travis-ci.org/sulu/SuluValidationBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sulu/SuluValidationBundle/badges/quality-score.png)](https://scrutinizer-ci.com/g/sulu/SuluValidationBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/sulu/SuluValidationBundle/badges/coverage.png)](https://scrutinizer-ci.com/g/sulu/SuluValidationBundle/)
[![StyleCI](https://styleci.io/repos/61883398/shield)](https://styleci.io/repos/61883398)

This bundle validates requests for pre-configured routes.

## How it works

On every request an event listener checks if a schema in `sulu_validation.schemas` is defined for the current route id.

It then uses [json schema validation](http://json-schema.org/) for validate if the request data matches the configured
schema.

## Status

This repository will become version 1.0 of SuluValidationBundle. It is under **heavy development** and currently its
APIs and code are not stable yet (pre 1.0).

## Requirements

* Composer
* PHP `>=5.5`
* Symfony `^2.8 || ^3.2`

For detailed requirements see [composer.json](https://github.com/sulu/SuluValidationBundle/blob/master/composer.json).

## Documentation

The the Documentation is stored in the
[Resources/doc/](https://github.com/sulu/SuluValidationBundle/blob/master/Resources/doc) folder.

## Installation

All the installation instructions are located in the
[Documentation](https://github.com/sulu/SuluValidationBundle/blob/master/Resources/doc/installation.md).

## License

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE)

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Sulu/SuluValidationBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project using the
[Sulu Minimal Edition](https://github.com/sulu/sulu-minimal) to allow developers of the bundle to reproduce the issue
by simply cloning it and following some steps.

