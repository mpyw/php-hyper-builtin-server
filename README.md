# PHP Hyper Built-in Server

Reverse proxy for PHP built-in server which supports multiprocessing and TLS/SSL encryption.  

## Installing

Currently you need the following settings.

```json
{
    "require-dev": {
        "mpyw/php-hyper-builtin-server": "@dev"
    },
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mpyw-forks/socket.git"
        }
    ]
}
```

## Usage

```
Usage:
    vendor/bin/hyper-run <options>

[Required]
    -S   Server URL such as "127.0.0.1:8080" or "https://127.0.0.1:8081".
         When protocol is omitted, it is assumed as "http://".
         When port is omitted, it is assumed as 80(http) or 443(https).
         Multiple arguments can be accepted.
         At least 1 server must be specified.
    -s   The same as -S but the default protocol is "https://".

[Optional]
    -n   The number of PHP built-in server clusters. Default is 10.
    -t   Path for document root. Default is the current directory.
    -r   Path for router script. Default is empty.
    -c   Path for alternative PEM-encoded certificate.
         Default is "....../certificate.pem".

[Restriction]
    - "https://" is supported only on PHP 5.6.0 or later.
    - Access logs are not displayed in Windows.
```

## Example

```
vendor/bin/hyper-run -S localhost:8080 -s localhost:8081 -t src/app/www
```

It listens...

- `http://localhost:8080`
- `https://localhost:8081`

Note for Windows users:

cmd.exe has no option to execute via shebang `#!/usr/bin/env php`,  
so you need to use `php vendor/bin/hyper-run` instead of `vendor/bin/hyper-run`.
