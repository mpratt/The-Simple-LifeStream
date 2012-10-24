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

class FacebookPages extends \SimpleLifestream\Core\Adapter
{
    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->http->fetch('http://www.facebook.com/feeds/page.php?id=' . $this->resource . '&format=json'), true);
        if (!empty($apiResponse['link']) && !empty($apiResponse['entries']))
            return array_map(array($this, 'filterResponse'), $apiResponse['entries']);

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
        if (!empty($value['title']))
            $text = $value['title'];
        else if (!empty($value['content']))
            $text = (strlen($value['content']) > 130 ? substr($value['content'],0, 130) . '...' : $value['content']);

        if (empty($text) || trim($text) == '')
            $text = $value['alternate'];

        if (!empty($value['author']['name']))
            $resource = $value['author']['name'];
        else
            $resource = 'Facebook Pages';

        return array('service'  => 'facebookpages',
                     'type'     => 'link',
                     'resource' => $resource,
                     'stamp'    => (int) strtotime($value['published']),
                     'url'      => $value['alternate'],
                     'text'     => $text);
    }
}
?>
