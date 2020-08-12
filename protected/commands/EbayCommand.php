<?php

class EbayCommand extends CConsoleCommand
{
    public static $storeMap = [
        'brand' => [],
        'type' => [],
        'subtype' => [],
    ];
    
    private $cliColor;

    public function init()
    {
        $this->cliColor = new CliColors();
        ini_set('max_execution_time', 3600 * 12);
        date_default_timezone_set('America/Los_Angeles');

        return parent::init();
    }

    public function actionIndex()
    {
        $fileLog = '../csc_product.log';
        $fileInc = '../csc_product_inc.log';

        $file = dirname(__FILE__) . '/../../data/US_Parts_Catalog20191003.csv';
        $lastIndex = is_file($fileInc) ? (int) file_get_contents($fileInc) : 0;
        
        if (($h = fopen($file, "r")) !== false) {
            $i = 0;
            $keys = [];
            while (($data = fgetcsv($h, 1000, ",")) !== false) {		
                $lineData = $data;
                
                echo $i . "\n";
                
                if ($i === 0) {
                    $keys = $data;
                } elseif ($i > $lastIndex && count($keys) === count($data)) {
                    echo "\t handle: \n";
                    
                    $data = array_map(function($value) {
                        return trim($value, '"');
                    }, $data);
                    
                    
                    $data = array_combine($keys, $data);
                    $productAttrs = [
                        'title' => $data['Title'],
                        'epid' => $data['ePID'],
                        'manufacture_part_number' => $data['ManufacturePartNumber'],
                        'brand_id' => $this->getProductBrandId($data['Brand']),
                        'ebay_category_ids' => [],
                        'type_ids' => [],
                        'subtype_ids' => [],
                    ];
                    
                    $types = trim($data['Type']);
                    if (!empty($types)) {
                        $types = explode('|', $types);
                        foreach ($types as $type) {
                            $productAttrs['type_ids'][] = $this->getProductTypeId($type);
                        }
                    }
                    $subTypes = trim($data['SubType']);
                    if (!empty($subTypes)) {
                        $subTypes = explode('|', $subTypes);
                        foreach ($subTypes as $subType) {
                            $productAttrs['subtype_ids'][] = $this->getProductSubTypeId($subType);
                        }
                    }
                    
                    $categoryIdParts = explode('|', $data['CategoryID']);
                    $categoryBreadcrumbParts = explode('|', $data['CategoryBreadcrumb']);
                    if (count($categoryIdParts) === count($categoryBreadcrumbParts)) {
                        foreach ($categoryBreadcrumbParts as $bi => $categoryBreadcrumb) {
                            $productAttrs['ebay_category_ids'][] = ProductEbayCategory::findOrCreateByBreadcrumbs(
                            $categoryBreadcrumb, $categoryIdParts[$bi])->id;
                        } 
                    }
                    if (empty($productAttrs['ebay_category_ids'])) {
                        $logLine = sprintf("%s \n", implode("\t", $lineData));
                        file_put_contents($fileLog, $logLine, FILE_APPEND);
                        continue;
                    }
                    
                    $product = $this->getProduct($productAttrs);
                    echo "\t product# $product->id \n";
                    
                    file_put_contents($fileInc, $i);
                }
                
                $i++; // > 20 && die();
            }
            fclose($h);
        }       
    }
    
    public function getProductBrandId($title)
    {
        if (empty(self::$storeMap['brand'][$title])) {
        
            $brand = ProductBrand::model()->findByAttributes(['title' => $title]);
            if (empty($brand)) {
                $brand = new ProductBrand;
                $brand->title = $title;
                $brand->is_active = 1;
                $brand->save(); 
            }
            self::$storeMap['brand'][$title] = $brand->id;
        }
        return self::$storeMap['brand'][$title];
    }
    
    public function getProductTypeId($title)
    {
        if (empty(self::$storeMap['type'][$title])) {
        
            $type = ProductType::model()->findByAttributes(['title' => $title]);
            if (empty($type)) {
                $type = new ProductType;
                $type->title = $title;
                $type->is_active = 1;
                $type->save(); 
            }
            self::$storeMap['type'][$title] = $type->id;
        }
        return self::$storeMap['type'][$title];
    }
    
    public function getProductSubTypeId($title)
    {
        if (empty(self::$storeMap['subtype'][$title])) {
        
            $subType = ProductSubType::model()->findByAttributes(['title' => $title]);
            if (empty($subType)) {
                $subType = new ProductSubType;
                $subType->title = $title;
                $subType->is_active = 1;
                $subType->save(); 
            }
            self::$storeMap['subtype'][$title] = $subType->id;
        }
        return self::$storeMap['subtype'][$title];
    }
    
    public function getProduct($attrs)
    {
        $product = Product::model()->findByAttributes([
            'title' => $attrs['title'],
            'brand_id' => $attrs['brand_id'],
            'manufacture_part_number' => $attrs['manufacture_part_number'],
        ]);
        
        if (empty($product)) {
            $product = new Product;
            $product->attributes = $attrs;
            $product->is_active = 1;
            $product->save(); 
        }
        return $product;
    }
    
}