<?php
/**
 * Adapter.php
 * Every service should extend this class.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Core;

abstract class Adapter
{
    protected $resource;
    protected $http;

    /**
     * Gets the data API response and returns an array
     * with all the information.
     *
     * @return array
     */
    abstract public function getApiData();

    /**
     * Instantiates the service
     *
     * @param object An Object with http capabilities and respects the IHttpRequest Interface
     * @return void
     */
    public function __construct(\SimpleLifestream\Interfaces\IHttpRequest &$http)
    {
        $this->http = $http;
    }

    /**
     * Sets the Default Language
     *
     * @param string $resource
     * @return void
     */
    public function setResource($resource) { $this->resource = $resource; }

    /**
     * Gets an index and returns a translated string
     *
     * @return string
     */
    protected function translate()
    {
        if (func_num_args() < 1)
            return 'Undefined Lang Index';

        $index = func_get_arg(0);
        if (isset($this->translation[$this->defaultLang][$index]))
        {
            if (func_num_args() > 1)
            {
                $params = func_get_args();
                array_shift($params);
                return vsprintf($this->translation[$this->defaultLang][$index], $params);
            }
            else
                return $this->translation[$this->defaultLang][$index];
        }

        return $index;
    }
}
?>
