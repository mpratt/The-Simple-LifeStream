<?php
/**
 * ServiceAdapter.php
 * Every service should extend this class.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream;

abstract class ServiceAdapter
{
    protected $resource;

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     */
    abstract public function getApiData();

    /**
     * Constructor
     *
     * @param object $http Instance of \SimpleLifestream\Interfaces\IHttp
     * @param mixed $resource
     * @return void
     */
    public function __construct(\SimpleLifestream\Interfaces\IHttp $http, $resource)
    {
        $this->http = $http;
        $this->resource = $resource;
    }
}

?>
