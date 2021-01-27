<?php

namespace ProxmoxVE\CustomClasses;

class ProtectedCredentials
{
    protected $hostname;
    protected $token_name;
    protected $token_value;

    public function __construct($host, $token_name, $token_value)
    {
        $this->hostname = $host;
        $this->token_name = $token_name;
        $this->token_value = $token_value;
    }
}
