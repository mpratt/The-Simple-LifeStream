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
 */
class Github extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://github.com/%s.json';

    /** @var array Allowed Types for this stream */
    protected $allowedTypes = array(
        'pushevent',
        'createevent',
        'followevent',
        'watchevent',
        'issuecommentevent',
        'pullrequestevent',
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response))
            return array_map(array($this, 'filterResponse'), $response);

        return null;
    }

    /** inline {@inheritdoc} */
    protected function filterResponse($value)
    {
        $value['type'] = strtolower($value['type']);
        if (!in_array($value['type'], $this->allowedTypes))
            return array();

        switch ($value['type'])
        {
            case 'createevent':
            case 'pushevent':

                /**
                 * Github registers CreateEvents twice! The first one is done when you create the repo via webbrowser
                 * and the second one when you actually do your first push.
                 * To avoid double-posting we just choose the second one.
                 */
                if (empty($value['payload']['ref_type']) || empty($value['payload']['ref']))
                    return array();

                $text = $value['repo']['name'];
                if ($value['payload']['ref_type'] == 'tag')
                {
                    $type = 'repo-released';
                    $text = $value['payload']['ref'] . ' (' . basename($value['repo']['name']) . ')';
                }
                else
                    $type = 'repo-' . str_replace('event', '', $value['type']);

                $url = $value['repo']['url'];
                break;

            case 'watchevent':

                $type = 'starred';
                $url  = $value['repo']['url'];
                $text = $value['repo']['name'];
                break;

            case 'followevent':

                $type = 'followed';
                $url  = $value['url'];
                $text = $value['payload']['target']['login'];
                break;

            case 'pullrequestevent':

                if ($value['payload']['action'] != 'opened')
                    return array();

                $type = 'repo-pull-' . strtolower($value['payload']['action']);
                $url  = $value['payload']['pull_request']['html_url'];
                $text = $value['repo']['name'];
                break;

            case 'issuecommentevent':

                $type = 'repo-issue-' . strtolower($value['payload']['action']);
                $url  = $value['payload']['issue']['url'];
                $text = $value['payload']['issue']['title'];
                break;
        }

        return array(
            'service'  => 'github',
            'type'     => strtolower($type),
            'resource' => $value['actor']['login'],
            'stamp'    => (int) strtotime($value['created_at']),
            'url'      => $url,
            'text'     => $text,
         );
    }
}
?>
