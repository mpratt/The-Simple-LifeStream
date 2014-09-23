<?php
/**
 * Github.php
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
 * A provider for Github
 * @link http://developer.github.com/v3/activity/events/
 * @link http://developer.github.com/v3/activity/events/types/
 */
class Github extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://api.github.com/users/%s/events';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response)) {
            return array_map(array($this, 'filterResponse'), $response);
        }

        return null;
    }

    /**
     * Manages create events that represent a created repository, branch, or tag.
     *
     * @link http://developer.github.com/v3/activity/events/types/#createevent
     *
     * @param array $value
     * @return array
     */
    protected function createEvents(array $value)
    {
        $type = 'repo-created';
        $url  = $value['repo']['url'];
        $text = $value['repo']['name'];

        if (!empty($value['payload']['ref'])) {
            $text = $value['payload']['ref'] . ' (' . basename($value['repo']['name']) . ')';
            if ($value['payload']['ref_type'] == 'tag') {
                $type = 'repo-released';
            } else {

                // dont register the creation of a master branch
                if (strtolower(basename($value['payload']['ref'])) == 'master') {
                    return array();
                }

                $type = 'repo-created-branch';
            }
        }

        return array(
            'text' => $text,
            'url' => $url,
            'type' => $type
        );
    }

    /**
     * Manages events triggered when a repository branch is pushed to.
     *
     * @link http://developer.github.com/v3/activity/events/types/#pushevent
     *
     * @param array $value
     * @return array
     */
    protected function pushEvents(array $value)
    {
        $type = 'repo-pushed';
        $url  = $value['repo']['url'];
        $text = $value['repo']['name'];

        if (strtolower(basename($value['payload']['ref'])) != 'master') {
            $text = $value['repo']['name'] . ' (' . basename($value['payload']['ref']) . ')';
        }

        return array(
            'text' => $text,
            'url' => $url,
            'type' => $type
        );
    }

    /**
     * Filters and formats the response
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse(array $value = array())
    {
        $value['type'] = strtolower($value['type']);
        $actions = array(
            'createevent' => array($this, 'createEvents'),
            'pushevent' => array($this, 'pushEvents'),
            'watchevent' => function (array $value) {
                return array(
                    'type' => 'starred',
                    'text' => $value['repo']['name'],
                    'url' => $value['repo']['url'],
                );
            },
            'followevent' => function (array $value) {
                return array(
                    'type' => 'followed',
                    'text' => $value['payload']['target']['login'],
                    'url' => $value['payload']['target']['html_url'],
                );
            },
            'pullrequestevent' => function (array $value) {
                if (!in_array($value['payload']['action'], array('opened', 'closed', 'reopened'))) {
                    return array();
                }

                return array(
                    'type' => 'repo-pull-' . $value['payload']['action'],
                    'text' => $value['payload']['pull_request']['title'] . ' (' . $value['repo']['name'] . ')',
                    'url' => $value['payload']['pull_request']['html_url'],
                );
            },
            'issuesevent' => function (array $value) {
                if (!in_array($value['payload']['action'], array('opened', 'closed'))) {
                    return array();
                }

                return array(
                    'type' => 'repo-issue-' . $value['payload']['action'],
                    'text' => $value['payload']['issue']['title'] . ' (' . $value['repo']['name'] . ')',
                    'url' => $value['payload']['issue']['html_url'],
                );
            },
            'issuecommentevent' => function (array $value) {
                return array(
                    'type' => 'repo-issue-commented',
                    'text' => $value['payload']['issue']['title'] . ' (' . $value['repo']['name'] . ')',
                    'url' => $value['payload']['issue']['html_url'],
                );
            },
            'forkevent' => function (array $value) {
                return array(
                    'type' => 'repo-fork-created',
                    'text' => $value['repo']['name'] . '  ' . $value['payload']['forkee']['full_name'],
                    'url' => $value['payload']['forkee']['html_url'],
                );
            },
        );

        if (isset($actions[$value['type']])) {
            $urlTable = array('/repos/' => '/', '//api.github.com' => '//github.com');
            $data = call_user_func_array($actions[$value['type']], array($value));
            if (!$data) {
                return array();
            }

            $callbackReturn = $this->applyCallbacks($value);
            return array_merge($callbackReturn, array(
                'service'  => 'github',
                'type'     => strtolower($data['type']),
                'resource' => $value['actor']['login'],
                'stamp'    => (int) strtotime($value['created_at']),
                'url'      => str_replace(array_keys($urlTable), array_values($urlTable), $data['url']),
                'text'     => $data['text'],
            ));
        }

        return array();
    }
}
?>
