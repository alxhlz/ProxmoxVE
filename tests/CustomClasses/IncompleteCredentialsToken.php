<?php

namespace ProxmoxVE\CustomClasses;

class IncompleteCredentialsToken
{
    public function __construct($token_name, $token_value)
    {
        $this->token_name = $token_name;
        $this->token_value = $token_value;
    }
}
