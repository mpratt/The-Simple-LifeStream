<?php
/**
 * StackExchange.php
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
 * A provider for StackOverflow/StackExchange sites
 */
class StackExchange extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://api.stackexchange.com/2.1/users/%s/timeline?site=%s&filter=!)*GP5_z';

    /** inline {@inheritdoc} */
    protected $settings = array(
        'site' => 'stackoverflow'
    );

    /** @var array Allowed Types for this stream */
    protected $allowedTypes = array(
        'commented',
        'answered',
        'badge',
        'accepted',
        'asked',
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['items'])) {
            return array_filter(array_map(array($this, 'filterResponse'), $response['items']));
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
        if (!in_array($value['timeline_type'], $this->allowedTypes)) {
            return array();
        }

        $callbackReturn = $this->applyCallbacks($value);
        return array_merge($callbackReturn, array(
            'service'  => strtolower($this->settings['site']),
            'type'     => strtolower($value['timeline_type']),
            'resource' => $this->settings['resource'],
            'stamp'    => (int) $value['creation_date'],
            'url'      => $value['link'],
            'text'     => ($value['timeline_type'] == 'badge' ? $value['detail'] : $value['title']),
        ));
    }

    /** inline {@inheritdoc} */
    public function getApiUrl()
    {
        return sprintf($this->url, $this->settings['resource'], $this->settings['site']);
    }
}
?>
