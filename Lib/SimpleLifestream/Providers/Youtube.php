<?php
/**
 * Youtube.php
 *
 * @package Providers
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Providers;

/**
 * A provider for youtube
 */
class Youtube extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://gdata.youtube.com/feeds/api/users/%s/favorites?v=2&alt=jsonc';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['data']['items'])) {
            return array_map(array($this, 'filterResponse'), $response['data']['items']);
        }

        return null;
    }

    /**
     * Filters and formats the response
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse(array $value = array())
    {
        if (!isset($value['video']['thumbnail']['sqDefault'])) {
            $value['video']['thumbnail']['sqDefault'] = 'http://s.ytimg.com/yts/img/youtube_logo_stacked-vfl225ZTx.png';
        }

        $callbackReturn = $this->applyCallbacks($value);
        return array_merge($callbackReturn, array(
            'service'  => 'youtube',
            'type'     => 'favorited',
            'resource' => $value['author'],
            'username' => $value['author'],
            'stamp'    => (int) strtotime($value['created']),
            'url'      => 'http://www.youtube.com/watch?v=' . $value['video']['id'],
            'text'     => $value['video']['title'],
            'thumbnail' => $value['video']['thumbnail']['sqDefault'],
        ));
    }
}
?>
