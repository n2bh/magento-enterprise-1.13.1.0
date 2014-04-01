<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Solr client
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Search_Model_Client_Solr extends Apache_Solr_Service
{
    /**
     * Store user login, that needed in authentication with solr server
     *
     * @var string
     */
    protected $_login = '';

    /**
     * Store user password, that needed in authentication with solr server
     *
     * @var string
     */
    protected $_password = '';

    /**
     * Suggestions servlet
     */
    const SUGGESTIONS_SERVLET = 'spell';

    /**
     * Constructed servlet full path URLs
     *
     * @var string
     */
    protected $_suggestionsUrl;

    /**
     * Initialize Solr Client
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $_optionsNames = array(
            'hostname',
            'login',
            'password',
            'port',
            'path'
        );
        if (!sizeof(array_intersect($_optionsNames, array_keys($options)))) {
            Mage::throwException(
                Mage::helper('enterprise_search')->__('Unable to perform search because of search engine missed configuration.')
            );
        }

        $this->setUserLogin($options['login']);
        $this->setPassword($options['password']);

        if (isset($options['timeout'])) {
            ini_set('default_socket_timeout', $options['timeout']);
        }

        parent::__construct($options['hostname'], $options['port'], '/' . $options['path'] . '/');
        return $this;
    }

    protected function _initUrls()
    {
        parent::_initUrls();
        $this->_suggestionsUrl = $this->_constructUrl(self::SUGGESTIONS_SERVLET);

    }

    /**
     * Send an rollback command.
     *
     * @param float $timeout Maximum expected duration of the commit operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function rollback($timeout = 3600)
    {
        $rawPost = '<rollback/>';
        return $this->_sendRawPost($this->_updateUrl, $rawPost, $timeout);
    }

    /**
     * Create a delete document based on a multiple queries and submit it
     *
     * @param array $rawQueries Expected to be utf-8 encoded
     * @param boolean $fromPending
     * @param boolean $fromCommitted
     * @param float $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByQueries($rawQueries, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $pendingValue = $fromPending ? 'true' : 'false';
        $committedValue = $fromCommitted ? 'true' : 'false';

        $rawPost = '<delete fromPending="' . $pendingValue . '" fromCommitted="' . $committedValue . '">';

        foreach ($rawQueries as $query)
        {
            //escape special xml characters
            $query = htmlspecialchars($query, ENT_NOQUOTES, 'UTF-8');

            $rawPost .= '<query>' . $query . '</query>';
        }

        $rawPost .= '</delete>';

        return $this->delete($rawPost, $timeout);
    }

    /**
     * Alias to Apache_Solr_Service::deleteByMultipleIds() method
     *
     * @param array $ids Expected to be utf-8 encoded strings
     * @param boolean $fromPending
     * @param boolean $fromCommitted
     * @param float $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByIds($ids, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $this->deleteByMultipleIds($ids, $fromPending, $fromCommitted, $timeout);
    }

    /**
     * Central method for making a get operation against this Solr Server
     *
     * @param string $url
     * @param float $timeout Read timeout in seconds
     * @return Apache_Solr_Response
     *
     * @throws Exception If a non 200 response status is returned
     */
    protected function _sendRawGet($url, $timeout = FALSE)
    {
        stream_context_set_option(
            $this->_getContext, 'http', 'header',
            "Authorization: Basic " . base64_encode($this->getUserLogin() . ':' . $this->getPassword())
        );
        return parent::_sendRawGet($url, $timeout);
    }

    /**
     * Central method for making a post operation against this Solr Server
     *
     * @param string $url
     * @param string $rawPost
     * @param float $timeout Read timeout in seconds
     * @param string $contentType
     * @return Apache_Solr_Response
     *
     * @throws Exception If a non 200 response status is returned
     */
    protected function _sendRawPost($url, $rawPost, $timeout = FALSE, $contentType = 'text/xml; charset=UTF-8')
    {
        stream_context_set_option(
            $this->_postContext, 'http', 'header',
            "Authorization: Basic " . base64_encode($this->getUserLogin() . ':' . $this->getPassword())
        );
        return parent::_sendRawPost($url, $rawPost, $timeout, $contentType);
    }

    /**
     * Setter for solr server username
     *
     * @param string $username
     */
    public function setUserLogin($username)
    {
        $this->_login = (string)$username;
    }

    /**
     * Getter of solr server username
     *
     * @return string
     */
    public function getUserLogin()
    {
        return $this->_login;
    }

    /**
     * Setter for solr server password
     *
     * @param string $username
     */
    public function setPassword($username)
    {
        $this->_password = (string)$username;
    }

    /**
     * Getter of solr server password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Simple Search interface
     *
     * @deprecated after 1.9.0.0 - integrated into $this->combinedSearch()
     *
     * @param string $query The raw query string
     * @param array $params key / value pairs for other query parameters (see Solr documentation), use arrays for parameter keys used more than once (e.g. facet.field)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function searchSuggestions($query, $params = array(), $method = self::METHOD_GET)
    {
        if (!is_array($params))
        {
            $params = array();
        }
        // construct our full parameters
        // sending the version is important in case the format changes
        $params['version'] = self::SOLR_VERSION;

        // common parameters in this interface
        $params['wt'] = self::SOLR_WRITER;
        $params['json.nl'] = $this->_namedListTreatment;

        $params['q'] = $query;

        // use http_build_query to encode our arguments because its faster
        // than urlencoding all the parts ourselves in a loop
        $queryString = http_build_query($params, null, $this->_queryStringDelimiter);

        $queryString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);

        if ($method == self::METHOD_GET)
        {
            return $this->_sendRawGet($this->_suggestionsUrl . $this->_queryDelimiter . $queryString);
        }
        else if ($method == self::METHOD_POST)
        {
            return $this->_sendRawPost(
                $this->_suggestionsUrl, $queryString, FALSE, 'application/x-www-form-urlencoded'
            );
        }
        else
        {
            throw new Exception("Unsupported method '$method', please use the Apache_Solr_Service::METHOD_* constants");
        }
    }
}
