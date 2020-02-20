# A lightweight package to execute commands over an SSH connection, based on `spatie/ssh`, with php 5.6 compatibility.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chris-doehring/ssh.svg?style=flat-square)](https://packagist.org/packages/spatie/ssh)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/chris-doehring/ssh/run-tests?label=tests)](https://github.com/spatie/ssh/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/chris-doehring/ssh.svg?style=flat-square)](https://packagist.org/packages/spatie/ssh)

This package is a fork of the original [spatie/ssh](https://github.com/spatie/ssh) package to be compatible with php5.6. *Please use it with caution.*

You can execute an SSH command like this:

```php
Ssh::create('user', 'host')->execute('your favorite command');
```

It will return an instance of [Symfony's `Process`](https://symfony.com/doc/3.3/components/process.html).

## Support Spatie

As Spatie is the original creator of this package, please consider [supporting them](https://spatie.be/open-source/support-us) or checkout another of their great [open source packages](https://spatie.be/open-source).

## Installation

You can install this package via composer:

```bash
composer require chris-doehring/ssh
```

## Usage

You can execute an SSH command like this:

```php
$process = Ssh::create('user', 'example.com')->execute('your favorite command');
```

It will return an instance of [Symfony's `Process`](https://symfony.com/doc/3.3/components/process.html).

### Getting the result of a command

To check if your command ran ok

```php
$process->isSuccessful();
```


This is how you can get the output

```php
$process->getOutput();
```


### Running multiple commands

To run multiple commands pass an array to the execute method.

```php
$process = Ssh::create('user', 'example.com')->execute([
   'first command',
   'second command',
]);
```

### Choosing a port

You can choose a port by passing it to the constructor.


```php
$port = 123;

Ssh::create('user', 'host', $port);
```

Alternatively you can use the `usePort` function:

```php
Ssh::create('user', 'host')->usePort($port);
```


### Specifying the private key to use

You can use `usePrivateKey` to specify a path to a private SSH key to use.

```php
Ssh::create('user', 'host')->usePrivateKey('/home/user/.ssh/id_rsa');
```

### Disable Strict host key checking

By default, strict host key checking is enabled. You can disable strict host key checking using `disableStrictHostKeyChecking`.

```php
Ssh::create('user', 'host')->disableStrictHostKeyChecking();
```

### Uploading & downloading files and directories

You can upload files & directories to a host using:

```php
Ssh::create('user', 'host')->upload('path/to/local/file', 'path/to/host/file');
```

Or download them:

```php
Ssh::create('user', 'host')->download('path/to/host/file', 'path/to/local/file');
```

Under the hood the process will use `scp`.

### Modifying the Symfony process

Behind the scenes all commands will be performed using [Symfonys `Process`](https://symfony.com/doc/3.3/components/process.html).

You can configure to the `Process` by using the `configureProcess` method. Here's and example where we disable the timeout.

```php
Ssh::create('user', 'host')->configureProcess(fn (Process $process) => $process->setTimeout(null));
```

### Immediately responding to output

You can get notified whenever you command produces output by setting by passing a closure to `onOuput`. 

```php
Ssh::create('user', 'host')->onOutput(fn($type, $line) => echo $line)->execute('whoami');
```

Whenever there is output that close will get called with two parameters:
- `type`: this can be `Symfony\Component\Process\Process::OUT` for regular output and `Symfony\Component\Process\Process::ERR` for error output
- `line`: the output itself

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

This package has been forked to be compatible with the deprecated php version `5.6`. Please consider using php `7.4` and the most recent original package from [spatie/ssh](https://github.com/spatie/ssh).
If you have to use it anyway, be sure to run it on the [security backport version of Microsoft](https://github.com/microsoft/php-src). Neither I nor any other contributors give any warranty when running this fork under php 5.6.

## Alternatives

  If you need some more features, take a look at [DivineOmega/php-ssh-connection](https://github.com/DivineOmega/php-ssh-connection).

## Credits

This package is completely based on [spatie/ssh](https://github.com/spatie/ssh). If you like it, please consider [supporting them](https://spatie.be/open-source/support-us).

- [Chris Döhring](https://github.com/chris-doehring) (php 5.6 integration)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

The `Ssh` class contains code taken from [laravel/envoy](https://laravel.com/docs/6.x/envoy)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
