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
    /**
     * campaign url
     *
     * @var string
     */
    private $campaignUrl;

    /**
     * your api key
     *
     * @var string
     */
    private $apiKey;

    /**
     * click data of current request, including geo data, browsers, device, os...etc
     *
     * @var array
     */
    private $clickData = [];

    /**
     * construct class
     *
     * @param string $campaignUrl campaign url
     * @param string $apiKey api key
     */
    public function __construct($campaignUrl, $apiKey)
    {
        //check curl extension
        if (!extension_loaded('curl')) {
            echo "MaxConvClickAPI - Error: PHP curl extension is not loaded, please install and enable it.";
            return;
        }

        $this->apiKey = $apiKey;

        //if specify different campaign id
        if (isset($_GET['_camp']) && strpos($campaignUrl, '/visit/{CAMP_ID}') !== false) {
            $campaignUrl = str_replace('/visit/{CAMP_ID}', "/visit/{$_GET['_camp']}", $campaignUrl);
        }

        //change to http to get fastest speed
        $campaignUrl = str_replace(['https://', '/visit/'], ['http://', '/visitapi/'], $campaignUrl);

        //replace query parameters if any
        if (is_array($_GET) && !empty($_GET)) {
            //remove query if any
            if (stripos($campaignUrl, '?') !== false) {
                $campaignUrl = preg_replace('/\?.*?$/', '', $campaignUrl);
            }

            //add current query params
            $campaignUrl .= "?".http_build_query($_GET);
        }

        $this->campaignUrl = $campaignUrl;

        //retrieve click data
        $ret = $this->fetchClickData();
        if (!$ret) {
            echo "MaxConvClickAPI - Error: Click API return empty response";
            return;
        }

        if (!$ret['success']) {
            echo "MaxConvClickAPI - Error: ".$ret['errorMessage'];
        }

        //assign click data
        $this->clickData = $ret['data'];

        //set cookie(as first party) if any
        if (isset($ret['data']['set_cookie']) && !empty($ret['data']['set_cookie'])) {
            //set cookie as first-party
            foreach ($ret['data']['set_cookie'] as $name=>$value) {
                setcookie($name, $value, time() + 15552010, '/', '');
            }
        }
    }

    /**
     * display appropriate lander
     *
     * @return void
     */
    public function loadLander()
    {
        if (empty($this->clickData)) {
            return;
        }

        $landerUrl = $this->getLanderUrl();

        if (stripos($landerUrl, 'http') !== 0) {
            echo "MaxConvClickAPI - Error, invalid lander: {$landerUrl}";
            return;
        }

        //options
        $ch = curl_init($landerUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => [
                "User-Agent: MaxConv Click Data API"
            ],
            CURLOPT_ENCODING=>''
        ]);

        //execute
        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "MaxConvClickAPI - Load lander error, ".curl_error($ch);
            curl_close($ch);
            return;
        }

        curl_close($ch);

        //replace lander vars
        $html = str_replace(
            [
                '{OFFER_URL}',
                '{LANDER_CLICK_URL}',
                '{LANDER_CLICK_REPORT_URL}'
            ],
            [
                isset($this->clickData['offer_url']) ? $this->clickData['offer_url'] : '#',
                isset($this->clickData['lander_click_url']) ? $this->clickData['lander_click_url'] : '#',
                isset($this->clickData['lander_click_report_url']) ? $this->clickData['lander_click_report_url'] : '#'
            ],
            $html
        );

        echo $html;
    }

    /**
     * fetch click data
     *
     * @return void
     */
    public function fetchClickData()
    {
        //headers
        $headers = $this->getAllHeaders();
        unset($headers['Host']);
        unset($headers['Content-Type']);
        unset($headers['Content-Length']);
        $headers['Connection'] = '';
        $headers['Authorization'] = $this->apiKey;
        $headers['X-Forwarded-For'] = $_SERVER['REMOTE_ADDR'];
        $curlHeaders = [];
        foreach ($headers as $name=>$value) {
            $curlHeaders[] = "{$name}: {$value}";
        }

        //options
        $ch = curl_init($this->campaignUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_ENCODING=>''
        ]);

        //execute
        $body = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            return [
                'success'=>false,
                'errorMessage'=>$errorMessage
            ];
        }

        curl_close($ch);

        if (!$body) {
            return [
                'success'=>false,
                'errorMessage'=>"Empty response"
            ];
        }

        $bodyArr = @json_decode($body, true);

        if (!is_array($bodyArr)) {
            return [
                'success'=>false,
                'errorMessage'=>"None-jason response: {$body}"
            ];
        }

        return $bodyArr;
    }

    /**
     * polyfill for getallheaders().
     *
     * @return array
     */
    public function getAllHeaders()
    {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        return $headers;
    }

    /**
     * a url that report landing page click when requested
     *
     * @return void
     */
    public function getLPClickReportUrl()
    {
        return isset($this->clickData['lander_click_report_url']) && $this->clickData['lander_click_report_url'] ? $this->clickData['lander_click_report_url'] : '#';
    }

    /**
     * landing page url click url, which redirect to offer link
     *
     * @return void
     */
    public function getLPClickUrl()
    {
        return isset($this->clickData['lander_click_url']) && $this->clickData['lander_click_url'] ? $this->clickData['lander_click_url'] : '#';
    }

    /**
     * get current click data
     *
     * @return array
     */
    public function getClickData()
    {
        return $this->clickData;
    }

    /**
     * get lander url
     *
     * @return string
     */
    public function getLanderUrl()
    {
        return isset($this->clickData['lander_url']) && $this->clickData['lander_url'] ? $this->clickData['lander_url'] : '*Unknown';
    }

    /**
     * get offer url
     *
     * @return string
     */
    public function getOfferUrl()
    {
        return isset($this->clickData['offer_url']) && $this->clickData['offer_url'] ? $this->clickData['offer_url'] : '*Unknown';
    }
}
