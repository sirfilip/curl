<?php

/**
 * Simple curl php wrapper.
 * 
 * Can send requests post|put|delete|get|head 
 * and return response.
 *
 * Ex:
 *
 * $curl = new Curl
 * $response = $curl->get('http://google.com');
 */
class Curl 
{
    private $_session;
    private $_last_response;
    private $_info;
    
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Resets the session and sets the defaults.
     */
    public function reset()
    {
        $this->_session = curl_init();
        $this->_last_response = '';
        $this->_info = array();
        curl_setopt($this->_session, CURLOPT_AUTOREFERER, 1);
        curl_setopt($this->_session, CURLOPT_COOKIESESSION, 1);
        curl_setopt($this->_session, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->_session, CURLOPT_MAXREDIRS, 5);
        curl_setopt($this->_session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->_session, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($this->_session, CURLOPT_TIMEOUT, 100);
        return $this;
    }

    /**
     * Returns all transfer for debug purpose.
     */
    public function debug()
    {
        curl_setopt($this->_session, CURLOPT_HEADER, 1);
        curl_setopt($this->_session, CURLINFO_HEADER_OUT, 1);
        curl_setopt($this->_session, CURLOPT_VERBOSE, 1);
        return $this;
    }
    
    /**
     * Performs HTTP GET request.
     *
     * @param String url location 
     * @return String response
     */
    public function get($url)
    {
        curl_setopt($this->_session, CURLOPT_HTTPGET, 1);
        $this->url($url);
        return $this->exec();
    }

    /**
     * Performs HTTP POST request.
     *
     * @param String url location
     * @param Array post params
     * @return String
     */
    public function post($url, $params = array())
    {
        curl_setopt($this->_session, CURLOPT_POST, 1);
        $this->url($url);
        curl_setopt($this->_session, CURLOPT_POSTFIELDS, $params);
        return $this->exec();
    }

    /**
     * Performs HTTP PUT request.
     *
     * @param String url location
     * @param array put params
     * @return String response
     */
    public function put($url, $params = array())
    {
        curl_setopt($this->_session, CURLOPT_PUT, 1);
        $this->url($url);
        curl_setopt($this->_session, CURLOPT_POSTFIELDS, $params);
        return $this->exec();
    }

    /**
     * Performs HTTP DELETE request.
     *
     * @param String url location.
     * @return String response.
     */
    public function delete($url)
    {
        curl_setopt($this->_session, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->url($url);
        return $this->exec();
    }

    /**
     * Performs HTTP HEAD request.
     *
     * @param String url location
     * @return String response
     */
    public function head($url)
    {
        curl_setopt($this->_session, CURLOPT_NOBODY, 1);
        $this->url($url);
        return $this->exec();
    }

    /**
     * Performs File upload
     *
     * @param String url location
     * @param String full path of the file to be uploaded
     * @return String response
     */
    public function upload($url, $filename)
    {
        curl_setopt($this->_session, CURLOPT_UPLOAD, 1);
        $this->url($url);
        curl_setopt($this->_session, CURLOPT_INFILE, $filename);
        return $this->exec();
    }

    /**
     * Performs basic http authentication.
     *
     * @param String username
     * @param String password
     */
    public function authenticate($username, $password)
    {
        curl_setopt($this->_session, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->_session, CURLOPT_USERPWD, "{$username}:{$password}");
        return $this;
    }

    /**
     * Sets custom headers.
     *
     * @param Array headers
     */
    public function headers($headers = array())
    {
        curl_setopt($this->_session, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    /**
     * Sets custom port.
     *
     * @param Int port number.
     */
    public function port($port_number = 80)
    {
        curl_setopt($this->_session, CURLOPT_PORT, $port_number);
        return $this;
    }

    /**
     * Sets custom referer.
     *
     * @param String referer
     */
    public function referer($referer)
    {
        curl_setopt($this->_session, CURLOPT_REFERER, $referer);
        return $this;
    }

    /**
     * Sets custom useragent.
     *
     * @param String agent
     */
    public function agent($agent)
    {
        curl_setopt($this->_session, CURLOPT_USERAGENT, $agent);
        return $this;
    }

    /**
     * Sets url location for the request.
     *
     * @param String url
     */
    protected function url($url)
    {
        curl_setopt($this->_session, CURLOPT_URL, $url);
    }

    /**
     * Closes the current curl session.
     *
     * Call to reset should be used to start new session.
     */
    public function close()
    {
        curl_close($this->_session);
        return $this;
    }

    /**
     * Fetches info from the last response.
     *
     * @param String key if given returns only the requested param
     * @return Array or String based on the key param
     */
    public function info($key = null)
    {
        if (is_null($key))
        {
            return $this->_info;
        }
        else
        {
            return $this->_info[$key];
        }
    }

    /**
     * Fetches the last response status code.
     *
     * @return Int
     */
    public function code()
    {
        return $this->info('http_code');
    }

    /**
     * Executes the request and populates last_response
     * info and error values.
     *
     * @return String last response
     */
    protected function exec()
    {
        $this->_last_response = curl_exec($this->_session);
        $this->_info = curl_getinfo($this->_session);
        $this->_error = curl_error($this->_session);
        return $this->_last_response;
    }

    /**
     * Returns last error message if any.
     *
     * @return String blank if no error occured
     */
    public function error()
    {
        return $this->_error;
    }

    /**
     * Housekeeping.
     */
    public function __destruct()
    {
        if ($this->_session) curl_close($this->_session);
        $this->last_response = '';
    }
}

// eof php
