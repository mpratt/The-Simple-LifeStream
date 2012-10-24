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

class StackOverflow extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->http->fetch('http://api.stackoverflow.com/1.0/users/' . $this->resource . '/timeline'), true);
        if (empty($apiResponse['user_timelines']))
            return array();

        return array_map(array($this, 'filterResponse'), $apiResponse['user_timelines']);
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
