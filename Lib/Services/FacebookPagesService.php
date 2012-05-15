<?php
/**
 * FacebookPagesService.php
 * A service for Facebook Pages
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class FacebookPagesService extends SimpleLifestreamAdapter
{
    protected $translation = array('en' => array('no_title' => 'No title'),
                                   'es' => array('no_title' => 'Sin titulo'));

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->fetchUrl('http://www.facebook.com/feeds/page.php?id=' . $this->config['username'] . '&format=json'), true);

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
            $title = $value['title'];
        else if (!empty($value['content']))
            $title = (strlen($value['content']) > 130 ? substr($value['content'],0, 130) . '...' : $value['content']);
        else
            $title = $this->translate('no_title');

        return array('service' => 'facebookpages',
                     'date' => strtotime($value['published']),
                     'html' => sprintf('<a href="%s" target="_blank">%s</a>', $value['alternate'], $title));
    }
}
?>
