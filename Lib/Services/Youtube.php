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

class Youtube extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $return = array();
        $apiResponse = json_decode($this->http->fetch('http://gdata.youtube.com/feeds/api/users/' . $this->resource . '/favorites?v=2&alt=jsonc'), true);
        if (!empty($apiResponse['data']['items']))
        {
            foreach($apiResponse['data']['items'] as $value)
            {
                $return[] = array('service'  => 'youtube',
                                  'type'     => 'favorited',
                                  'resource' => $this->resource,
                                  'stamp'    => (int) strtotime($value['created']),
                                  'url'      => 'http://www.youtube.com/watch?v=' . $value['video']['id'],
                                  'text'     => $value['video']['title']);
            }
        }

        return $return;
    }
}
?>
