<?php
/**
 * Twiter.php
 *
 * @package Providers
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Providers;

/**
 * A provider for Twitter
 * @link http://stackoverflow.com/questions/12916539/simplest-php-example-for-retrieving-user-timeline-with-twitter-api-version-1-1
 */
class Twitter extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=%s&count=%d';

    /** inline {@inheritdoc} */
    protected $settings = array(
        'consumer_key' => '',
        'consumer_secret' => '',
        'access_token' => '',
        'access_token_secret' => '',
        'resource' => '',
        'count' => 50,
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $url = $this->getApiUrl();
        $oauth = array(
            'oauth_consumer_key' => $this->settings['consumer_key'],
            'oauth_token' => $this->settings['access_token'],
            'oauth_nonce' => uniqid(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'screen_name' => $this->settings['resource'],
            'count' => $this->settings['count'],
        );

        $baseInfo = $this->buildBaseString($url, 'GET', $oauth);
        $compositeKey = rawurlencode($this->settings['consumer_secret']) . '&' . rawurlencode($this->settings['access_token_secret']);
        $oauthSignature = base64_encode(hash_hmac('sha1', $baseInfo, $compositeKey, true));
        $oauth = array_merge($oauth, array('oauth_signature' => $oauthSignature));
        $header = array($this->buildAuthorizationHeader($oauth), 'Expect:');

        $options = $this->createRequestOptions($header);
        $response = $this->http->fetch($url, $options);
        $response = json_decode($response, true);

        if (!empty($response)) {
            return array_map(array($this, 'filterResponse'), $response);
        }

        return null;
    }

    /**
     * Creates the required parameters for the http wrapper.
     *
     * @link http://curl.haxx.se/docs/caextract.html
     * @link https://dev.twitter.com/docs/security/using-ssl
     *
     * @param array $header
     * @return array
     *
     * @throws RuntimeException when no cacert.pem was found
     */
    protected function createRequestOptions($header)
    {
        if (!file_exists($cert = __DIR__ . '/../Certificates/cacert.pem')) {
            throw new \RuntimeException(
                sprintf('The Twitter could not find the certificate in "%s"', $cert)
            );
        }

        $curl = array();
        if (function_exists('curl_init')) {
            $curl = array(
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_CAINFO => $cert,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            );
        }

        $fopen =  array(
            'http' => array(
                'header' => $header
            ),
            'ssl' => array(
                'verify_peer'   => true,
                'cafile'        => $cert,
                'verify_depth'  => 9,
                'CN_match'      => 'api.twitter.com'
            )
        );

        return array(
            'curl' => $curl,
            'fopen' => $fopen,
        );
    }

    /**
     * Method to generate the base string used by cURL
     *
     * @param string $baseURI
     * @param string $method
     * @param array $params
     * @return string Built base string
     */
    protected function buildBaseString($baseURI, $method, array $params = array())
    {
        // Strip querystring from url
        $baseURI = preg_replace('~\?.*~i', '', $baseURI);

        $r = array();
        ksort($params);
        foreach($params as $key => $value) {
            $r[] = $key . '=' . rawurlencode($value);
        }

        return $method . '&' . rawurlencode($baseURI). '&' . rawurlencode(implode('&', $r));
    }

    /**
     * Method to generate authorization header used by cURL
     *
     * @param array $oauth
     * @return string $return Header used by cURL/file_get_contents for request
     */
    protected function buildAuthorizationHeader(array $oauth = array())
    {
        $values = array();
        foreach($oauth as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        return 'Authorization: OAuth ' . implode(', ', $values);
    }

    /** inline {@inheritdoc} */
    public function getApiUrl()
    {
        return sprintf($this->url, $this->settings['resource'], $this->settings['count']);
    }

    /**
     * Extracts media entities from a tweet response
     *
     * @param array $value
     * @return array
     */
    protected function getMedia(array $value)
    {
        $return = array();
        if (!isset($value['entities']) || !isset($value['entities']['media'])) {
            return $return;
        }

        foreach ((array) $value['entities']['media'] as $media) {
            $url = (isset($media['media_url_https']) ? $media['media_url_https'] : $media['media_url']);
            $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH),PATHINFO_EXTENSION));
            if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {
                $return[] = sprintf('<a href="%s" rel="external nofollow" data-media="%s"><img class="sl-tw-img-twitter-img" src="%s" alt="" title=""></a>', $url, $url, $url);
            } else {
                $return[] = sprintf('<a href="%s" rel="external nofollow" data-media="%s">%s</a>', $url, $url, $media['display_url']);
            }
        }

        return $return;
    }

    /**
     * Filters and formats the response
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse(array $value = array())
    {
        $tweet = $tweetRaw = $value['text'];
        if (isset($value['retweeted_status'])) {
            $tweet = $tweetRaw = 'RT @' . $value['retweeted_status']['user']['screen_name'] . ': ' . $value['retweeted_status']['text'];
        }

        $tweet = preg_replace_callback('~\bhttp(s)?://([\w#$%&\~/=?@\/\.])+\b/?~i', function ($matches) {
            $url = $matches[0];
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }

            return sprintf('<a href="%s" rel="nofollow" target="_blank">%s</a>', $url, $url);
        }, $tweet);

        $tweet = preg_replace('~#([\pL-_+]+)~ui', ' <a href="https://twitter.com/hashtag/$1?src=hash" target="_blank" rel="nofollow">#$1</a>', $tweet);
        $tweet = preg_replace('~@([\pL-_+]+)~ui', ' <a href="https://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $tweet);

        $callbackReturn = $this->applyCallbacks($value);
        return array_merge($callbackReturn, array(
            'service'  => 'twitter',
            'type'     => 'tweeted',
            'resource' => $this->settings['resource'],
            'stamp'    => (int) strtotime($value['created_at']),
            'url'      => 'http://twitter.com/#!/' . $this->settings['resource'] . '/status/' . $value['id_str'],
            'text'     => $tweetRaw,
            'tweet_html' => $tweet,
            'tweet_media' => $this->getMedia($value),
        ));
    }

}
?>
