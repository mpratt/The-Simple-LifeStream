<?php
/**
 * Reddit.php
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
 * A provider for Reddit
 */
class Reddit extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://www.reddit.com/user/%s.json';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['data']['children'])) {
            return array_map(array($this, 'filterResponse'), $response['data']['children']);
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
        if (empty($value['kind']) || !in_array($value['kind'], array('t1', 't3'))) {
            return array();
        }

        // Populate this keys just in case
        $value['data'] = array_merge(array(
            'link_title' => 'Unknown Title',
            'title' => 'Unknown Title',
            'subreddit' => 'Unknown Subreddit',
            'author' => 'Unknown Author',
        ), $value['data']);

        $url = 'http://www.reddit.com';
        if (!empty($value['data']['permalink'])) {
            $url .= $value['data']['permalink'];
        } else {
            $value['data']['link_id'] = $this->stripRedditIds($value['data']['link_id']);
            $value['data']['name'] = $this->stripRedditIds($value['data']['name']);
            $url .= '/r/' . $value['data']['subreddit'] . '/comments/' . $value['data']['link_id'] . '/#' . $value['data']['name'];
        }

        $modes = array(
            't1' => array(
                'type' => 'commented',
                'title' => $value['data']['link_title']
            ),
            't3' => array(
                'type' => 'posted',
                'title' => $value['data']['title']
            ),
        );

        $text = $modes[$value['kind']]['title'];
        if (!empty($text)) {
            $callbackReturn = $this->applyCallbacks($value);
            return array_merge($callbackReturn, array(
                'service'  => 'reddit',
                'type'     => $modes[$value['kind']]['type'],
                'resource' => $value['data']['author'],
                'stamp'    => (int) $value['data']['created_utc'],
                'url'      => $url,
                'text'     => $text,
                'subreddit' => $value['data']['subreddit'],
            ));
        }

        return array();
    }

    /**
     * Strips t1_ and t3_ prefixes from a string
     *
     * @param string $id
     * @return string
     */
    protected function stripRedditIds($id)
    {
        return preg_replace('~^(t1|t3)_~', '', $id);
    }
}
?>
