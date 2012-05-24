<?php
/**
 * RedditService.php
 * A service for Reddit
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class RedditService extends SimpleLifestreamAdapter
{
    protected $translation = array('en' => array('comment' => 'commented on "<a href="%s">%s</a>".',
                                                 'created' => 'posted <a href="%s">%s</a>.',
                                                 'saved'   => 'added "<a href="%s">%s</a>" to his saved list.'),
                                   'es' => array('comment' => 'comento en "<a href="%s">%s</a>".',
                                                 'created' => 'posteó <a href="%s">%s</a>.',
                                                 'saved'   => 'guardo "<a href="%s">%s</a>" en sus favoritos.'));

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->fetchUrl('http://www.reddit.com/user/' . $this->config['username'] . '.json'), true);

        $return = array();
        if (!empty($apiResponse['data']['children']))
        {
            $comments = array_map(array($this, 'filterRecentActions'), $apiResponse['data']['children']);
            $return = array_merge($return, $comments);
        }

        if (!empty($this->config['saved_feed']) && strtolower(pathinfo($this->config['saved_feed']), PATHINFO_EXTENSION) == 'json')
        {
            $apiResponse = json_decode($this->fetchUrl($this->config['saved_feed']), true);
            if (!empty($apiResponse['data']['children']))
            {
                $saved = array_map(array($this, 'filterRecentActions'), $apiResponse['data']['children']);
                $return = array_merge($return, $saved);
            }
        }

        return $return;
    }

    /**
     * Callback method that filters/translates the ApiResponses
     * recent actions
     *
     * @param array $value
     * @return array
     */
    protected function filterRecentActions($value)
    {
        // We are only interested on this types
        if (empty($value['data']) || empty($value['data']['created']))
            return ;

        $link = 'http://www.reddit.com';
        if (!empty($value['data']['permalink']))
            $link .= $value['data']['permalink'];
        else
            $link .= '/r/'$value['data']['subreddit'] . '/comments/' . str_replace('t3_', '', $value['data']['link_id']) . '/#' . str_replace('t1', '', $value['data']['name']);

        switch ($value['kind'])
        {
            case 't1':
                $html = $this->translate('comment', $link, trim($value['data']['link_title']));
                break;

            case 't3':
                if ($value['data']['author'] == $this->config['username'])
                    $html = $this->translate('created', $link, trim($value['data']['title']));
                else
                    $html = $this->translate('saved', $link, trim($value['data']['title']));
                break;

            default:
                return array();
                break;
        }

        return array('service' => 'reddit',
                     'date' => $value['data']['created'],
                     'html' => $html);
    }
}
?>
