<?php
/**
 * ServiceAdapter.php
 *
 * @package SimpleLifestream
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream;

/**
 * This class provides a minor logic for every service and
 * it acts as a "Interface" for them.
 * Every service should extend this class.
 *
 * @abstract
 */
abstract class ServiceAdapter
{
    /** @var string This property refers as the contextual username/url/userid for the current service */
    protected $resource;

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     * @throws Exception When an error happened, no matter what kind.
     */
    abstract public function getApiData();

    /**
     * Constructor
     *
     * @param object $http Instance of \SimpleLifestream\Interfaces\IHttp
     * @param mixed $resource Mostly the username of url of a given service.
     * @return void
     */
    public function __construct(\SimpleLifestream\Interfaces\IHttp $http, $resource)
    {
        $this->http = $http;
        $this->resource = $resource;
    }
}

?>
