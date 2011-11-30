<?php
/**
 * YoutubeService.php
 * A service for youtube
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class YoutubeService extends SimpleLifestreamAdapter
{
    protected $translation = array('favorites' => 'guardó <a href="http://www.youtube.com/watch?v=%s">%s</a> en sus favoritos.');

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->fetchUrl('http://gdata.youtube.com/feeds/api/users/' . $this->config['username'] . '/favorites?v=2&alt=jsonc'), true);

        $return = array();
        if (!empty($apiResponse['data']['items']))
        {
            foreach($apiResponse['data']['items'] as $value)
            {
                $return[] = array('service' => 'youtube',
                                  'date' => strtotime($value['created']),
                                  'html' => sprintf($this->translation['favorites'], $value['video']['id'], $value['video']['title']));
            }
        }

        return $return;
    }
}
?>