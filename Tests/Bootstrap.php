<?php
/**
 * Bootstrap.php
 * The test bootstrap file
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

date_default_timezone_set('America/Bogota');
require __DIR__ . '/../vendor/autoload.php';

/**
 * Checks that service provider returns consistent data
 *
 * @param array $result
 * @param array $types
 * @return bool
 *
 * @throws InvalidArgumentException when an inconsistency is found.
 * @codeCoverageIgnore
 */
function checkServiceKeys(array $result, array $types)
{
    $services = array('facebookpages',
                      'feed',
                      'github',
                      'reddit',
                      'stackoverflow',
                      'twitter',
                      'youtube');

    foreach ($result as $r)
    {
        if (!in_array($r['service'], $services))
            throw new InvalidArgumentException('Unknown Service ' . $r['service']);

        if (!in_array($r['type'], $types))
            throw new InvalidArgumentException('Invalid type given ' . $r['type']);

        if (empty($r['resource']))
            throw new InvalidArgumentException('The resource key shouldnt be empty');

        if (!is_numeric($r['stamp']) || empty($r['stamp']) || strlen($r['stamp']) < 10)
            throw new InvalidArgumentException('The stamp seems to be invalid ' . $r['stamp']);

        if (empty($r['text']))
            throw new InvalidArgumentException('The text key shouldnt be empty');

        $url = parse_url($r['url']);
        if (!$url || empty($url['host']))
            throw new InvalidArgumentException('The url ' . $r['url'] . ' seems to be invalid');
    }

    return true;
}

/**
 * A class used to test the FB Pages Service Provider
 *
 * @codeCoverageIgnore
 */
class FacebookPagesMock extends \SimpleLifestream\Services\FacebookPages { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the Feed Service Provider
 *
 * @codeCoverageIgnore
 */
class FeedMock extends \SimpleLifestream\Services\Feed { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the Github Service Provider
 *
 * @codeCoverageIgnore
 */
class GithubMock extends \SimpleLifestream\Services\Github { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the Reddit Service Provider
 *
 * @codeCoverageIgnore
 */
class RedditMock extends \SimpleLifestream\Services\Reddit { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the StackOverflow Service Provider
 *
 * @codeCoverageIgnore
 */
class StackOverflowMock extends \SimpleLifestream\Services\StackOverflow { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the Twitter Service Provider
 *
 * @codeCoverageIgnore
 */
class TwitterMock extends \SimpleLifestream\Services\Twitter { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the Youtube Service Provider
 *
 * @codeCoverageIgnore
 */
class YoutubeMock extends \SimpleLifestream\Services\Youtube { public $reply; protected function fetch($url, array $headers = array(), array $options = array()) { return $this->reply; } }

/**
 * A class used to test the main SimpleLifestream merging capabilities
 *
 * @codeCoverageIgnore
 */
class SimpleLifestreamMock extends \SimpleLifestream\SimpleLifestream { public $services = array(); }
?>
