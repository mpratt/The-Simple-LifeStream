<?php
/**
 * Autoload.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

date_default_timezone_set('UTC');

require __DIR__ . '/../Lib/SimpleLifestream/Autoload.php';
require_once __DIR__ . '/TestService.php';

/**
 * Testing Mocks
 */
class SimpleLifestreamMock extends \SimpleLifestream\SimpleLifestream { public $providers = array(); }
class MockHttp extends \SimpleLifestream\HttpRequest
{
    protected $reply;
    public function __construct($reply) { $this->reply = $reply; }
    public function fetch($url, array $params = array()) { unset($url); return $this->reply; }
}
?>
