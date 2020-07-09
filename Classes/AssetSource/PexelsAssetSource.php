<?php
declare(strict_types=1);
namespace DL\AssetSource\Pexels\AssetSource;

/*
 * This file is part of the DL.AssetSource.Pexels package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use DL\AssetSource\Pexels\Api\PexelsClient;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\AssetSource\AssetProxyRepositoryInterface;
use Neos\Media\Domain\Model\AssetSource\AssetSourceInterface;


final class PexelsAssetSource implements AssetSourceInterface
{
    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var string
     */
    private $assetSourceIdentifier;

    /**
     * @var PexelsAssetProxyRepository
     */
    private $assetProxyRepository;

    /**
     * @var PexelsClient
     */
    protected $pexelsClient;

    /**
     * @var string
     */
    private $copyRightNoticeTemplate;

    /**
     * @var string
     */
    private $defaultSearchTerm;

    /**
     * @var string
     */
    private $iconPath;

    /**
     * PexelsAssetSource constructor.
     * @param string $assetSourceIdentifier
     * @param array $assetSourceOptions
     */
    public function __construct(string $assetSourceIdentifier, array $assetSourceOptions)
    {
        $this->assetSourceIdentifier = $assetSourceIdentifier;
        $this->pexelsClient = new PexelsClient($assetSourceOptions['accessKey'], $assetSourceOptions['proxyUrl'] ?? '');
        $this->copyRightNoticeTemplate = $assetSourceOptions['copyRightNoticeTemplate'] ?? '';
        $this->defaultSearchTerm = trim($assetSourceOptions['defaultSearchTerm']) ?? '';
        $this->iconPath = trim($assetSourceOptions['icon']) ?? '';
    }

    /**
     * This factory method is used instead of a constructor in order to not dictate a __construct() signature in this
     * interface (which might conflict with an asset source's implementation or generated Flow proxy class).
     *
     * @param string $assetSourceIdentifier
     * @param array $assetSourceOptions
     * @return AssetSourceInterface
     */
    public static function createFromConfiguration(string $assetSourceIdentifier, array $assetSourceOptions): AssetSourceInterface
    {
        return new static($assetSourceIdentifier, $assetSourceOptions);
    }

    /**
     * A unique string which identifies the concrete asset source.
     * Must match /^[a-z][a-z0-9-]{0,62}[a-z]$/
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->assetSourceIdentifier;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Pexels';
    }

    /**
     * @return AssetProxyRepositoryInterface
     */
    public function getAssetProxyRepository(): AssetProxyRepositoryInterface
    {
        if ($this->assetProxyRepository === null) {
            $this->assetProxyRepository = new PexelsAssetProxyRepository($this);
        }

        return $this->assetProxyRepository;
    }

    /**
     * @return PexelsClient
     */
    public function getPexelsClient(): PexelsClient
    {
        return $this->pexelsClient;
    }

    /**
     * @return string
     */
    public function getCopyRightNoticeTemplate(): string
    {
        return $this->copyRightNoticeTemplate;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDefaultSearchTerm(): string
    {
        return $this->defaultSearchTerm;
    }

    /**
     * Returns the resource path to Assetsources icon
     *
     * @return string
     */
    public function getIconUri(): string
    {
        return $this->resourceManager->getPublicPackageResourceUriByPath($this->iconPath);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Photos provided by www.pexels.com';
    }
}
