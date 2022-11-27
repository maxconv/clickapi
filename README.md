<p align="center">
  <a href="https://maxconv.com">
    <img width="200" src="https://cdn.maxconv.com/logo-color.svg" />
  </a>
</p>

<h1 align="center">MaxConv Click API</h1>

English | [简体中文](./README-zh_CN.md)

**No redirect in entire process**. Get visitor's traffic metrics like country, operating system, browser...etc, or display appropriate lander according to flow rules. 

## 📦 How to use

1. Get your api key

    Login your maxconv account, get your api key at: [Settings -> Security -> API Keys](https://app.maxconv.com/app/settings/security)

2. Download [index.php](./index.php) in github, modify the config area:

    ```php
    // --- config area start--- //
    $apiKey='Your API Key Here';
    $campaignUrl='Your Campaign Link Here';
    // --- config area end --- //
    ```

    with your owner information, remove any any query paramater in campaign url, it should looks like `https://demode.maxconvtrk.com/visit/6c3e9664-d5ac-4170-95dd-8a81c85c4603`

3. Init api and load appropriate lander

    Use the code below to init the class and load lander that matched the flow rule.

    ```php
    //init
    $maxconv = new MaxConvClickAPI($campaignUrl, $apiKey);
    //load lander
    $maxconv->loadLander();
    ```

4. Add offer urls to lander

    Get offer url with method `$maxconv->getOfferUrl()`, this url is the offer url, so visitor will directly go to offer link. You can also use `{OFFER_URL}` in your lander, click api will replace it with offer url.

    Get lander click url with method `$maxconv->getLPClickUrl()`, this url is the will be url like `https://your-domain/click`, visitor will go to your tracking domain, then to the offer url. You can also use `{LANDER_CLICK_URL}` in your lander, click api will replace it with lander click url.

    Get lander click report url with method `$maxconv->getLPClickReportUrl()`, this url can be used to track a click event when you want visitor directly go to the offer, and track their clicks. You can also use `{LANDER_CLICK_REPORT_URL}` in your lander, click api will replace it with lander click report url.

## 🔨 Examples

#### Send visitors directly to offer, and track click

**index.php:**
```php
<?php

// --- config area start--- //
$apiKey='Your API kEY Here';
$campaignUrl='Your Campaign Url Here';
// --- config area end --- //

//init
$maxconv = new MaxConvClickAPI($campaignUrl, $apiKey);

//load lander
$maxconv->loadLander();

/**
 * This file is MaxConv Click API.
 *
 * (c) MaxConv <support@maxconv.com>
 *
 * For the full documentation and support, please visit https://maxconv.com
 */
class MaxConvClickAPI
{
    //api codes...
}
```

**lander.html:**
```html
<a 
    href="{OFFER_URL}"
    onclick="navigator.sendBeacon('{LANDER_CLICK_REPORT_URL}');"
    target="_blank"
>Get Offer</a>
```

#### Send visitors redirect to offer so you can hide referrer

**index.php:**
```php
<?php

// --- config area start--- //
$apiKey='Your API kEY Here';
$campaignUrl='Your Campaign Url Here';
// --- config area end --- //

//init
$maxconv = new MaxConvClickAPI($campaignUrl, $apiKey);

//load lander
$maxconv->loadLander();

/**
 * This file is MaxConv Click API.
 *
 * (c) MaxConv <support@maxconv.com>
 *
 * For the full documentation and support, please visit https://maxconv.com
 */
class MaxConvClickAPI
{
    //api codes...
}
```

**lander.html:**
```html
<a href="{LANDER_CLICK_URL}">Get Offer</a>
```

## ⌨️ API Functions

| Function name | Description |
| --- | --- |
| loadLander() | Display appropriate lander according to flow rules |
| getClickData() | Return current click data in array format, including country, browser...etc |
| getOfferUrl() | Return offer url |
| getLPClickUrl() | Return click url, which looks like `http://domain.com/click`, will be redirect to offer when clicked |
| getLPClickReportUrl() | Return a url that can be used to report a click event |

## 🤝 Support
`support@maxconv.com`
