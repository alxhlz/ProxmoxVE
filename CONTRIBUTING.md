Welcome developers!
===================

Before making any contribution you should know:

- **Development of new features happens in the `dev` branch**, when this branch is stable enough it will merge with `main` and a new release will be made. Critical bug fixes should also happen in a different branch than `main` and will be merged too.
- In your fork you can add changes to which ever branch you want, but if you submit a PR do so against the `dev` branch or as a new branch altogether. **Any PR made to the `main` branch will be rejected**.
- All **code contribution should follow PSR-2** coding style standards.
- You should **code the tests for any function you add**, some times is not possible but try doing it. If you need help on that, just ask. :)
- **All functions need to be properly documented**, all comments, variable names, function names only on english language.
- Variables and functions names should be self descriptive.


Installation
------------

Of course you should [fork the repo](https://github.com/lehuizi/ProxmoxVE/fork), then after cloning your forked repo:

```sh
$ composer install --dev  # Run command inside the project folder
```

Using docker
------------

If you have another conflicting PHP setup or you don't have any setup at all and you just want to code, you can use the `lehuizi/php-proxmoxve` [docker image](https://hub.docker.com/r/lehuizi/php-proxmoxve) to have a complete development environment.

After you have cloned your forked project, and with docker installed on your machine, inside the project directory, just run:

``` sh
$ docker pull lehuizi/php-proxmoxve
$ docker run -v $(pwd):/root/proxmoxve -it lehuizi/php-proxmoxve
```

Alternatively, the repository ships with a `Dockerfile` which also can be used by contributors in order to build that same image instead of pulling from the docker.io registry.

``` sh
$ docker build -t php-proxmoxve .
$ docker run -v $(pwd):/root/proxmoxve -it php-proxmoxve
```

Inside the container you have all the PHP extensions needed to develop PHP code for this project. The only step left is to install the project dependencies with composer:

``` sh
$ composer install  # Run inside the container
```

Remember to use the container only to test the application, you can still code and commit in your local computer. The container only provides the environment.

Tests
----------------------

Try to just create pull requests as soon as all tests are passing. I would appreciate if you write your own tests as soon as you implement something new.

``` sh
$ ./vendor/bin/pest
```

What needs to be done?
----------------------

What ever you think will improve this library and also you could check out the open issues. Any help is gladly welcome, thanks for your consideration!
