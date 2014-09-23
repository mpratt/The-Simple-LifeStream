<?php
/**
 * FacebookPages.php
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
 * A provider for Facebook Pages
 */
class FacebookPages extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://www.facebook.com/feeds/page.php?id=%s&format=json';

    /** inline {@inheritdoc} */
    protected $settings = array(
        'content_length' => '80',
        'content_delimiter' => '...',
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['entries'])) {
            return array_map(array($this, 'filterResponse'), $response['entries']);
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
        $callbackReturn = $this->applyCallbacks($value);

        if (trim($value['title']) !== '') {
            $text = $value['title'];
        } else if (trim(strip_tags($value['content'])) !== '') {
            $text = strip_tags($value['content']);
        } else {
            $text = $value['alternate'];
        }

        $text = $this->truncate(trim($text));
        return array_merge($callbackReturn, array(
            'service'  => 'facebookpages',
            'type'     => 'link',
            'resource' => $value['author']['name'],
            'stamp'    => (int) strtotime($value['published']),
            'url'      => $value['alternate'],
            'text'     => $text
        ));
    }

    /**
     * Truncates the $text
     *
     * @param string $text
     * @return string
     */
    protected function truncate($text)
    {
        if (strlen($text) > $this->settings['content_length']) {
            return substr($text, 0, $this->settings['content_length']) . $this->settings['content_delimiter'];
        }

        return $text;
    }
}

?>
