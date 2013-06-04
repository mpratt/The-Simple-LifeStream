<?php
/**
 * Reddit.php
 * A service for Reddit
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class Reddit extends \SimpleLifestream\ServiceAdapter
{
    protected $url = 'http://www.reddit.com/user/%s.json';
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $response = json_decode($this->http->get(sprintf($this->url, $this->resource)), true);
        if (!empty($response['data']['children']))
            return array_filter(array_map(array($this, 'filterActions'), $response['data']['children']));

        throw new \Exception('The data returned by ' . sprintf($this->url, $this->resource) . ' seems invalid.');
    }

    /**
     * Callback method that filters/translates the ApiResponses
     * recent actions
     *
     * @param array $value
     * @return array
     */
    protected function filterActions($value)
    {
        $modes = array('t1' => array('type' => 'commented', 'text' => isset($value['data']['link_title']) ? $value['data']['link_title'] : 'Unknown'),
                       't3' => array('type' => 'posted', 'text' => isset($value['data']['title']) ? $value['data']['title'] : 'unkown'));

        if (empty($value['data']) || empty($value['kind']) || !isset($modes[$value['kind']]))
            return array();

        $url = 'http://www.reddit.com';
        if (!empty($value['data']['permalink']))
            $url .= $value['data']['permalink'];
        else
            $url .= '/r/' . $value['data']['subreddit'] . '/comments/' . str_replace('t3_', '', $value['data']['link_id']) . '/#' . str_replace('t1_', '', $value['data']['name']);

        return array('service'  => 'reddit',
                     'type'     => $modes[$value['kind']]['type'],
                     'resource' => $value['data']['author'],
                     'stamp'    => (int) $value['data']['created'],
                     'url'      => $url,
                     'text'     => $modes[$value['kind']]['text']);
    }
}
?>
