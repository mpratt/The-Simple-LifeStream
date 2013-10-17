<?php
/**
 * IHttp.php
 *
 * @package Interfaces
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Interfaces;

/**
 * This Interface defines the common methods for a class that wants to make
 * Http Requests.
 */
interface IHttp
{
    /**
     * Checks if the response from a url was already cached.
     * If that is not the case, it makes the request, stores the response
     * and returns the value.
     *
     * @param string $url
     * @return string
     */
    public function get($url);
}
?>
