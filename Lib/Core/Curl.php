<?php
/**
 * Curl.php
 * An Http Service that uses Curl to fetch data from a website
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Core;

class Curl implements \SimpleLifestream\Interfaces\IHttpRequest
{
    /**
     * Fetches for data in a url
     *
     * @param string $url The url
     * @return string
     */
    public function fetch($url)
    {
        if (!function_exists('curl_init'))
            throw new \Exception('Curl must be installed on your server! You might need to implement your own Http Wrapper.');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:2.0.1) Gecko/20110606 Firefox/4.0.1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        if (empty($data))
            throw new \Exception('No data was found on ' . parse_url($url, PHP_URL_HOST) . ' | Please try again!');

        return $data;
    }
}
?>
