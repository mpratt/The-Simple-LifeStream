<?php
/**
 * StackOverflowService.php
 * A service for Stack Overflow
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class StackOverflowService extends SimpleLifestreamAdapter
{
    protected $translation = array('answer'   => 'contestó la pregunta - "<a href="http://stackoverflow.com/questions/%s">%s</a>".',
                                   'question' => 'publicó la pregunta "<a href="http://stackoverflow.com/questions/%s">%s</a>".',
                                   'badge'    => 'se ganó la medalla "<a href="http://stackoverflow.com/users/%s?tab=reputation">%s</a>" (%s).',
                                   'comment'  => 'comentó en "<a href="http://stackoverflow.com/questions/%s#%s">%s</a>".');

    /**
     * Gets the data of the user and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $apiResponse = json_decode($this->fetchUrl('http://api.stackoverflow.com/1.0/users/' . $this->config['username'] . '/timeline'), true);

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
        // We are only interested on this types
        if (!in_array($value['timeline_type'], array('askoranswered', 'badge', 'comment')))
            return ;

        $html = 'Unknown Action';
        if ($value['timeline_type'] == 'askoranswered')
        {
            if ($value['post_type'] == 'answer')
                $html = sprintf($this->translation['answer'], $value['post_id'], $value['description']);
            else
                $html = sprintf($this->translation['question'], $value['post_id'], $value['description']);
        }
        else if ($value['timeline_type'] == 'badge')
            $html = sprintf($this->translation['badge'], $value['user_id'], $value['description'], $value['detail']);
        else if ($value['timeline_type'] == 'comment')
            $html = sprintf($this->translation['comment'], $value['post_id'], $value['comment_id'], $value['description']);

        return array('service' => 'stackoverflow',
                     'date' => $value['creation_date'],
                     'html' => $html);
    }
}
?>