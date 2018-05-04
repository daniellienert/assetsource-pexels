<?php
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
    const ORIGINAL = 'original';    // The size of the original image is given with the attributes width and height.
    const LARGE = 'large';          // This image has a maximum width of 940px and a maximum height of 650px. It has the aspect ratio of the original image.
    const LARGE_2X = 'large2x';     // This image has a maximum width of 1880px and a maximum height of 1300px. It has the aspect ratio of the original image.
    const MEDIUM = 'medium';        // This image has a height of 350px and a flexible width. It has the aspect ratio of the original image.
    const SMALL = 'small';          // This image has a height of 130px and a flexible width. It has the aspect ratio of the original image.
    const PORTRAIT = 'portrait';    // This image has a width of 800px and a height of 1200px.
    const LANDSCAPE = 'landscape';  // This image has a width of 1200px and height of 627px.
    const TINY = 'tiny';            // This image has a width of 280px and height of 200px.
}
