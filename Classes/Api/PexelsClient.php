<?php
namespace DL\AssetSource\Pexels\Api;

/*
 * This file is part of the DL.AssetSource.Pexels package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use GuzzleHttp\Client;
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
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $photoPropertyCache;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client([
                'headers' => [
                    'Authorization' => $this->apiKey
                ]
            ]);
        }

        return $this->client;
    }

    /**
     * @param int $pageSize
     * @param int $page
     * @return PexelsQueryResult
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
    public function findByIdentifier(string $identifier) {
        if(!$this->photoPropertyCache->has($identifier)) {
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
     */
    private function executeQuery(string $type, int $pageSize = 20, int $page = 1, string $query = '')
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
     */
    protected function processResult(array $resultArray) {
        $photos = $resultArray['photos'] ?? [];
        $totalResults = $resultArray['total_results'] ?? 30;

        foreach ($photos as $photo) {
            if(isset($photo['id'])) {
                $this->photoPropertyCache->set($photo['id'], $photo);
            }
        }

        return new PexelsQueryResult($photos, $totalResults);
    }
}
