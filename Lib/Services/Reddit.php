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

class Reddit extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->http->fetch('http://www.reddit.com/user/' . $this->resource. '.json'), true);
        if (!empty($apiResponse['data']['children']))
            return array_map(array($this, 'filterActions'), $apiResponse['data']['children']);

        return array();
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
        // We are only interested on this types
        if (empty($value['data']) || empty($value['data']['created']))
            return ;

        $resource = $value['data']['author'];
        $url = 'http://www.reddit.com';
        if (!empty($value['data']['permalink']))
            $url .= $value['data']['permalink'];
        else
            $url .= '/r/' . $value['data']['subreddit'] . '/comments/' . str_replace('t3_', '', $value['data']['link_id']) . '/#' . str_replace('t1_', '', $value['data']['name']);

        switch ($value['kind'])
        {
            case 't1':
                $type = 'commented';
                $text = $value['data']['link_title'];
                break;

            case 't3':
                $type = 'posted';
                $text = $value['data']['title'];
                break;

            default:
                return array();
                break;
        }

        return array('service'  => 'reddit',
                     'type'     => lcfirst($type),
                     'resource' => $resource,
                     'stamp'    => (int) $value['data']['created'],
                     'url'      => $url,
                     'text'     => $text);
    }
}
?>
