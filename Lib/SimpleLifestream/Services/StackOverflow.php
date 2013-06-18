<?php
/**
 * StackOverflow.php
 * A service for Stack Overflow
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class StackOverflow extends \SimpleLifestream\ServiceAdapter
{
    protected $url = 'http://api.stackoverflow.com/1.0/users/%s/timeline';

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $request = $this->http->get(sprintf($this->url, $this->resource), true);
        $response = json_decode($request, true);
        if (empty($response['user_timelines']))
            throw new \Exception('The data returned by ' . sprintf($this->url, $this->resource) . ' seems invalid.');

        return array_filter(array_map(array($this, 'filterResponse'), $response['user_timelines']));
    }

    /**
     * Callback method that filters/translates the ApiResponse
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse($value)
    {
        switch ($value['timeline_type'])
        {
            case 'askoranswered':
            case 'accepted':
                $url  = 'http://stackoverflow.com/questions/' . $value['post_id'];
                $text =  $value['description'];

                if ($value['action'] == 'accepted')
                    $type = 'acceptedAnswer';
                else
                    $type = $value['action'];

                break;

            case 'badge':

                if ($value['action'] != 'awarded')
                    return array();

                $url  = 'http://stackoverflow.com/users/' . $value['user_id'] . '?tab=reputation';
                $text = $value['description'] . ' (' . $value['detail'] . ')';
                $type = 'badgeWon';

                break;

            case 'comment':
                $url  = 'http://stackoverflow.com/questions/' . $value['post_id'] . '#' . $value['comment_id'];
                $text = $value['description'];
                $type = 'commented';

                break;

            default:
                return array();
                break;
        }

        return array('service'  => 'stackoverflow',
                     'type'     => lcfirst($type),
                     'resource' => $this->resource,
                     'stamp'    => (int) $value['creation_date'],
                     'url'      => $url,
                     'text'     => $text);
    }
}
?>
