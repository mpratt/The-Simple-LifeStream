<?php
/**
 * Instagram.php
 *
 * @package Providers
 * @author  QWp6t <hi@qwp6t.me>
 * @link    http://qwp6t.me/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleLifestream\Providers;

/**
 * A provider for Instagram
 * @link http://stackoverflow.com/questions/6311195/how-to-get-a-users-instagram-feed
 */
class Instagram extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://api.instagram.com/v1/users/%s/media/recent?client_id=%s&count=%d';

    /** inline {@inheritdoc} */
    protected $settings = array(
        'resource' => '',
        'client_id' => '',
        'count' => 15,
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $url = $this->getApiUrl();
        $options = $this->createRequestOptions();
        $response = $this->http->fetch($url, $options);
        $response = json_decode($response, true);

        return (isset($response['meta']['code']) && $response['meta']['code'] === 200) ? array_map(array($this, 'filterResponse'), $response['data']) : null;
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
    protected function createRequestOptions()
    {
        if (!file_exists($cert = __DIR__ . '/../Certificates/cacert.pem')) {
            throw new \RuntimeException(
                sprintf('The Instagram adapter could not find the certificate in "%s"', $cert)
            );
        }

        $curl = $fopen = array();
        if (function_exists('curl_init')) {
            $curl = array(
                CURLOPT_CAINFO => $cert,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            );
        }

        $fopen =  array(
            'ssl' => array(
                'verify_peer'   => true,
                'cafile'        => $cert,
                'verify_depth'  => 9,
                'CN_match'      => 'api.instagram.com'
            )
        );

        return array(
            'curl' => $curl,
            'fopen' => $fopen,
        );
    }

    /** inline {@inheritdoc} */
    public function getApiUrl()
    {
        return sprintf($this->url, $this->settings['resource'], $this->settings['client_id'], $this->settings['count']);
    }

    /** inline {@inheritdoc} */
    protected function filterResponse($value)
    {
        $resource = $value[$value['type'] . 's']['standard_resolution']['url'];
        return array(
            'service'  => 'instagram',
            'type'     => (strtolower($value['type']) == 'video' ? 'uploaded-video' : 'took-picture'),
            'resource' => $resource,
            'username' => $value['user']['username'],
            'stamp'    => (int) $value['created_time'],
            'url'      => stripslashes($value['link']),
            'text'     => $value['caption']['text'],
            'thumbnail' => stripslashes($value['images']['thumbnail']['url'])
        );
    }
}
?>
