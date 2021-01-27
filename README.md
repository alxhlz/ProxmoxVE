ProxmoxVE API Client
====================

This **PHP 7.2+** library allows you to interact with your Proxmox server via API.

![Test pipeline](https://github.com/lehuizi/ProxmoxVE/workflows/Test%20pipeline/badge.svg?branch=main)
[![Latest Stable Version](https://poser.pugx.org/lehuizi/proxmoxve/v/stable.svg)](https://packagist.org/packages/zzantares/proxmoxve)
[![Total Downloads](https://poser.pugx.org/lehuizi/proxmoxve/downloads.svg)](https://packagist.org/packages/zzantares/proxmoxve)
[![License](https://poser.pugx.org/zzantares/proxmoxve/license.svg)](https://packagist.org/packages/zzantares/proxmoxve)

Installation
------------

Recommended installation is using [Composer], if you do not have [Composer] what are you waiting?

In the root of your project execute the following:

```sh
$ composer require lehuizi/proxmoxve
```

Or add this to your `composer.json` file:

```json
{
    "require": {
        "lehuizi/proxmoxve": "~5.0"
    }
}
```

Then perform the installation:
```sh
$ composer install --no-dev
```


Usage
-----

```php
<?php

// Require the autoloader
require_once 'vendor/autoload.php';

// Use the library namespace
use ProxmoxVE\Proxmox;

// Create your credentials array
$credentials = [
    'hostname' => 'proxmox.server.com',  // Also can be an IP Address
    'username' => 'root',
    'password' => 'secret',
];

// Realm and port defaults to 'pam' and '8006' but you can specify them like so
$credentials = [
    'hostname' => 'proxmox.server.com',
    'username' => 'root',
    'password' => 'secret',
    'realm' => 'pve',
    'port' => '9009',
];

// It is also possible to authenticate against your proxmox server using api tokens
$credentials = [
    'hostname' => 'proxmox.server.com',
    'username' => 'root',
    'token_name' => 'mytoken',
    'token_value' => '00000-00000-000000000000'
];

// Then simply pass your credentials when creating the API client object.
$proxmox = new Proxmox($credentials);

$nodes = $proxmox->get('/nodes');

print_r($nodes);
```


Sample output:

```php
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [disk] => 2539465464
                    [cpu] => 0.031314446882002
                    [maxdisk] => 30805066770
                    [maxmem] => 175168446464
                    [node] => mynode1
                    [maxcpu] => 24
                    [level] =>
                    [uptime] => 139376
                    [id] => node/mynode1
                    [type] => node
                    [mem] => 20601992182
                )

        )

)
```


License
-------

This project is released under the MIT License. See the bundled [LICENSE] file for details.

[LICENSE]:./LICENSE
=======

Want to contribute?
--------------------

Thank you! Take a look at the [CONTRIBUTING], you could easily set up a development environment to get you started in no time!


[LICENSE]:./LICENSE
[CONTRIBUTING]:./CONTRIBUTING.md
[PVE2 API Documentation]:http://pve.proxmox.com/pve-docs/api-viewer/index.html
[ProxmoxVE API]:http://pve.proxmox.com/wiki/Proxmox_VE_API
[Proxmox wiki]:http://pve.proxmox.com/wiki
[Composer]:https://getcomposer.org/
