<?php
/**
 * @package   orcid-php-client
 * @author    Sam Wilson <samwilson@purdue.edu>
   modified by Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Http;

/**
 * Curl http transport class
 **/
class Curl
{
    /**
     * The connection resource
     * @var  resource
     **/
    private $resource = null;
    /**
     * @var array
     */
    private $response_infos=[];
    /**
     * @var int
     */
    private $response_code=0;
    /**
     * @var mixed
     */
    private $errno;
    /**
     * @var mixed
     */
    private $error;
    /**
     * @var mixed
     */
    private $response;
    /**
     * Constructs a new instance
     *
     * @return  void
     **/
    public function __construct($withHeader=false)
    {
        $this->initialize($withHeader);
    }

    /**
     * Initializes the resource
     *
     * @param bool $withHeader
     * @return  $this
     */
    public function initialize($withHeader=false)
    {
        $this->resource = curl_init();
        if ($withHeader) {
            $this->setHTTPRequestHeader();
        }
        $this->setReturnTransfer();

        return $this;
    }


    /**
     * Sets a generic option on the curl resource
     *
     * @param   int    $opt    The curl option to set
     * @param   mixed  $value  The curl option value to use
     * @return  $this
     **/
    public function setOpt($opt, $value)
    {
        curl_setopt($this->resource, $opt, $value);

        return $this;
    }

    /**
     * @param array $curlOptions
     * @return $this
     */
    public function setOptions(array $curlOptions)
    {
        foreach ($curlOptions as $opt=>$value) {
            curl_setopt($this->resource, $opt, $value);
        }
        return $this;
    }

    /**
     * Returns string response
     *
     * @return  $this
     **/
    public function setReturnTransfer()
    {
        return $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * Sets the url endpoint
     *
     * @param string $url the url endpoint to set
     * @return  $this
     */
    public function setUrl(string $url)
    {
        return $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     * Sets the post fields (and implicitly implies a post request)
     *
     * @param array $fields the post fields to set on the request
     * @return  $this
     */
    public function setPostFields(array $fields)
    {
        // Form raw string version of fields
        $raw   = '';
        $first = true;
        foreach ($fields as $key => $value) {
            if (!$first) {
                $raw .= '&';
            }

            $raw .= $key . '=' . $value;

            $first = false;
        }

        $this->setOpt(CURLOPT_POST, count($fields));
        $this->setOpt(CURLOPT_POSTFIELDS, $raw);

        return $this;
    }

    /**
     * @param $data
     * @return Curl
     */
    public function setPostData($data)
    {
        return $this->setOpt(CURLOPT_POSTFIELDS, $data);
    }

    /**
     * @return $this
     */
    public function setPut()
    {
        return $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
    }

    /**
     * @return $this
     */
    public function setDelete()
    {
        return $this->setOpt(CURLOPT_CUSTOMREQUEST, "DELETE");
    }

    /**
     * Sets a header on the request
     *
     * @param   string|array  $header  the header to set
     * @return  $this
     **/
    public function setHeader($header)
    {
        $headers = [];

        if (is_array($header)) {
            foreach ($header as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
        } else {
            $headers[] = $header;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    public function setHTTPRequestHeader()
    {
        $this->setOpt(CURLOPT_HEADER, true);
    }
    /**
     * Executes the request
     * @return  string
     **/
    public function execute($close=true)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = curl_exec($this->resource);
        $this->setResponse($response);
        $this->initialiseResponseData();
        if ($close) {
            $this->close();
        }
        return $response;
    }

    public function initialiseResponseData()
    {
        $this -> response_code=curl_getinfo($this->resource, CURLINFO_HTTP_CODE);
        $this -> response_infos=curl_getinfo($this->resource);
        $this -> errno = curl_errno($this->resource);
        $this -> error  =curl_error($this->resource);
    }

    /**
     * @param mixed $response
     */
    protected function setResponse($response)
    {
        $this -> response = $response;
    }

    /**
     * @return array
     */
    public function getResponseInfos()
    {
        return $this->response_infos;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }


    /**
     * Resets the curl resource to be used again
     *
     * @param bool $withHeader
     * @return  $this
     */
    public function reset($withHeader=false)
    {
        $this->close()
            ->initialize($withHeader);

        return $this;
    }

    /**
     * Shuts down the resource
     *
     * @return  $this
     **/
    public function close()
    {
        curl_close($this->resource);

        return $this;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getErrno()
    {
        return $this -> errno;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this -> error;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this -> response;
    }
}
