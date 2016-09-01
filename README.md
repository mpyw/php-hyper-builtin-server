# PHP Hyper Built-in Server

Reverse proxy for PHP built-in server which supports multiprocessing and TLS/SSL encryption.  

## Installing

### Global install

```
composer global require mpyw/php-hyper-builtin-server:^2.0
```

If not yet, you must add **`~/.composer/vendor/bin`** to `$PATH`.  
Append the following statement to `~/.bashrc`, `~/.zshrc` and so on.

```bash
export PATH="~/.composer/vendor/bin:$PATH"
```

### Local install only for development environment

```
composer require --dev mpyw/php-hyper-builtin-server:^2.0
```

Use **`vendor/bin/hyper-run`** as the execution path.

## Usage

```ShellSession
mpyw@localhost:~$ hyper-run -h

Usage:
    hyper-run <options>

Example:
    hyper-run -S localhost:8080 -s localhost:8081

[Required]
    -S   "<Host>:<Port>" of an HTTP server. Multiple arguments can be accepted.
    -s   "<Host>:<Port>" of an HTTPS server. Multiple arguments can be accepted.

[Optional]
    -n   The number of PHP built-in server clusters, 1 to 20. Default is 10.
    -t   Path for document root. Default is the current directory.
    -r   Path for router script. Default is empty.
    -c   Path for PEM-encoded certificate.
         Default is "/Users/mpyw/.composer/vendor/mpyw/php-hyper-builtin-server/certificate.pem".

Restriction:
    - The option -s is supported only on PHP 5.6.0 or later.
    - Access logs are not displayed in Windows.
    
mpyw@localhost:~$
```

## Example

```
hyper-run -S localhost:8080 -s localhost:8081 -t src/app/www
```

It listens on

- `http://localhost:8080`
- `https://localhost:8081`

using the directory `src/app/www` as the document root.

## Note for Windows users

Unfortunately `cmd.exe` has no option to execute via shebang `#!/usr/bin/env php`.  
So you need to create the following batch file to save the proper directory.

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

