<?php

class ProductService
{    
    private $ebayApi;
    
    public function __construct()
    {
        $this->ebayApi = new ApiEbay;
    }
    
    /*
     * @param Product $product
     */
    public function syncDetail(Product $product)
    { 
        $info = $this->ebayApi->getProductDetails($product->epid);
        $notes = [];
        foreach ($info[0]['productDetails'] as $item) {
            if ($item['propertyName'][0] === 'XML data') {
                foreach ($item['value'] as $value) {
                    $notes[] = $value['text'][0]['value'][0];
                }
            }
        }
        $product->notes = implode(', ', $notes);
        $product->save();
        
        if (!empty($info[0]['stockPhotoURL'])) {
            foreach ($info[0]['stockPhotoURL'] as $item) {
                $photo = new ProductPhoto;
                $photo->product_id = $product->id;
                $photo->image_url = str_replace('~~_7.', '~~_27.', $item['standard'][0]['value'][0]);
                $photo->save();
            }
        }
    }
}    
