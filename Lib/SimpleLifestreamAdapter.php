<?php
/**
 * SimpleLifestreamAdapter.php
 * Every service should extend this class.
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
abstract class SimpleLifestreamAdapter
{
    protected $config;
    protected $requires = array('username');

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     */
    abstract public function getApiData();

    /**
     * Sets the configuration for this service
     *
     * @param array $config
     * @return void
     */
    public function setConfig($config)
    {
        if (!empty($this->requires) && !in_array($this->requires, array_keys($config)))
        {
            foreach ($this->requires as $key)
            {
                if (empty($config[$key]))
                    throw new Exception('The Service "' . get_class($this) . '" requires a configuration key named "' . $key . '".');
            }
        }

        $this->config = $config;
    }

    /**
     * A convenience method that fetches the contents of a url
     *
     * @param string $url
     * @return string
     */
    protected function fetchUrl($url)
    {
        if (!function_exists('curl_init'))
            throw new Exception('Curl must be installed on your server');

        $timeout = (isset($this->config['timeout']) && $this->config['timeout'] > 0 ? (int) $this->config['timeout'] : 4);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:2.0.1) Gecko/20110606 Firefox/4.0.1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        if (empty($data))
            throw new Exception('No data was found on ' . parse_url($url, PHP_URL_HOST) . ' | Try again please!');

        return $data;
    }
}
?>
