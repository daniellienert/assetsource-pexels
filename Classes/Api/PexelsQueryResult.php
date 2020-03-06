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

class PexelsQueryResult
{

    /**
     * @var \ArrayObject
     */
    protected $photos = [];

    /**
     * @var \ArrayIterator
     */
    protected $photoIterator;

    /**
     * @var int
     */
    protected $totalResults = 30;

    /**
     * @param array $photos
     * @param int $totalResults
     */
    public function __construct(array $photos, int $totalResults)
    {
        $this->photos = new \ArrayObject($photos);
        $this->photoIterator = $this->photos->getIterator();
        $this->totalResults = $totalResults;
    }

    /**
     * @return \ArrayObject
     */
    public function getPhotos(): \ArrayObject
    {
        return $this->photos;
    }

    /**
     * @return \ArrayIterator
     */
    public function getPhotoIterator(): \ArrayIterator
    {
        return $this->photoIterator;
    }

    /**
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }
}
