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

interface PexelsImageSizeInterface
{
    public const ORIGINAL = 'original';    // The size of the original image is given with the attributes width and height.
    public const LARGE = 'large';          // This image has a maximum width of 940px and a maximum height of 650px. It has the aspect ratio of the original image.
    public const LARGE_2X = 'large2x';     // This image has a maximum width of 1880px and a maximum height of 1300px. It has the aspect ratio of the original image.
    public const MEDIUM = 'medium';        // This image has a height of 350px and a flexible width. It has the aspect ratio of the original image.
    public const SMALL = 'small';          // This image has a height of 130px and a flexible width. It has the aspect ratio of the original image.
    public const PORTRAIT = 'portrait';    // This image has a width of 800px and a height of 1200px.
    public const LANDSCAPE = 'landscape';  // This image has a width of 1200px and height of 627px.
    public const TINY = 'tiny';            // This image has a width of 280px and height of 200px.
}
