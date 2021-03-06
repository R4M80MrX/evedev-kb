<?php
/**
 * DogmaApi
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * EVE Swagger Interface
 *
 * An OpenAPI for EVE Online
 *
 * OpenAPI spec version: 0.8.9
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace EsiClient;

use \Swagger\Client\Configuration;
use \Swagger\Client\ApiClient;
use \Swagger\Client\ApiException;
use \Swagger\Client\ObjectSerializer;

/**
 * DogmaApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class DogmaApi
{

    /**
     * API Client
     *
     * @var \Swagger\Client\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \Swagger\Client\ApiClient|null $apiClient The api client to use
     */
    public function __construct(\Swagger\Client\ApiClient $apiClient = null)
    {
        if ($apiClient == null) {
            $apiClient = new ApiClient();
            $apiClient->getConfig()->setHost('https://esi.evetech.net');
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \Swagger\Client\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \Swagger\Client\ApiClient $apiClient set the API client
     *
     * @return DogmaApi
     */
    public function setApiClient(\Swagger\Client\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation getDogmaAttributes
     *
     * Get attributes
     *
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return int[]
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaAttributes($datasource = null, $if_none_match = null)
    {
        list($response) = $this->getDogmaAttributesWithHttpInfo($datasource, $if_none_match);
        return $response;
    }

    /**
     * Operation getDogmaAttributesWithHttpInfo
     *
     * Get attributes
     *
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return Array of int[], HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaAttributesWithHttpInfo($datasource = null, $if_none_match = null)
    {
        // parse inputs
        $resourcePath = "/v1/dogma/attributes/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json'));

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // header params
        if ($if_none_match !== null) {
            $headerParams['If-None-Match'] = $this->apiClient->getSerializer()->toHeaderValue($if_none_match);
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                'int[]',
                '/v1/dogma/attributes/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, 'int[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), 'int[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\BadRequest', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 420:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorLimited', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 503:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ServiceUnavailable', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 504:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GatewayTimeout', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getDogmaAttributesAttributeId
     *
     * Get attribute information
     *
     * @param int $attribute_id A dogma attribute ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return \Swagger\Client\Model\GetDogmaAttributesAttributeIdOk
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaAttributesAttributeId($attribute_id, $datasource = null, $if_none_match = null)
    {
        list($response) = $this->getDogmaAttributesAttributeIdWithHttpInfo($attribute_id, $datasource, $if_none_match);
        return $response;
    }

    /**
     * Operation getDogmaAttributesAttributeIdWithHttpInfo
     *
     * Get attribute information
     *
     * @param int $attribute_id A dogma attribute ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return Array of \Swagger\Client\Model\GetDogmaAttributesAttributeIdOk, HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaAttributesAttributeIdWithHttpInfo($attribute_id, $datasource = null, $if_none_match = null)
    {
        // verify the required parameter 'attribute_id' is set
        if ($attribute_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $attribute_id when calling getDogmaAttributesAttributeId');
        }
        // parse inputs
        $resourcePath = "/v1/dogma/attributes/{attribute_id}/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json'));

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // header params
        if ($if_none_match !== null) {
            $headerParams['If-None-Match'] = $this->apiClient->getSerializer()->toHeaderValue($if_none_match);
        }
        // path params
        if ($attribute_id !== null) {
            $resourcePath = str_replace(
                "{" . "attribute_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($attribute_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\GetDogmaAttributesAttributeIdOk',
                '/v1/dogma/attributes/{attribute_id}/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\GetDogmaAttributesAttributeIdOk', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaAttributesAttributeIdOk', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\BadRequest', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaAttributesAttributeIdNotFound', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 420:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorLimited', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 503:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ServiceUnavailable', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 504:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GatewayTimeout', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getDogmaDynamicItemsTypeIdItemId
     *
     * Get dynamic item information
     *
     * @param int $item_id item_id integer (required)
     * @param int $type_id type_id integer (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return \Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdOk
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaDynamicItemsTypeIdItemId($item_id, $type_id, $datasource = null, $if_none_match = null)
    {
        list($response) = $this->getDogmaDynamicItemsTypeIdItemIdWithHttpInfo($item_id, $type_id, $datasource, $if_none_match);
        return $response;
    }

    /**
     * Operation getDogmaDynamicItemsTypeIdItemIdWithHttpInfo
     *
     * Get dynamic item information
     *
     * @param int $item_id item_id integer (required)
     * @param int $type_id type_id integer (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return Array of \Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdOk, HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaDynamicItemsTypeIdItemIdWithHttpInfo($item_id, $type_id, $datasource = null, $if_none_match = null)
    {
        // verify the required parameter 'item_id' is set
        if ($item_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $item_id when calling getDogmaDynamicItemsTypeIdItemId');
        }
        // verify the required parameter 'type_id' is set
        if ($type_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $type_id when calling getDogmaDynamicItemsTypeIdItemId');
        }
        // parse inputs
        $resourcePath = "/v1/dogma/dynamic/items/{type_id}/{item_id}/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json'));

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // header params
        if ($if_none_match !== null) {
            $headerParams['If-None-Match'] = $this->apiClient->getSerializer()->toHeaderValue($if_none_match);
        }
        // path params
        if ($item_id !== null) {
            $resourcePath = str_replace(
                "{" . "item_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($item_id),
                $resourcePath
            );
        }
        // path params
        if ($type_id !== null) {
            $resourcePath = str_replace(
                "{" . "type_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($type_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdOk',
                '/v1/dogma/dynamic/items/{type_id}/{item_id}/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdOk', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdOk', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\BadRequest', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaDynamicItemsTypeIdItemIdNotFound', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 420:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorLimited', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 503:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ServiceUnavailable', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 504:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GatewayTimeout', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getDogmaEffects
     *
     * Get effects
     *
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return int[]
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaEffects($datasource = null, $if_none_match = null)
    {
        list($response) = $this->getDogmaEffectsWithHttpInfo($datasource, $if_none_match);
        return $response;
    }

    /**
     * Operation getDogmaEffectsWithHttpInfo
     *
     * Get effects
     *
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return Array of int[], HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaEffectsWithHttpInfo($datasource = null, $if_none_match = null)
    {
        // parse inputs
        $resourcePath = "/v1/dogma/effects/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json'));

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // header params
        if ($if_none_match !== null) {
            $headerParams['If-None-Match'] = $this->apiClient->getSerializer()->toHeaderValue($if_none_match);
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                'int[]',
                '/v1/dogma/effects/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, 'int[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), 'int[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\BadRequest', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 420:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorLimited', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 503:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ServiceUnavailable', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 504:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GatewayTimeout', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getDogmaEffectsEffectId
     *
     * Get effect information
     *
     * @param int $effect_id A dogma effect ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return \Swagger\Client\Model\GetDogmaEffectsEffectIdOk
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaEffectsEffectId($effect_id, $datasource = null, $if_none_match = null)
    {
        list($response) = $this->getDogmaEffectsEffectIdWithHttpInfo($effect_id, $datasource, $if_none_match);
        return $response;
    }

    /**
     * Operation getDogmaEffectsEffectIdWithHttpInfo
     *
     * Get effect information
     *
     * @param int $effect_id A dogma effect ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @param string $if_none_match ETag from a previous request. A 304 will be returned if this matches the current ETag (optional)
     * @return Array of \Swagger\Client\Model\GetDogmaEffectsEffectIdOk, HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\ApiException on non-2xx response
     */
    public function getDogmaEffectsEffectIdWithHttpInfo($effect_id, $datasource = null, $if_none_match = null)
    {
        // verify the required parameter 'effect_id' is set
        if ($effect_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $effect_id when calling getDogmaEffectsEffectId');
        }
        // parse inputs
        $resourcePath = "/v2/dogma/effects/{effect_id}/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json'));

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // header params
        if ($if_none_match !== null) {
            $headerParams['If-None-Match'] = $this->apiClient->getSerializer()->toHeaderValue($if_none_match);
        }
        // path params
        if ($effect_id !== null) {
            $resourcePath = str_replace(
                "{" . "effect_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($effect_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\GetDogmaEffectsEffectIdOk',
                '/v2/dogma/effects/{effect_id}/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\GetDogmaEffectsEffectIdOk', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaEffectsEffectIdOk', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\BadRequest', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GetDogmaEffectsEffectIdNotFound', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 420:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorLimited', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 503:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ServiceUnavailable', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 504:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\GatewayTimeout', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

}
