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

use DL\AssetSource\Pexels\Exception\ConfigurationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;

final class PexelsClient
{
    const API_URL = 'https://api.pexels.com/v1/';

    const QUERY_TYPE_CURATED = 'curated';
    const QUERY_TYPE_SEARCH = 'search';

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

    /**
     * @param int $pageSize
     * @param int $page
     * @return PexelsQueryResult
     * @throws GuzzleException
     * @throws \Neos\Cache\Exception
     */
    public function curated(int $pageSize = 20, int $page = 1)
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
    public function search(string $query, int $pageSize = 20, int $page = 1)
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
}
