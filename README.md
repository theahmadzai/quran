<p align="center"><img src="http://i.imgur.com/fWuCik5.png" alt="Quran.com API Helper"></p>

Quran.com - API Helpera
======================

[![Join the chat at https://gitter.im/Quran-API-Helper-PHP/Lobby](https://badges.gitter.im/Quran-API-Helper-PHP/Lobby.svg)](https://gitter.im/Quran-API-Helper-PHP/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/theahmadzai/quran.svg?branch=master)](https://travis-ci.org/theahmadzai/quran)
[![Latest Stable Version](https://poser.pugx.org/quran/quran/v/stable)](https://packagist.org/packages/quran/quran)
[![Total Downloads](https://poser.pugx.org/quran/quran/downloads)](https://packagist.org/packages/quran/quran)
[![Latest Unstable Version](https://poser.pugx.org/quran/quran/v/unstable)](https://packagist.org/packages/quran/quran)
[![License](https://poser.pugx.org/quran/quran/license)](https://packagist.org/packages/quran/quran)

Simple API Helper package for Quran API, https://quran.api-docs.io/v3/

Quran API Helper is a library package for PHP developers, it fetches the requested data from Quran.com API and brings you it in an array, this package also includes a caching system it caches all the requested data and boosts the speed, decreases the deley time.

### Requirements
- PHP 5.6+
- cUrl enabled

### Installation
It's recommended that you use Composer to install it.
```sh
composer require quran/quran
```
This will install Quran.come API Helper module and all the dependencies needed.

### About the main Ruby API
#### API Repository
Main API is written in Ruby. https://github.com/quran/quran.com-api

#### API Documentation
Find the documents about main API here. https://quran.api-docs.io/v3

### Usage
Here's a basic usage example:

```php
<?php

require 'vendor/autoload.php';

$quran = new \Quran\Quran([
    'cache' => __DIR__ . '/cache',
]);

$quran->chapter(1)->verse();
```

#### Options
Options means getting list of the recitations, translations, languages or tafsirs.

```php
// List of recitations
$quran->recitations();

// List of translations
$quran->translations();

// List of languages
$quran->languages();

// List of tafsirs
$quran->tafsirs();
```

#### Searching
You can search for a keyword in Quran.

```php
$quran->search([
    'query' => 'adam', // Keyword
    'size'  => 20,     // Number of items - OPTIONAL - default: 20
    'page'  => 0,      // Number of items per page for pagination - OPTIONAL - default: 0
]);
```

#### Custom Query
You send custom queries to the API, by providing path and http query.

```php
$quran->get('/chapters/1/verses', 'language=en&recitation=2'); // 2nd Parameter is optional
```

#### Chapter About
Fetches short information about chapter given, fetches about all if null.

```php
$quran->chapter(1,'about'); // 1st Parameter is optional

$quran->chapter('about'); // 2nd Parameter is must
```

#### Chapter Info
Fetches detailed information about chapter given, doesn't fetches for all chapters at once

```php
$quran->chapter(1, 'info');
```

#### Chapter Verses
Fetches the verses of a chapter provided.

```php
$quran->chapter(69)->verse(); // Fetches first 10 verses of given chapter

$quran->chapter(69)->verse([ // Fetches all the 52 verses of the chapter 69
    'offset' => 1,  // Starting verse
    'limit'  => 52, // How many verses - OPTIONAL - default: 10
]); 

$quran->chapter(69)->verse([ // Fetches 11 - 20 verses of the chapter
    'page'   => 2 // There is 10 verses in each page
]);

// Advance usage

$quran->chapter(69,[
    // You don't need these to provide each time you can set
    // them at once in top when instantiating the API Helper.
    'language'     => 'en',          // language - default: en
    'recitation'   => 1,             // recitation - default: 1
    'translations' => [21, 54, 40],  // translations - default: 21
    'text_type'    => 'image',       // text type - default: text
])->verse();

$verses = $quran->chapter(69)->verse(['page' => 1],[ // Gets what you want, just add the parameters
    'text_simple',
    'image'        => 'url',
    'audio'        => 'url',
    'translations' => ['text', 'language_name'],
]);

foreach($verses as $verse){
    echo '<pre>', print_r($verse, true), '</pre>';
}
```

#### Chapter Verse Tafsir
Fetches you the tafsir of the verse of a chapter given, only 1 verse tafsir on each request.

Gets the tafsir of a verse of chapter, by default gets all the tafsir if you want the specific one just provide the parameter inside tafsir method otherwise leave it blank it will fetch all the available tafsirs.
 
```php
$quran->chapter(114, 6)->tafsir(16);
```

#### Advance Usage
Example of a beautiful usage

```php
<?php

require 'vendor/autoload.php';

$quran = new \Quran\Quran([
    'cache'        => __DIR__ . '/cache',
    'language'     => 'en',
    'recitation'   => 1,
    'translations' => [21, 54, 40],
    'tafsirs'      => [16, 17],
    'text_type'    => 'image',
]);

$verses = $quran->chapter(1)->verse(['page' => 1], [
    'image'        => 'url',
    'audio'        => 'url',
    'translations' => 'text',
]);

foreach ($verses as $verse) {
    echo <<<EOT
<center>
<img src="{$verse['image_url']}">
<h3>{$verse['translations_text']}</h3>
<audio controls><source src="{$verse['audio_url']}" type="audio/mpeg"></audio>
</center><hr>
EOT;
}
```

#### Result:

<p align="center"><img src="http://i.imgur.com/qvK9X4m.png" alt="Result of the above code"></p>

## Cache
By calling cache method you see what files are cached, the date created and all the info.
cache must be enable.

```php
echo $quran->cache();
```
<p align="center"><img src="http://i.imgur.com/nYqoPxr.png" alt="Prints the cache"></p>

