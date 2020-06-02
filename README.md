# PHP Hyper Built-in Server [![Build Status](https://travis-ci.com/mpyw/php-hyper-builtin-server.svg?branch=master)](https://travis-ci.com/mpyw/php-hyper-builtin-server) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/php-hyper-builtin-server/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/php-hyper-builtin-server/?branch=master)

Reverse proxy for PHP built-in server which supports multiprocessing and TLS/SSL encryption.

## Installing

### Global install

```shell script
composer global require mpyw/php-hyper-builtin-server:^3.0
```

If not yet, you must add **`~/.composer/vendor/bin`** to `$PATH`.  
Append the following statement to `~/.bashrc`, `~/.zshrc` or what not.

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

### Local install only for development environment

```shell script
composer require --dev mpyw/php-hyper-builtin-server:^3.0
```

Use **`vendor/bin/hyper-run`** as the execution path.

## Usage

### Quick start

```shell script
hyper-run -S localhost -s localhost -t src/app/www
```

2 servers will start with the directory `src/app/www` as the document root:

- `http://localhost:8000`
- `https://localhost:44300`

Servers start with first unoccupied port within range depending on a scheme.

| Scheme  | Default | Range       |
| ------- | ------- | ----------- |
| `HTTP`  | 8000    | 8000-8099   |
| `HTTPS` | 44300   | 44300-44399 |

### Customize ports

```shell script
hyper-run -S localhost:8080 -s localhost:4000 -t src/app/www
```

2 servers will start with the directory `src/app/www` as the document root:

- `http://localhost:8080`
- `https://localhost:4000`

### Command Reference

```ShellSession
mpyw@localhost:~$ hyper-run -h

Usage:
    hyper-run <options>

Example:
    hyper-run -S localhost:8000 -s localhost:44300

[Required]
    -S   "<Host>:<Port>" of an HTTP server. Multiple arguments can be accepted.
    -s   "<Host>:<Port>" of an HTTPS server. Multiple arguments can be accepted.

[Optional]
    -n   The number of PHP built-in server clusters, from 1 to 20. Default is 10.
    -t   Path for the document root. Default is the current directory.
    -r   Path for the router script. Default is empty.
    -c   Path for the PEM-encoded certificate.
         Default is "/Users/mpyw/.composer/vendor/mpyw/php-hyper-builtin-server/certificate.pem".

Restrictions:
    - The option -s is only supported on PHP 5.6.0 or later.
    - Access logs will not be displayed on Windows.

mpyw@localhost:~$
```

## Note for Windows users

Unfortunately, `cmd.exe` has no option to run via shebang `#!/usr/bin/env php`, so you need to create the following batch file in the proper directory.

### For Standalone PHP

```bat
@echo OFF
"C:\php\php.exe" "%HOMEPATH%\.composer\vendor\mpyw\php-hyper-builtin-server\hyper-run" %*
```

### For XAMPP

```bat
@echo OFF
"C:\xampp\php\php.exe" "%HOMEPATH%\.composer\vendor\mpyw\php-hyper-builtin-server\hyper-run" %*
```

## License

- `PHP Hyper Built-in Server` is open-sourced software licensed under the [MIT license](LICENSE) by [@mpyw](https://github.com/mpyw).
