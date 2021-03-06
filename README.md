Rivera Framework
==========

:warning: This project is under construction
* Do not attempt to use in prod environment
* PM me for requests/suggestions

[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Features

This repo contains a light PHP framework supporting :

* Applications (MVC, Cli)
* Database connection (MySQL) and query builder
* ORM with command to generate models/classes from database schema
* Cache management (Disk, Memcached) with support for redundancy
* Session management (PHP, Memcached) with support for redundancy
* Translations management (Classes, libs, templates)
* Routing and link generation
* Helpers: rf_*()
* Various classes for API management, data processing, logging, custom exceptions, etc.

## Requirements

This project has been tested on Linux/Unix and Windows using PHP 7+ but should be compatible with PHP 5.6+.

## Installation

Using composer:

    composer require riveraframework/rf-core:dev-develop

## Documentation

SOON

## Examples

Skeleton application available at:
https://github.com/riveraframework/example-skeleton

REST API application available at:
https://github.com/riveraframework/example-rest-api

OAuth2 API/SSO application available at:
https://github.com/riveraframework/example-oauth2-api-sso