<?php
/**
 * Github.php
 * A service for Github
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Services;

class Github extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = utf8_encode($this->http->fetch('https://github.com/' . $this->resource . '.json'));
        $apiResponse = json_decode($apiResponse, true);

        if (!empty($apiResponse) && is_array($apiResponse))
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
        // We are only interested on this types
        if (!in_array($value['type'], array('PushEvent', 'CreateEvent', 'GistEvent', 'WatchEvent', 'FollowEvent')))
            return ;

        // Store the username as resource
        $resource = $value['actor'];

        $html = 'unknown action';
        switch ($value['type'])
        {
            case 'CreateEvent':
            case 'PushEvent':

                // Github registers CreateEvents twice! The first one is done when you create the repo via webbrowser
                // and the second one when you actually do your first push.
                // To avoid double-posting we just choose the second one.
                if (empty($value['payload']['ref']) || empty($value['repository']))
                    return ;

                $type = $value['type'];
                $url  = $value['repository']['url'];
                $text = $value['repository']['name'];

                break;

            case 'GistEvent':
                $type = $value['payload']['action'] . 'Gist';
                $url  = $value['payload']['url'];
                $text = $value['payload']['name'];

                break;

            case 'WatchEvent':

                $type = 'starred';
                $url  = $value['url'];
                $text = $value['repository']['name'];

                break;

            case 'FollowEvent':

                $type = 'followed';
                $url  = $value['url'];
                $text = $value['payload']['target']['login'];

                break;

            default :
                return array();
                break;
        }

        return array('service'  => 'github',
                     'type'     => lcfirst($type),
                     'resource' => $resource,
                     'stamp'    => (int) strtotime($value['created_at']),
                     'url'      => $url,
                     'text'     => $text);
    }
}
?>
