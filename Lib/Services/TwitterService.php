<?php
/**
 * TwiterService.php
 * A service for Twitter
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class TwitterService extends SimpleLifestreamAdapter
{
    protected $translation = array('en' => array('view' => 'view tweet.'),
                                   'es' => array('view' => 'ver tweet.'));

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->fetchUrl('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $this->config['username']), true);

        if (!isset($apiResponse['error']) || !empty($apiResponse))
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
        return array('service' => 'twitter',
                     'date' => strtotime($value['created_at']),
                     'html' => $value['text'] . ' (<a href="http://twitter.com/#!/' . $this->config['username'] . '/status/' . $value['id_str'] . '">' . $this->translate('view') . '</a>)');
    }
}
?>
