<?php
/**
 * IHttpRequest.php
 * This Interface defines the rules for a class that wants to fetch data from a website.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Interfaces;

interface IHttpRequest
{
    /**
     * Fetches for data in a url
     * Throws am exception when an error has ocurred.
     *
     * @param string $url The url
     * @return string
     */
    public function fetch($url);
}
?>
