<?php
/**
 * HttpRequest.php
 *
 * @package SimpleLifestream
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleLifestream;

class HttpRequest Implements \SimpleLifestream\Interfaces\IHttp
{
    protected $config, $cache;

    /**
     * Constructor
     *
     * @param array $config
     * @param object $cache Instance of SimpleLifestream\Interfaces\ICache
     * @return void
     */
    public function __construct(array $config = array(), \SimpleLifestream\Interfaces\ICache $cache)
    {
        $config = array_intersect_key($config, array_flip(array('user_agent', 'timeout')));
        $this->config = array_merge(array(
            'user_agent' => 'Mozilla 5.0/ PHP/SimpleLifestream',
            'timeout' => 0
        ), $config);

        $this->cache = $cache;
    }

    /**
     * Checks if the response from a url was already cached.
     * If that is not the case, it makes the request, stores the response
     * and returns the value.
     *
     * @param string $url
     * @return string
     */
    public function get($url)
    {
        $url = trim($url);
        $id = 'http_' . md5($url);

        $return = $this->cache->read($id);
        if (empty($return))
        {
            $return = $this->makeRequest($url);
            $this->cache->store($id, $return);
        }

        return $return;
    }

    /**
     * Executes http requests
     *
     * @param string $url
     * @return string
     *
     * @throws Exception when an error ocurred or if no way to do a request exists
     */
    protected function makeRequest($url)
    {
        if (function_exists('curl_init'))
            return $this->fetchWithCurl($url);

        return $this->fetchWithFileGetContents($url);
    }

    /**
     * Uses Curl to fetch data from an url
     *
     * @param string $url
     * @return string
     *
     * @throws Exception when the returned status code is not 200 or no data was found
     */
    protected function fetchWithCurl($url)
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $this->config['user_agent'],
            CURLOPT_CONNECTTIMEOUT => $this->config['timeout'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => 'UTF-8',
            CURLOPT_RETURNTRANSFER => 1
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (empty($data) || !in_array($status, array('200')))
            throw new \Exception($status . ': Invalid response for ' . $url);

        return $data;
    }

    /**
     * Uses file_get_contents to fetch data from an url
     *
     * @param string $url
     * @return string
     *
     * @throws Exception when allow_url_fopen is disabled or when no data was returned
     */
    protected function fetchWithFileGetContents($url)
    {
        if (!ini_get('allow_url_fopen'))
            throw new \Exception('Could not execute lookup, allow_url_fopen is disabled');

        $context = array('http' => array(
            'method' => 'GET',
            'user_agent' => $this->config['user_agent']
        ));

        if ($data = file_get_contents($url, false, stream_context_create($context)))
            return $data;

        throw new \Exception('Invalid Server Response from ' . $url);
    }
}

?>
