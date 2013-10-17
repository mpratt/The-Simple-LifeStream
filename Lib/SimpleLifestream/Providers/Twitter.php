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
    protected $url = 'http://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=%s&count=%d';

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

        // In case curl is not installed
        if (!defined('CURLOPT_HTTPHEADER'))
            define('CURLOPT_HTTPHEADER', 'dummy_value');

        $options = array(
            'curl' => array(CURLOPT_HTTPHEADER => $header),
            'fopen' => array('header' => $header),
        );

        $response = $this->http->fetch($url, $options);
        $response = json_decode($response, true);

        if (!empty($response))
            return array_map(array($this, 'filterResponse'), $response);

        return null;
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
        foreach($params as $key => $value)
            $r[] = $key . '=' . rawurlencode($value);

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
        foreach($oauth as $key => $value)
            $values[] = $key . '="' . rawurlencode($value) . '"';

        return 'Authorization: OAuth ' . implode(', ', $values);
    }

    /** inline {@inheritdoc} */
    public function getApiUrl() { return sprintf($this->url, $this->settings['resource'], $this->settings['count']); }

    /** inline {@inheritdoc} */
    protected function filterResponse($value)
    {
        return array(
            'service'  => 'twitter',
            'type'     => 'tweeted',
            'resource' => $this->settings['resource'],
            'stamp'    => (int) strtotime($value['created_at']),
            'url'      => 'http://twitter.com/#!/' . $this->settings['resource'] . '/status/' . $value['id_str'],
            'text'     => $value['text']
        );
    }
}
?>
