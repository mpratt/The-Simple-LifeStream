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

class Twitter extends \SimpleLifestream\ServiceAdapter
{
    protected $url = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=%s';

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $response = json_decode($this->http->get(sprintf($this->url, $this->resource)), true);
        if (!empty($response) && empty($response['errors']) && empty($response['error']))
            return array_filter(array_map(array($this, 'filterResponse'), $response));

        throw new \Exception('The data returned by ' . sprintf($this->url, $this->resource) . ' seems invalid.');
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
