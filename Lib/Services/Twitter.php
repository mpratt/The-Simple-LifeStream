<?php
/**
 * Twiter.php
 * A service for Twitter
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class Twitter extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->http->fetch('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $this->resource), true);
        if (!isset($apiResponse['error']) && !empty($apiResponse))
            return array_map(array($this, 'filterResponse'), $apiResponse);

        return array();
    }

    /**
     * Callback method that filters/translates the ApiResponse
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse($value)
    {
        return array('service'  => 'twitter',
                     'type'     => 'tweeted',
                     'resource' => $this->resource,
                     'stamp'    => (int) strtotime($value['created_at']),
                     'url'      => 'http://twitter.com/#!/' . $this->resource . '/status/' . $value['id_str'],
                     'text'     => $value['text']);
    }
}
?>
