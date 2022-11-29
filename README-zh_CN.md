<p align="center">
  <a href="https://maxconv.com">
    <img width="200" src="https://cdn.maxconv.com/logo-color.svg" />
  </a>
</p>

<h1 align="center">MaxConv Click API</h1>

[English](./README.md) | 简体中文

**全程无任何跳转**。获取用户的点击参数如国家，操作系统，浏览器等，或者根据规则自动展示相应的着陆页。

## 📦 如何使用

1. 获取API秘钥

    登录您的maxconv账号, 在 [Settings -> Security -> API Keys](https://app.maxconv.com/app/settings/security) 获取您的API秘钥。

2. 下载 [index.php](./index.php), 修改配置区域:

    ```php
    // --- config area start--- //
    $apiKey='Your API Key Here';
    $campaignUrl='Your Campaign Link Here';
    // --- config area end --- //
    ```

    将`Your API Key Here`替换成您的api key，将`Your Campaign Link Here`替换成您的campaign链接, campaign链接应该删去所有的参数，如 `https://demode.maxconvtrk.com/visit/6c3e9664-d5ac-4170-95dd-8a81c85c4603`

3. 初始化API，并展示相应的着陆页

    使用以下代码：

    ```php
    //init
    $maxconv = new MaxConvClickAPI($campaignUrl, $apiKey);
    //load lander
    $maxconv->loadLander();
    ```

4. 将Offer链接添加到着陆页

    用`$maxconv->getOfferUrl()`获取Offer链接, 用户点击链接会直接到Offer页面，中间无跳转. 您也可以在着陆页用`{OFFER_URL}`标记, API会自动将此标记替换成offer链接.

    用`$maxconv->getLPClickUrl()`获取着陆页点击链接, 此链接看上去像 `https://your-domain/click`, 用户点击链接会先到您的追踪域名，然后重定向到offer链接。您也可以在着陆页用`{LANDER_CLICK_URL}`标记, API会自动将此标记替换成着陆页点击链接.

    用`$maxconv->getLPClickReportUrl()`获取上报点击事件的链接, 此链接可以用来上报用户点击事件。您也可以在着陆页用`{LANDER_CLICK_URL}`，API会自动将此标记替换成上报点击事件的链接。

## 🔨 示例

#### 用户点击直接到offer，同时追踪点击

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

#### 用户重定向到offer，此方式可以隐藏着陆页地址

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

## ⌨️ API 方法

| 方法名称 | 功能 |
| --- | --- |
| loadLander() | 根据规则展示相应的着陆页 |
| getClickData() | 获取当前用户的点击数据（数组形式），包括国家，浏览器...等 |
| getOfferUrl() | 获取Offer链接 |
| getLPClickUrl() | 获取着陆页点击链接，看上去像`http://domain.com/click` |
| getLPClickReportUrl() | 获取用来上报用户点击的URL |

## 🤝 支持
`support@maxconv.com`，或者通过在线聊天组件和我们联系，组件位于MaxConv控制面板右上角。
