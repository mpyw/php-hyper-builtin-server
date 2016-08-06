# PHP Hyper Built-in Server

Reverse proxy for PHP built-in server which supports multiprocessing and TLS/SSL encryption.

## Installing

For development environment...

```
composer require --dev mpyw/php-hyper-builtin-server:^1.0
```

## Usage

Start listening on `http://localhost:8080` and `https://localhost:8081`.

```
vendor/bin/hyper-run <DocumentRoot>
```
