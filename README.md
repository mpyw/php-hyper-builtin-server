# PHP Hyper Built-in Server

Reverse proxy for PHP built-in server which supports multiprocessing and TLS/SSL encryption.  

## Installing

For development environment...

```
composer require --dev mpyw/php-hyper-builtin-server:@dev
```

## Usage

```
Usage:
    vendor/bin/hyper-run <options>

[REQUIRED]
    -S   Server URL such as "https://127.0.0.1:8081".
         When protocol is omitted, it is assumed as "http://".
         When port is omitted, it is assumed as 80(http) or 443(https).
         Multiple arguments can be accepted.
         At least 1 server must be specified.
         Note that "https://" is supported only on PHP 5.6.0 or later.

[OPTIONAL]
    -t   Document Root. Default is current directory.
    -n   The number of PHP built-in server clusters. Default is 10.
    -r   Router script.
```

## Example

```
vendor/bin/hyper-run -S localhost:8080 -S https://localhost:8081 -t src/app/www
```

## Known Bugs

On `https://`, octets are unexpectedly duplicated.
