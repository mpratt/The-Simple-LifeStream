<?php
/**
 * FacebookPages.php
 * A service for Facebook Pages
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class FacebookPages extends \SimpleLifestream\ServiceAdapter
{
    protected $url = 'http://www.facebook.com/feeds/page.php?id=%s&format=json';

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $response = json_decode($this->http->get(sprintf($this->url, $this->resource)), true);
        if (!empty($response['entries']))
            return array_map(array($this, 'filterResponse'), $response['entries']);

        throw new \Exception('No entries found on ' . sprintf($this->url, $this->resource));
    }

    /**
     * Callback method that filters/translates the ApiResponse
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse($value)
    {
        if (!empty($value['title']))
            $text = $value['title'];
        else if (!empty($value['content']))
            $text = (strlen($value['content']) > 130 ? substr($value['content'], 0, 130) . '...' : $value['content']);
        else
            $text = $value['alternate'];

        return array('service'  => 'facebookpages',
                     'type'     => 'link',
                     'resource' => $value['author']['name'],
                     'stamp'    => (int) strtotime($value['published']),
                     'url'      => $value['alternate'],
                     'text'     => $text);
    }
}

?>
