<?php

class ApiEbay
{
    const APP_ID = 'RomanCho-RomanCho-PRD-2ca83364c-a3d61ddc';
    const URL = 'https://svcs.ebay.com/services/marketplacecatalog/ProductService/v1';

    private $format = 'JSON';
    private $version = '1.3.0';
    private $globalId = 'EBAY-MOTOR';
    
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
        
    /*
     * @param array $addtParams
     * @return string
     */    
    public function getUrl(array $addtParams)
    {
        $params = [
            'SERVICE-VERSION' => $this->version,
            'SECURITY-APPNAME' => self::APP_ID,
            'GLOBAL-ID' => $this->globalId,
            'RESPONSE-DATA-FORMAT' => $this->format,
        ];
        
        foreach ($addtParams as $key => $val) {
            $params[$key] = $val;
        }

        return self::URL . '?' . http_build_query($params);
    }
    
    /*
     * @param string $url
     * @return array
     * @throw Exception
     */
    public function getResponse(string $url): array
    {
        echo "\t $url \n";
        
        $content = CUrlHelper::getPage($url);
        
        if ($this->format === 'JSON') {
            $response = json_decode($content, true);
		
            if (array_key_exists('errorMessage', $response)) {
                throw new Exception($response['errorMessage'][0]['error'][0]['message']);
            }
            
            return $response;
        } else if ($this->format === 'XML') {
            $xml = (array)simplexml_load_string($content);
            if (!empty($xml['error']['0'])) {
                throw new Exception($xml['error'][0]->message);
            }
            return $xml;
        }
        
        
    }

    /*
     * @param string $ePID
     * @param array $params
     * @return array
     * @throw Exception
     */
    public function getProductCompatibilities(string $ePID, array $params = []): array
    {
        $url = $this->getUrl([
            'OPERATION-NAME' => 'getProductCompatibilities',
            'paginationInput.entriesPerPage' => !empty($params['perPage']) ? $params['perPage'] : 100,
            'paginationInput.pageNumber' => !empty($params['page']) ? $params['page'] : 1,
            'productIdentifier.ePID' => $ePID,
        ]);
        
        return $this->getResponse($url);
    }
    
    /*
     * @param string $ePID
     * @param array $params
     * @return array
     * @throw Exception
     */
    public function getProductDetails(string $ePID): array
    {
        $url = $this->getUrl([
            'OPERATION-NAME' => 'getProductDetails',
            'productDetailsRequest.productIdentifier.ePID' => $ePID,
        ]);
        
        $data = $this->getResponse($url);
        
        return !empty($data['getProductDetailsResponse'][0]['product']) 
            ? $data['getProductDetailsResponse'][0]['product']
            : [];
    }
}    
