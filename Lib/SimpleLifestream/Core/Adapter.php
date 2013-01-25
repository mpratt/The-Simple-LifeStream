<?php
/**
 * Adapter.php
 * Every service should extend this class.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Core;

abstract class Adapter
{
    protected $resource;

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     */
    abstract public function getApiData();

    /**
     * Gets information from a Url
     *
     * @param string $url
     * @param array  $headers
     * @param array  $options
     * @return string
     *
     * @throws RuntimeException when the status answer is not 200
     * @codeCoverageIgnore
     */
    protected function fetch($url, array $headers = array(), array $options = array())
    {
        if (!class_exists('\Requests'))
            return $this->fetchWithCurl($url, $headers, $options);

        $request = \Requests::get($url, $headers, $options);
        if (!$request->success)
            throw new \RuntimeException('The response from ' . $url . ' seems to be invalid');

        return $request->body;
    }

    /**
     * Fetches for data in a url using Curl
     *
     * @param string $url
     * @param array $headers
     * @param array $options
     * @return string
     *
     * @throws RuntimeException when the status answer is not 200
     * @codeCoverageIgnore
     */
    protected function fetchWithCurl($url, array $headers = array(), array $options = array())
    {
        if (!function_exists('curl_init'))
            throw new \RuntimeException('Curl must be installed on your server!.');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:2.0.1) Gecko/20110606 Firefox/4.0.1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        if (empty($data))
            throw new \RuntimeException('No data was found on ' . $url);

        return $data;
    }

    /**
     * Sets the Resource/Configuration for this service
     *
     * @param string $resource
     * @return void
     */
    public function setResource($resource) { $this->resource = $resource; }
}

?>
