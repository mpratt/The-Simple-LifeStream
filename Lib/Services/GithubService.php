<?php
/**
 * GithubService.php
 * A service for Github
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class GithubService extends SimpleLifestreamAdapter
{
    protected $translation = array('PushEvent'   => 'actualizó el proyecto <a href="%s">%s</a>.',
                                   'CreateEvent' => 'creó el proyecto <a href="%s">%s</a>.',
                                   'createGist'  => 'creó el gist <a href="%s">%s</a>',
                                   'updateGist'  => 'actualizó el gist <a href="%s">%s</a>');

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = utf8_encode($this->fetchUrl('https://github.com/' . $this->config['username'] . '.json'));
        $apiResponse = json_decode($apiResponse, true);

        if (!empty($apiResponse) && is_array($apiResponse))
            return array_map(array($this, 'filterResponse'), $apiResponse);

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
        // We are only interested on this types
        if (!in_array($value['type'], array('PushEvent', 'CreateEvent', 'GistEvent')))
            return ;

        switch ($value['type'])
        {
            case 'CreateEvent':
            case 'PushEvent':

                // Github registers CreateEvents twice! The first one is done when you create the repo via webbrowser
                // and the second one when you actually push your first push.
                // To avoid double-posting we just choose the second one.
                if (empty($value['payload']['ref']))
                    return ;

                $html = sprintf($this->translation[$value['type']], $value['repository']['url'], $value['repository']['name']);
                break;

            case 'GistEvent':
                $html = sprintf($this->translation[$value['payload']['action'] . 'Gist'], $value['payload']['url'], $value['payload']['name']);
                break;
        }

        return array('service' => 'github',
                     'date' => strtotime($value['created_at']),
                     'html' => $html);
    }
}
?>