<?php
declare(strict_types=1);

namespace DL\AssetSource\Pexels\Api;

/*
 * This file is part of the DL.AssetSource.Pexels package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use DL\AssetSource\Pexels\Exception\TransferException;
use Neos\Flow\Annotations as Flow;
use DL\AssetSource\Pexels\Exception\ConfigurationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Neos\Cache\Frontend\VariableFrontend;

final class PexelsClient
{
    protected const API_URL = 'https://api.pexels.com/v1/';

    protected const QUERY_TYPE_CURATED = 'curated';
    protected const QUERY_TYPE_SEARCH = 'search';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $queryResults = [];

    /**
     * @var VariableFrontend
     */
    protected $photoPropertyCache;

    /**
     * @var string
     */
    protected $proxy;

    /**
     * @param string $apiKey
     * @param string $proxy
     */
    public function __construct(string $apiKey, string $proxy)
    {
        $this->proxy = $proxy;
        $this->apiKey = $apiKey;
    }

    /**
     * @param int $pageSize
     * @param int $page
     * @return PexelsQueryResult
     * @throws GuzzleException
     * @throws \Neos\Cache\Exception
     */
    public function curated(int $pageSize = 20, int $page = 1): PexelsQueryResult
    {
        return $this->executeQuery(self::QUERY_TYPE_CURATED, $pageSize, $page);
    }

    /**
     * @param string $query
     * @param int $pageSize
     * @param int $page
     *
     * @return PexelsQueryResult
     * @throws GuzzleException
     * @throws \Neos\Cache\Exception
     */
    public function search(string $query, int $pageSize = 20, int $page = 1): PexelsQueryResult
    {
        return $this->executeQuery(self::QUERY_TYPE_SEARCH, $pageSize, $page, $query);
    }

    /**
     * @param string $identifier
     * @return mixed
     * @throws \Exception
     */
    public function findByIdentifier(string $identifier)
    {
        if (!$this->photoPropertyCache->has($identifier)) {
            throw new \Exception(sprintf('Photo with id %s was not found in the cache', $identifier), 1525457755);
        }

        return $this->photoPropertyCache->get($identifier);
    }

    /**
     * @param string $url
     * @return false|resource
     */
    public function getFileStream(string $url)
    {
        $tcpPrefixedProxy = str_replace('http', 'tcp', $this->proxy);

        $context = stream_context_create([
            'http' => [
                'proxy' => $tcpPrefixedProxy
            ],
        ]);

        $resource = fopen($url, 'r', false, $context);

        if (!is_resource($resource)) {
            throw new TransferException(sprintf('Unable to load an image from %s %s. Error: %s', $url, $tcpPrefixedProxy !== '' ? 'using proxy ' . $tcpPrefixedProxy : ' without using a proxy.', error_get_last()), 1600770625);
        }
        
        return $resource;
    }

    /**
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $query
     * @return PexelsQueryResult
     * @throws GuzzleException
     * @throws \Neos\Cache\Exception
     */
    private function executeQuery(string $type, int $pageSize = 20, int $page = 1, string $query = ''): PexelsQueryResult
    {
        $requestParameter = [
            'per_page' => $pageSize,
            'page' => $page
        ];

        if ($query !== '') {
            $requestParameter['query'] = $query;
        }

        $requestIdentifier = implode('_', $requestParameter);

        if (!isset($this->queryResults[$requestIdentifier])) {
            $result = $this->getClient()->request('GET', self::API_URL . $type . '?' . http_build_query($requestParameter));

            $resultArray = \GuzzleHttp\json_decode($result->getBody(), true);
            $this->queryResults[$requestIdentifier] = $this->processResult($resultArray);
        }

        return $this->queryResults[$requestIdentifier];
    }

    /**
     * @param array $resultArray
     * @return PexelsQueryResult
     * @throws \Neos\Cache\Exception
     */
    protected function processResult(array $resultArray): PexelsQueryResult
    {
        $photos = $resultArray['photos'] ?? [];
        $totalResults = $resultArray['total_results'] ?? count($photos);

        foreach ($photos as $photo) {
            if (isset($photo['id'])) {
                $this->photoPropertyCache->set((string)$photo['id'], $photo);
            }
        }

        return new PexelsQueryResult($photos, $totalResults);
    }

    /**
     * @return Client
     * @throws ConfigurationException
     */
    private function getClient(): Client
    {
        if (trim($this->apiKey) === '') {
            throw new ConfigurationException('No API key for pexels was defined. Get your API key at https://www.pexels.com/api/ and add it to your settings', 1594199031);
        }

        if ($this->client === null) {
            $this->client = new Client([
                'proxy' => $this->proxy,
                'timeout' => 3.0,
                'headers' => [
                    'Authorization' => $this->apiKey,
                ],
            ]);
        }

        return $this->client;
    }
}
