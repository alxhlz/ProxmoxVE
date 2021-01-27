<?php

use ProxmoxVE\{
    Proxmox,
    AuthToken
};
use ProxmoxVE\CustomClasses\{
    Person,
    IncompleteCredentialsToken,
    IncompleteCredentials,
    ProtectedCredentialsToken,
    ProtectedCredentials
};

it('throws exception when bad params passed', function () {
    new Proxmox('bad param');
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when non associative array is given as credentials', function () {
    new Proxmox([
        'root', 'So Bruce Wayne is alive? or did he died in the explosion?',
    ]);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when incomplete credentials array is passed', function () {
    new Proxmox([
        'username' => 'root',
        'password' => 'The NSA is watching us! D=',
    ]);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when wrong credentials object is passed', function () {
    $credentials = new Person('Harry Potter', 13);
    new Proxmox($credentials);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when incomplete credentials object with token auth is passed', function () {
    $credentials = new IncompleteCredentialsToken("token", "00000000-0000-0000-0000-000000000000");
    new Proxmox($credentials);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when incomplete credentials object with password auth is passed', function () {
    $credentials = new IncompleteCredentials("user", "and that's it");
    new Proxmox($credentials);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when protected credentials object with token auth is passed', function () {
    $credentials = new ProtectedCredentials('host', 'token', '00000000-0000-0000-0000-000000000000');
    new Proxmox($credentials);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

it('throws exception when protected credentials object with password auth is passed', function () {
    $credentials = new ProtectedCredentials('host', 'user', 'pass');
    new Proxmox($credentials);
})->throws(\ProxmoxVE\Exception\MalformedCredentialsException::class);

test('get credentials with all values using token auth', function () {
    $ids = [
        'hostname' => 'some.proxmox.tld',
        'username' => 'root',
        'token_name' => 'myapitoken',
        'token_value' => '00000000-0000-0000-0000-000000000000',
    ];

    $fakeAuthToken = new AuthToken('csrf', 'ticket', 'username');
    $proxmox = $this->getMockProxmox('login', $fakeAuthToken);
    $proxmox->setCredentials($ids);

    $credentials = $proxmox->getCredentials();

    $this->assertEquals($credentials->getHostname(), $ids['hostname']);
    $this->assertEquals($credentials->getTokenName(), $ids['token_name']);
    $this->assertEquals($credentials->getTokenValue(), $ids['token_value']);
    $this->assertEquals($credentials->getRealm(), 'pam');
    $this->assertEquals($credentials->getPort(), '8006');
    $this->assertEquals($credentials->getMethod(), 'token');
});

test('get credentials with all values using password auth', function () {
    $ids = [
        'hostname' => 'some.proxmox.tld',
        'username' => 'root',
        'password' => 'I was here',
    ];

    $fakeAuthToken = new AuthToken('csrf', 'ticket', 'username');
    $proxmox = $this->getMockProxmox('login', $fakeAuthToken);
    $proxmox->setCredentials($ids);

    $credentials = $proxmox->getCredentials();

    $this->assertEquals($credentials->getHostname(), $ids['hostname']);
    $this->assertEquals($credentials->getUsername(), $ids['username']);
    $this->assertEquals($credentials->getPassword(), $ids['password']);
    $this->assertEquals($credentials->getRealm(), 'pam');
    $this->assertEquals($credentials->getPort(), '8006');
    $this->assertEquals($credentials->getMethod(), 'password');
});

it('throws exception when hostname is unresolvable', function () {
    $credentials = [
        'hostname' => 'proxmox.example.tld',
        'username' => 'user',
        'password' => 'pass',
    ];

    new Proxmox($credentials);
})->throws(Exception::class);

it('throws exception when credentials are not correct', function () {
    $credentials = [
        'hostname' => 'proxmox.server.tld',
        'username' => 'are not',
        'password' => 'valid folks!',
    ];

    $httpClient = $this->getMockHttpClient(false); // Simulate failed login
    new Proxmox($credentials, null, $httpClient);
})->throws(Exception::class);

test('get and set response types', function () {
    $proxmox = $this->getProxmox(null);
    $this->assertEquals($proxmox->getResponseType(), 'array');

    $proxmox->setResponseType('json');
    $this->assertEquals($proxmox->getResponseType(), 'json');

    $proxmox->setResponseType('html');
    $this->assertEquals($proxmox->getResponseType(), 'html');

    $proxmox->setResponseType('extjs');
    $this->assertEquals($proxmox->getResponseType(), 'extjs');

    $proxmox->setResponseType('text');
    $this->assertEquals($proxmox->getResponseType(), 'text');

    $proxmox->setResponseType('png');
    $this->assertEquals($proxmox->getResponseType(), 'png');

    $proxmox->setResponseType('pngb64');
    $this->assertEquals($proxmox->getResponseType(), 'pngb64');

    $proxmox->setResponseType('object');
    $this->assertEquals($proxmox->getResponseType(), 'object');

    $proxmox->setResponseType('other');
    $this->assertEquals($proxmox->getResponseType(), 'array');
});

it('throws exception when get resource with bad params passed', function () {
    $proxmox = $this->getProxmox(null);
    $proxmox->get('/someResource', 'wrong params here');
})->throws(InvalidArgumentException::class);

it('throws exception when create resource with bad params passed', function () {
    $proxmox = $this->getProxmox(null);
    $proxmox->create('/someResource', 'wrong params here');
})->throws(InvalidArgumentException::class);

it('throws exception when set resource with bad params passed', function () {
    $proxmox = $this->getProxmox(null);
    $proxmox->set('/someResource', 'wrong params here');
})->throws(InvalidArgumentException::class);

it('throws exception when delete resource with bad params passed', function () {
    $proxmox = $this->getProxmox(null);
    $proxmox->delete('/someResource', 'wrong params here');
})->throws(InvalidArgumentException::class);

test('get resource', function () {
    $fakeResponse = <<<'EOD'
    {"data":[{"disk":940244992,"cpu":0.000998615325210486,"maxdisk":5284429824,"maxmem":1038385152,"node":"office","maxcpu":1,"level":"","uptime":3296027,"id":"node/office","type":"node","mem":311635968}]}
    EOD;

    $proxmox = $this->getProxmox($fakeResponse);

    $this->assertEquals($proxmox->get('/nodes'), json_decode($fakeResponse, true));
});
