<?php
namespace DL\AssetSource\Pexels\AssetSource;

/*
 * This file is part of the DL.AssetSource.Pexels package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use GuzzleHttp\Exception\GuzzleException;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryInterface;
use Neos\Media\Domain\Model\AssetSource\AssetProxyQueryResultInterface;
use Neos\Media\Domain\Model\AssetSource\AssetSourceConnectionExceptionInterface;

final class PexelsAssetProxyQuery implements AssetProxyQueryInterface
{

    /**
     * @var PexelsAssetSource
     */
    private $assetSource;

    /**
     * @var int
     */
    private $limit = 20;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var string
     */
    private $searchTerm = '';

    /**
     * UnsplashAssetProxyQuery constructor.
     * @param PexelsAssetSource $assetSource
     */
    public function __construct(PexelsAssetSource $assetSource)
    {
        $this->assetSource = $assetSource;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     */
    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return AssetProxyQueryResultInterface
     * @throws AssetSourceConnectionExceptionInterface
     * @throws \Exception
     * @throws GuzzleException
     */
    public function execute(): AssetProxyQueryResultInterface
    {
        $page = (int)ceil(($this->offset + 1) / $this->limit);

        $searchTerm = $this->searchTerm ?: $this->assetSource->getDefaultSearchTerm();

        if ($searchTerm === '') {
            $photos = $this->assetSource->getPexelsClient()->curated($this->limit, $page);
        } else {
            $photos = $this->assetSource->getPexelsClient()->search($searchTerm, $this->limit, $page);
        }

        return new PexelsAssetProxyQueryResult($this, $photos, $this->assetSource);
    }

    /**
     * @return int
     * @throws \Exception
     * @throws GuzzleException
     */
    public function count(): int
    {
        return $this->execute()->count();
    }
}
