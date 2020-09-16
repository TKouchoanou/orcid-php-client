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
     *
     * @var  object
     **/
    private $resource = null;
    /**
     * @var string
     */
    private $response_infos=null;
    /**
     * @var int
     */
    private $response_code=0;
    /**
     * Constructs a new instance
     *
     * @return  void
     **/
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initializes the resource
     *
     * @return  $this
     **/
    public function initialize($withHeader=false)
    {
        $this->resource = curl_init();
        if($withHeader){
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
     * @param   string  $url  the url endpoint to set
     * @return  $this
     **/
    public function setUrl($url)
    {
        return $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     * Sets the post fields (and implicitly implies a post request)
     *
     * @param   array  $fields  the post fields to set on the request
     * @return  $this
     **/
    public function setPostFields($fields)
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
     */
    public function setPostData($data)
    {
        return $this->setOpt(CURLOPT_POSTFIELDS, $data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setPut()
    {
        return $this->setOpt( CURLOPT_CUSTOMREQUEST, 'PUT');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setDelete()
    {
        return $this->setOpt( CURLOPT_CUSTOMREQUEST, "DELETE");
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

    function setHTTPRequestHeader(){
        $this->setOpt(CURLOPT_HEADER, true);
    }
    /**
     * Executes the request
     *
     * @return  array
     **/
    public function execute()
    {
        $response = curl_exec($this->resource);
        $this->initialiseResponseData();
        $this->reset();
        return $response;
    }

    public function initialiseResponseData(){
        $this->response_code=curl_getinfo($this->resource,CURLINFO_HTTP_CODE);
        $this->response_infos=curl_getinfo($this->resource);
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
    public function getResponseCode(): int
    {
        return $this->response_code;
    }

    public function getInfos(){
        return $this->response_infos;
    }

    /**
     * Resets the curl resource to be used again
     *
     * @return  $this
     **/
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
     * @return object
     */
    public function getResource()
    {
        return $this->resource;
    }
}
