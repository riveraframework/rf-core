<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Uri;

/**
 * Class Uri
 *
 * @since 1.0
 *
 * @package Rf\Core\Uri
 */
class Uri {
    
    /**
     * @var string Uri protocol
     * @since 1.0
     */
    protected $protocol;
    
    /**
     *
     * @var array Uri credentials
     * @since 1.0
     */
    protected $credentials;
    
    /**
     * @var string Uri host
     * @since 1.0
     */
    protected $host;
    
    /**
     * @var int Uri host type
     * @since 1.0
     */
    protected $hostType;
    
    /**
     * @var int Uri port
     * @since 1.0
     */
    protected $port;
    
    /**
     * @var string Uri query
     * @since 1.0
     */
    protected $query;
    
    /**
     * @var array Uri query params
     * @since 1.0
     */
    protected $queryString;

    /**
     * @var string $tmpUri
     * @since 1.0
     */
    protected $tmpUri;

    /**
     *
     * @since 1.0
     */
    const INIT_WITH_CURRENT_URI = 1;

    /**
     *
     * @since 1.0
     */
    const HOST_TYPE_DOMAIN = 1;

    /**
     *
     * @since 1.0
     */
    const HOST_TYPE_IPV4 = 2;

    /**
     *
     * @since 1.0
     */
    const HOST_TYPE_IPV6 = 3;

    /**
     * IPv4 regex pattern
     * @since 1.0
     */
    const REGEX_PATTERN_IPV4 = '/^((?:25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9]).){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])$/';

    /**
     * IPv6 regex pattern
     * @since 1.0
     */
    const REGEX_PATTERN_IPV6 = '/^\s*((([0-9A-Fa-f]{1,4}:){7}
                                (([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}
                                (:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|
                                [01]?\d{1,2})){3})|(:[0-9A-Fa-f]{1,4})))|
                                (([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
                                (\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
                                ((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){4}
                                (:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
                                (\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
                                ((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}
                                (:[0-9A-Fa-f]{1,4}){0,2}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
                                (\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|
                                (([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}((:((25[0-5]|2[0-4]\d|
                                [01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
                                ((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4}){0,4}
                                ((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
                                ((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]\d|
                                [01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
                                ((:[0-9A-Fa-f]{1,4}){1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})
                                (\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?\s*$/';

    /**
     * Create a new Uri object
     *
     * @since 1.0
     *
     * @param mixed $uri
     * @return \Rf\Core\Uri\Uri
     */
    public function __construct($uri = null) {
        if(isset($uri)) {
            if($uri === self::INIT_WITH_CURRENT_URI) {
                $this->parse(self::current());
            } else {
                $this->parse($uri);
            }
        }
        
        return $this;
    }

    /**
     * Parse an uri and store the components in the current object
     *
     * @since 1.0
     *
     * @param string $uri Uri to parse
     * @return void
     */
    public function parse($uri) {
        $this->tmpUri = $uri;
        $this->reset();
        $this->parseProtocol();
        $this->parseHost();
        $this->parseQuery();
        $this->parseQueryString();
        $this->tmpUri = null;
        
    }

    /**
     * Parse the protocol part of the current Uri object
     *
     * @since 1.0
     *
     * @return void
     */
    protected function parseProtocol() {
        if (preg_match('/^([A-Za-z][A-Za-z0-9\.\+\-]*):\/\//', $this->tmpUri, $match)) {
            $this->protocol = $match[1];
            $this->tmpUri = substr($this->tmpUri, strlen($this->protocol) + 3);
        }
    }

    /**
     * Parse the host part of the current Uri object
     *
     * @since 1.0
     *
     * @return void
     */
    protected function parseHost() {
        $explode = explode('/', $this->tmpUri, 2);
        $hostFull = $explode[0];
        $parts = explode('@', $hostFull);
        if(count($parts) > 1) {
            $credentialsPart = $parts[0];
            $this->credentials = explode(':', $credentialsPart);
            $hostPart = $parts[1];
        } else {
            $hostPart = $parts[0];
        }
        
        // Get host and port
        $parts2 = explode(':', $hostPart);
        $host = $parts2[0];
        $port = isset($parts2[1]) ? $parts2[1] : null;
        $this->host = $host;
        $this->port = $port;
        
        // Check host type
        if(preg_match(self::REGEX_PATTERN_IPV4, $this->host)) {
            $this->hostType = self::HOST_TYPE_IPV4;
        } elseif(preg_match(self::REGEX_PATTERN_IPV6, $this->host)) {
            $this->hostType = self::HOST_TYPE_IPV6;
        } else {
            $this->hostType = self::HOST_TYPE_DOMAIN;
        }
        
        $this->tmpUri = isset($explode[1]) ? $explode[1] : '';
    }

    /**
     * Parse the query part of the current Uri object
     *
     * @since 1.0
     *
     * @return void
     */
    protected function parseQuery() {
        $queryFull = explode('?', $this->tmpUri, 2);
        $this->query = $queryFull[0];
        $this->tmpUri = isset($queryFull[1]) ? $queryFull[1] : '';
    }

    /**
     * Parse the query params of the current Uri object
     *
     * @since 1.0
     *
     * @return void
     */
    protected function parseQueryString() {
        if(!empty($this->tmpUri)) {
            $explode = explode('&', $this->tmpUri);
            if(count($explode) > 0) {
                foreach($explode as $param) {
                    $param = explode('=', $param, 2);
                    if(!empty($param[0])) {
                        $this->queryString[$param[0]] = (empty($param[1]) ? '' : $param[1]);
                    }
                }
            }
        }
    }

    /**
     * Reset the uri parts of the current Uri object
     *
     * @since 1.0
     *
     * @return void
     */
    protected function reset() {
        $this->protocol = null;
        $this->credentials = null;
        $this->host = null;
        $this->port = null;
        $this->query = null;
        $this->queryString = null;
    }

    /**
     * Get/set the protocol part of the current Uri object
     *
     * @since 1.0
     *
     * @param null|string $protocol
     * @return \Rf\Core\Uri\Uri|string
     */
    public function protocol($protocol = null) {
        if(!isset($protocol)) {
            return $this->protocol;
        } else {
            $this->protocol = $protocol;
            return $this;
        }
    }

    /**
     * Get/set the credentials part of the current Uri object
     *
     * @since 1.0
     *
     * @param null|array $credentials
     * @return \Rf\Core\Uri\Uri|array
     */
    public function credentials($credentials = null) {
        if(!isset($credentials)) {
            return $this->credentials;
        } else {
            $this->credentials = $credentials;
            return $this;
        }
    }

    /**
     * Get/set the host part of the current Uri object
     *
     * @since 1.0
     *
     * @param null $host
     * @return \Rf\Core\Uri\Uri|string
     */
    public function host($host = null) {
        if(!isset($host)) {
            return $this->host;
        } else {
            $this->host = $host;
            return $this;
        }
    }

    /**
     * Get/set the domain part of the current Uri object
     *
     * @return bool|string
     */
    public function domain() {

        if($this->hostType === self::HOST_TYPE_DOMAIN) {

            $parts = array_reverse(explode('.', $this->host));
            $domain = (isset($parts[1]) ? $parts[1] . '.' : '') . $parts[0];

            return $domain;

        } else {
            return false; // throw error
        }

    }

    /**
     * Get/set the subdomain part of the current Uri object
     *
     * @since 1.0
     *
     * @param int|string $level [0-9]+|last
     * @return string|false
     */
    public function subDomain($level = 1) {

        if($this->hostType === self::HOST_TYPE_DOMAIN) {

            $parts = array_reverse(explode('.', $this->host));

            if($level === 'last') {
                $subDomain = $parts[count($parts) - 1];
            } else {
                $subDomain = (isset($parts[$level + 1]) ? $parts[$level + 1] : false);
            }

            return $subDomain;

        } else {
            return false; // throw error
        }

    }

    /**
     * Get/set the port of the current Uri object
     *
     * @since 1.0
     *
     * @param null $port
     * @return \Rf\Core\Uri\Uri|int
     */
    public function port($port = null) {
        if(!isset($port)) {
            return $this->port;
        } else {
            $this->port = $port;
            return $this;
        }
    }

    /**
     * Get/set the query part of the current Uri object
     *
     * @since 1.0
     *
     * @param null $query
     * @return \Rf\Core\Uri\Uri|string
     */
    public function query($query = null) {
        if(!isset($query)) {
            return $this->query;
        } else {
            $this->query = $query;
            return $this;
        }
    }

    /**
     * Get/set the query params of the current Uri object
     *
     * @since 1.0
     *
     * @param null $queryString
     * @return \Rf\Core\Uri\Uri|array
     */
    public function queryString($queryString = null) {
        if(!isset($queryString)) {
            return $this->queryString;
        } else {
            $this->queryString = $queryString;
            return $this;
        }
    }

    /**
     * Get the full uri of the current Uri object
     *
     * @since 1.0
     *
     * @return string
     */
    public function full() {
        $uri = '';
        $uri .= empty($this->protocol) ? '' : $this->protocol() . '://';
        $uri .= empty($this->credentials) ? '' : implode(':', $this->credentials()) . '@';
        $uri .= empty($this->host) ? '' : $this->host();
        $uri .= empty($this->port) ? '' : ':' . $this->port();
        $uri .= empty($this->query) ? '' : '/' . $this->query();
        if(empty($this->queryString)) {
            $uri .= '';
        } else {
            self::addQueryStringToUri($this->queryString(), $uri);
        }
        
        return $uri;
    }

    /**
     * Get the full current uri
     *
     * @since 1.0
     *
     * @return string
     */
    public static function current() {

        if(
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || $_SERVER['SERVER_PORT'] == '443'
        ) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        $port = (in_array($_SERVER['SERVER_PORT'], array('80', '443'))) ? '' : (':'.$_SERVER['SERVER_PORT']);

        return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

    }
    
    /**
     * Add the query string to an uri
     *
     * @since 1.0
     *
     * @param array $qs
     * @param string $uri
     * @return string
     */
    public static function addQueryStringToUri(array $qs, &$uri) {
        $count = 0;
        foreach($qs as $key => $value) {
            $count++;
            $uri .= ($count === 1) ? '?' : '&';
            $uri .= $key . '=' . $value;
        }
        return $uri;
    }
    
    /**
     * Get the domain part from an uri
     *
     * @since 1.0
     *
     * @param string $uri
     * @return string
     */
    public static function getDomainFromUri($uri) {
        $uri = new Uri($uri);
        return $uri->domain();
    }

}