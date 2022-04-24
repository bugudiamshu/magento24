<?php

namespace EngageBay\Marketing\Helper;

use Magento\Framework\HTTP\ZendClient;
use Psr\Log\LoggerInterface;
use Zend_Http_Client_Exception;
use Zend_Http_Response;

class EngageBayRestAPIHelper
{
    const ENGAGEBAY_BASE_URL               = 'https://app.engagebay.com/dev/api/panel';
    const ENGAGEBAY_LOGIN_URL              = 'https://app.engagebay.com/rest/api/login/get-domain';
    const ENGAGEBAY_SEARCH_CONTACT_API_URL = self::ENGAGEBAY_BASE_URL . '/subscribers/getByEmail/%s';
    const ENGAGEBAY_ADD_CONTACT_API_URL    = self::ENGAGEBAY_BASE_URL . '/subscribers/subscriber';
    const ENGAGEBAY_UPDATE_CONTACT_API_URL = self::ENGAGEBAY_BASE_URL . '/subscribers/update-partial';
    const ENGAGEBAY_ADD_NOTES_API_URL      = self::ENGAGEBAY_BASE_URL . '/notes';
    const ENGAGEBAY_ADD_TAGS_API_URL       = self::ENGAGEBAY_BASE_URL . '/subscribers/email/tags/add';
    const ENGAGEBAY_BULK_SYNC              = self::ENGAGEBAY_BASE_URL . '/magento/bulk-dump/%s';
    const ENGAGEBAY_STORE_ORDERS           = self::ENGAGEBAY_BASE_URL . '/magento/hook/ORDERS';
    const ENGAGEBAY_DEALS_TRACKS           = self::ENGAGEBAY_BASE_URL . '/tracks';

    public const LOGIN_REQUEST_IDENTIFIER             = 'login';
    public const SEARCH_REQUEST_IDENTIFIER            = 'search';
    public const ADD_CONTACT_REQUEST_IDENTIFIER       = 'add_contact';
    public const UPDATE_CONTACT_REQUEST_IDENTIFIER    = 'update_contact';
    public const ADD_NOTES_REQUEST_IDENTIFIER         = 'add_notes';
    public const ADD_TAGS_REQUEST_IDENTIFIER          = 'add_tags';
    public const BULK_SYNC_REQUEST_IDENTIFIER         = 'bulk_sync';
    public const STORE_ORDERS_HOOK_REQUEST_IDENTIFIER = 'store_orders_hook';
    public const DEALS_TRACKS_REQUEST_IDENTIFIER      = 'get_tracks';

    /**
     * @var ZendClient
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var Data
     */
    private $helper;

    /**
     * EngageBayRestAPIHelper constructor.
     *
     * @param ZendClient      $httpClient
     * @param Data            $helper
     * @param LoggerInterface $logger
     */
    public function __construct(ZendClient $httpClient, Data $helper, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger     = $logger;
        $this->helper     = $helper;
        $this->headers    = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => $helper->getRestApiKey(),
        ];
    }

    /**
     * EngageBay Login
     *
     * @param string $username
     * @param string $password
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function login(string $username, string $password)
    {
        $this->httpClient->resetParameters(true);
        $this->httpClient->setHeaders(['content-type' => 'application/x-www-form-urlencoded']);
        $this->httpClient->setUri(self::ENGAGEBAY_LOGIN_URL);
        $this->httpClient->setParameterPost(['email' => $username, 'password' => $password, 'source' => 'MAGENTO']);
        $this->httpClient->setUrlEncodeBody(true);

        $response = $this->httpClient->request('POST');

        return $this->decodeResponse($response, self::LOGIN_REQUEST_IDENTIFIER);
    }

    /**
     * Search Contact by email
     *
     * @param string $email
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function searchContact(string $email)
    {
        $response = $this->makeRequest(sprintf(self::ENGAGEBAY_SEARCH_CONTACT_API_URL, $email), 'GET');

        return $this->decodeResponse($response, self::SEARCH_REQUEST_IDENTIFIER);
    }

    /**
     * Add Contact in EngageBay
     *
     * @param array $contact
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function addContact(array $contact)
    {
        $response = $this->makeRequest(self::ENGAGEBAY_ADD_CONTACT_API_URL, 'POST', json_encode($contact));

        return $this->decodeResponse($response, self::ADD_CONTACT_REQUEST_IDENTIFIER);
    }

    /**
     * Update Contact
     *
     * @param array  $contact
     * @param string $engagebay_contact_id
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function updateContact(array $contact, string $engagebay_contact_id)
    {
        $contact['id'] = $engagebay_contact_id;

        $response = $this->makeRequest(self::ENGAGEBAY_UPDATE_CONTACT_API_URL, 'PUT', json_encode($contact));

        return $this->decodeResponse($response, self::UPDATE_CONTACT_REQUEST_IDENTIFIER);
    }

    /**
     * Create Notes for a contact
     *
     * @param array  $order
     * @param string $contact_id
     *
     * @return false|mixed
     */
    public function createNotes(array $order, string $contact_id)
    {
        $notes_body = [
            'subject'  => $order['subject'],
            'content'  => $order['content'],
            'parentId' => $contact_id,
        ];

        $response = $this->makeRequest(self::ENGAGEBAY_ADD_NOTES_API_URL, 'POST', json_encode($notes_body));

        return $this->decodeResponse($response, self::ADD_NOTES_REQUEST_IDENTIFIER);
    }

    /**
     * Create Tags for a contact
     *
     * @param string $email
     * @param string $items
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function createTags(string $email, string $items)
    {
        $products = preg_replace('/[^a-zA-Z0-9_.,]/', '_', $this->fnJsEscape($items));
        $tags     = '[' . $products . ']';

        $this->httpClient->resetParameters(true);
        $this->httpClient->setHeaders(
            [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => $this->helper->getRestApiKey(),
            'Accept'        => 'application/json',
            ]
        );
        $this->httpClient->setUri(self::ENGAGEBAY_ADD_TAGS_API_URL);
        $this->httpClient->setParameterPost(['email' => $email, 'tags' => $tags]);
        $this->httpClient->setUrlEncodeBody(true);

        $response = $this->httpClient->request('POST');

        return $this->decodeResponse($response, self::ADD_TAGS_REQUEST_IDENTIFIER);
    }

    /**
     * Bulk Sync Contacts / Orders
     *
     * @param array  $batch
     * @param string $type
     *
     * @return false|mixed
     * @throws Zend_Http_Client_Exception
     */
    public function bulkSync(array $batch, string $type)
    {
        $response = $this->makeRequest(sprintf(self::ENGAGEBAY_BULK_SYNC, $type), 'POST', json_encode($batch));

        return $this->decodeResponse($response, self::BULK_SYNC_REQUEST_IDENTIFIER);
    }

    /**
     * @return mixed|null
     * @throws Zend_Http_Client_Exception
     */
    public function syncDeals(array $payload)
    {
        $response = $this->makeRequest(self::ENGAGEBAY_STORE_ORDERS, 'POST', json_encode($payload));

        return $this->decodeResponse($response, self::STORE_ORDERS_HOOK_REQUEST_IDENTIFIER);
    }

    public function getTracks()
    {
        $response = $this->makeRequest(self::ENGAGEBAY_DEALS_TRACKS, 'GET');

        return $this->decodeResponse($response, self::DEALS_TRACKS_REQUEST_IDENTIFIER);
    }

    /**
     * Escape string
     *
     * @param string $str
     *
     * @return string
     */
    public function fnJsEscape(string $str)
    {
        return strtr(
            $str,
            [
                '\\' => '\\\\',
                "'"  => "\\'",
                '"'  => '\\"',
                "\r" => '\\r',
                "\n" => '\\n',
                "\t" => '\\t',
                '</' => '<\/',
                '/'  => '\\/',
            ]
        );
    }

    /**
     * Make HTTP request
     *
     * @param string            $uri
     * @param string            $method
     * @param array|string|null $data
     *
     * @return Zend_Http_Response
     * @throws Zend_Http_Client_Exception
     */
    private function makeRequest(string $uri, string $method, $data = null): Zend_Http_Response
    {
        $this->httpClient->setHeaders($this->headers);
        $this->httpClient->setUri($uri);
        $this->httpClient->setRawData($data);

        return $this->httpClient->request($method);
    }

    /**
     * Decode HTTP Response
     *
     * @param Zend_Http_Response $response
     * @param string             $requestIdentifier
     *
     * @return false|mixed
     */
    public function decodeResponse(Zend_Http_Response $response, string $requestIdentifier)
    {
        if ($response->getStatus() === 200 || $response->getStatus() === 204) {
            return json_decode($response->getBody(), true);
        } else {
            $this->logger->info(sprintf("Request failed for [%s]", $requestIdentifier));
            $this->logger->info($response->getRawBody());
        }

        return false;
    }
}
