<?php

/**
 * This file is part of the ProxmoxVE PHP API wrapper library (unofficial).
 *
 * @copyright 2014 César Muñoz <zzantares@gmail.com>
 * @license   http://opensource.org/licenses/MIT The MIT License.
 */

namespace ProxmoxVE;

use ProxmoxVE\Exception\MalformedCredentialsException;

/**
 * Credentials class. Handles all related data used to connect to a Proxmox
 * server.
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class Credentials
{
    /**
     * @var string The Proxmox hostname (or IP address) to connect to.
     */
    private $hostname;

    /**
     * @var string The Proxmox hostname (or IP address) to connect to.
     */
    private $method;

    /**
     * @var string The Proxmox authorization header for token based authentication.
     */
    private $authorization_header;

    /**
     * @var string The credentials token_name used to authenticate with Proxmox.
     */
    private $token_name;

    /**
     * @var string The credentials token_value used to authenticate with Proxmox.
     */
    private $token_value;

    /**
     * @var string The credentials username used to authenticate with Proxmox.
     */
    private $username;

    /**
     * @var string The credentials password used to authenticate with Proxmox.
     */
    private $password;

    /**
     * @var string The authentication realm (defaults to "pam" if not provided).
     */
    private $realm;

    /**
     * @var string The Proxmox port (defaults to "8006" if not provided).
     */
    private $port;

    /**
     * Construct.
     *
     * @param array|object $credentials This needs to have 'hostname', ('token_name', 'token_value')
     *                                  or ('username', 'password') defined.
     */
    public function __construct($credentials)
    {
        // Get credentials object in valid array form
        $credentials = $this->parseCustomCredentials($credentials);

        if (!$credentials) {
            $error = 'PVE API needs a credentials object or an array.';
            throw new MalformedCredentialsException($error);
        }

        $this->hostname = $credentials['hostname'];
        $this->username = $credentials['username'];
        $this->realm = $credentials['realm'];
        $this->port = $credentials['port'];

        if ($credentials['method'] == 'token') {
            $this->token_name = $credentials['token_name'];
            $this->token_value = $credentials['token_value'];
            $this->authorization_header = sprintf(
                'PVEAPIToken=%s@%s!%s=%s',
                $this->username,
                $this->realm,
                $this->token_name,
                $this->token_value
            );
            $this->method = 'token';
        } elseif ($credentials['method'] == 'password') {
            $this->password = $credentials['password'];
            $this->method = 'password';
        }
    }


    /**
     * Gives back the string representation of this credentials object.
     *
     * @return string Credentials data in a single string.
     */
    public function __toString()
    {
        if ($this->method == 'token') {
            return sprintf(
                '[Host: %s:%s], [Username: %s@%s], [Token: %s].',
                $this->hostname,
                $this->port,
                $this->username,
                $this->realm,
                $this->token_name
            );
        } elseif ($this->method == 'password') {
            return sprintf(
                '[Host: %s:%s], [Username: %s@%s].',
                $this->hostname,
                $this->port,
                $this->username,
                $this->realm
            );
        }
    }


    /**
     * Returns the base URL used to interact with the ProxmoxVE API.
     *
     * @return string The proxmox API URL.
     */
    public function getApiUrl()
    {
        return 'https://' . $this->hostname . ':' . $this->port . '/api2';
    }


    /**
     * Gets the method that will be used to authenticated against
     * the proxmox api.
     *
     * @return string The method that is used to authenticate against
     *                proxmox api.
     */
    public function getMethod()
    {
        return $this->method;
    }


    /**
     * Gets the full authorization header that will be used
     * to authenticate against the proxmox api instead of using
     * a ticket.
     *
     * @return string The method that is used to authenticate against
     *                proxmox api.
     */
    public function getAuthorizationHeader()
    {
        return $this->authorization_header;
    }


    /**
     * Gets the hostname configured in this credentials object.
     *
     * @return string The hostname in the credentials.
     */
    public function getHostname()
    {
        return $this->hostname;
    }


    /**
     * Gets the token_name given to this credentials object.
     *
     * @return string|null  The token_name in the credentials
     *                      or null if no username was found.
     */
    public function getTokenName()
    {
        return $this->token_name;
    }


    /**
     * Gets the token_value given to this credentials object.
     *
     * @return string|null  The token_value in the credentials
     *                      or null if no username was found.
     */
    public function getTokenValue()
    {
        return $this->token_value;
    }


    /**
     * Gets the username given to this credentials object.
     *
     * @return string|null  The username in the credentials
     *                      or null if no username was found.
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Gets the password set in this credentials object.
     *
     * @return string   The password in the credentials
     *                  or null if no password was found.
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * Gets the realm used in this credentials object.
     *
     * @return string The realm in this credentials.
     */
    public function getRealm()
    {
        return $this->realm;
    }


    /**
     * Gets the port configured in this credentials object.
     *
     * @return string The port in the credentials.
     */
    public function getPort()
    {
        return $this->port;
    }


    /**
     * Given the custom credentials object it will try to find the required
     * values to use it as the proxmox credentials, this can be an array or an
     * object with accesible properties.
     *
     * @param mixed $credentials
     *
     * @return array|null If credentials are found they are returned as an
     *                    associative array, returns null if object can not be
     *                    used as a credentials provider.
     */
    public function parseCustomCredentials($credentials)
    {
        if (is_array($credentials)) {
            return $this->parseCustomCredentialsArray($credentials);
        }

        if (!is_object($credentials)) {
            return null;
        }

        return $this->parseCustomCredentialsObject($credentials);
    }

    /**
     * This is a little helper to parse the given credentials of
     * parseCustomCredentials() as soon as a array is detected
     * as input.
     *
     * @param array $credentials
     *
     * @return array|null If credentials are found they are returned as an
     *                    associative array, returns null if object can not be
     *                    used as a credentials provider.
     */
    private function parseCustomCredentialsArray(array $credentials)
    {

        if (array_key_exists('token_name', $credentials) || array_key_exists('token_value', $credentials)) {
            $requiredKeys = ['hostname', 'username', 'token_name', 'token_value'];
            $credentials['method'] = 'token';
        } else {
            $requiredKeys = ['hostname', 'username', 'password'];
            $credentials['method'] = 'password';
        }

        $credentialsKeys = array_keys($credentials);

        $found = count(array_intersect($requiredKeys, $credentialsKeys));

        if ($found != count($requiredKeys)) {
            return null;
        }

        // Set default realm and port if are not in the array.
        if (!isset($credentials['realm'])) {
            $credentials['realm'] = 'pam';
        }

        if (!isset($credentials['port'])) {
            $credentials['port'] = '8006';
        }

        return $credentials;
    }

    /**
     * This is a little helper to parse the given credentials of
     * parseCustomCredentials() as soon as a array is detected
     * as input.
     *
     * @param object $credentials
     *
     * @return array|null If credentials are found they are returned as an
     *                    associative array, returns null if object can not be
     *                    used as a credentials provider.
     */
    private function parseCustomCredentialsObject(object $credentials)
    {
        // Trying to find variables
        $objectProperties = array_keys(get_object_vars($credentials));
        if (array_key_exists('token_name', $objectProperties) || array_key_exists('token_value', $objectProperties)) {
            $requiredProperties = ['hostname', 'username', 'token_name', 'token_value'];
            $method = 'token';
        } else {
            $requiredProperties = ['hostname', 'username', 'password'];
            $method = 'password';
        }

        // Needed properties exists in the object?
        $found = count(array_intersect($requiredProperties, $objectProperties));
        if ($found == count($requiredProperties)) {
            $realm = in_array('realm', $objectProperties)
                ? $credentials->realm
                : 'pam';

            $port = in_array('port', $objectProperties)
                ? $credentials->port
                : '8006';

            $collectedProperties = [
                'realm' => $realm,
                'port' => $port,
                'method' => $method
            ];

            foreach ($requiredProperties as $property) {
                $collectedProperties[$property] = $credentials->$property;
            }

            return $collectedProperties;
        }
    }
}
