<?php
/**
 * Youtube.php
 * A service for youtube
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class Youtube extends \SimpleLifestream\ServiceAdapter
{
    protected $url = 'http://gdata.youtube.com/feeds/api/users/%s/favorites?v=2&alt=jsonc';

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $response = json_decode($this->http->get(sprintf($this->url, $this->resource)), true);
        if (!empty($response['data']['items']))
        {
            $return = array();
            foreach($response['data']['items'] as $value)
            {
                $return[] = array('service'  => 'youtube',
                                  'type'     => 'favorited',
                                  'resource' => $this->resource,
                                  'stamp'    => (int) strtotime($value['created']),
                                  'url'      => 'http://www.youtube.com/watch?v=' . $value['video']['id'],
                                  'text'     => $value['video']['title']);
            }

            return $return;
        }

        throw new \Exception('The data returned by ' . sprintf($this->url, $this->resource) . ' seems invalid.');
    }
}
?>
