[![Latest Stable Version](https://poser.pugx.org/dl/assetsource-pexels/v/stable)](https://packagist.org/packages/dl/assetsource-pexels) [![Total Downloads](https://poser.pugx.org/dl/assetsource-pexels/downloads)](https://packagist.org/packages/dl/assetsource-pexels) [![License](https://poser.pugx.org/dl/assetsource-pexels/license)](https://packagist.org/packages/dl/assetsource-pexels)

![Images provided by Pexels](https://user-images.githubusercontent.com/642226/39978717-6c848b32-5742-11e8-82bb-d5e325e29c6d.png)

# Pexels Asset Source
This package provides a Neos Asset Source to access the [Pexels](https://www.pexels.com) image database.

## How to use it
1. Install the package via composer `composer require dl/assetsource-pexels`
2. Request an API key from Pexels https://www.pexels.com/api/new/
3. Configure the API key in the settings

![Neos Media Browser with Pexels Data Source selected](https://user-images.githubusercontent.com/642226/39648639-261402f6-4fe3-11e8-9788-7c5425eac087.png)

## AssetsourceOptions

**copyRightNoticeTemplate**

Eel expression to compile the copyright notice using available data:

* *user.name* The authors name
