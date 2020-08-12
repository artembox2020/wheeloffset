<?php

class EbayCompatibilityCommand extends CConsoleCommand
{
    private $cliColor;

    public function init()
    {
        $this->cliColor = new CliColors();
        ini_set('max_execution_time', 3600 * 12);
        date_default_timezone_set('America/Los_Angeles');

        return parent::init();
    }
    
    public function actionTest()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'notes IS NULL';
        $criteria->compare('is_compatibility', 1);
        
        $limit = 100;
        $count = Product::model()->count($criteria);
        
        for ($offset = 0; $offset < $count; $offset+=$limit) {
            $criteria = new CDbCriteria;
            $criteria->condition = 'notes IS NULL';
            $criteria->compare('is_compatibility', 1);
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $products = Product::model()->findAll($criteria);
            
            foreach ($products as $product) {
                (new ProductService)->syncDetail($product);
                echo $product->id . "\n";
            }
        }    
    }    
    
    public function actionIndex(int $brand_id)
    {
        Yii::app()->db->createCommand("REPLACE INTO product_compatibility SELECT * FROM product_compatibility_temp")->execute();
        Yii::app()->db->createCommand("DELETE FROM product_compatibility_temp")->execute();
        
        $limit = 100;
        $count = Product::model()->findByAttributes(['is_compatibility' => 0])->count();
        
        for ($offset = 0; $offset < $count; $offset+=$limit) {
            
            $criteria = new CDbCriteria;
            $criteria->compare('is_compatibility', 0);
            $criteria->compare('brand_id', $brand_id); //TODO
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $products = Product::model()->findAll($criteria);
            
            foreach ($products as $product) {
                echo "product #$product->id \n";
                $compatibilitiesResponse = (new ApiEbay(['format' => 'XML']))->getProductCompatibilities($product->epid);
                                
                if (empty($compatibilitiesResponse['paginationOutput'])) {
                    $product->is_universal = 1;
                    $product->is_compatibility = 1;
                    $product->save();
                    
                    //$file = dirname(__FILE__) . "/../../data/product_{$product->id}.json";
                    //file_put_contents($file, json_encode($compatibilitiesResponse));
                    echo "\t" . $this->cliColor->getColoredString('Empty data', 'white', 'red') . "\n";
                    continue;
                }
                
                
                
                $pagination = $compatibilitiesResponse['paginationOutput'];
                
                $this->setProductCompatibilities($product, $compatibilitiesResponse['compatibilityDetails']);
                
                $totalPages = $pagination->totalPages;
                $totalEntries = $pagination->totalEntries;
                
                echo "\t totalPages: " . $totalPages . " \n";
                echo "\t totalEntries: " . $totalEntries . " \n";
                echo "\t\t page: 1 \n";
                
                if ($totalPages > 1) {
                    
                    for ($apiPage=2; $apiPage <= $totalPages; $apiPage++) {
                        
                        echo "\t\t page: $apiPage \n";
                        
                        $compatibilitiesResponse = (new ApiEbay(['format' => 'XML']))->getProductCompatibilities($product->epid, [
                            'page' => $apiPage,
                        ]);
                        $this->setProductCompatibilities($product, $compatibilitiesResponse['compatibilityDetails']);
                    }
                }
                $product->is_compatibility = 1;
                $product->save();
            }
        }
    }
    
    private function setProductCompatibilities($product, $items) 
    {
        (new ProductService)->syncDetail($product);
        
        foreach ($items as $item) {
            
            $info = [];
			
            foreach ($item->productDetails as $det) {
                $info[(string)$det->propertyName] = str_replace("'", "\\'", (string)$det->value->text->value);
            }
            
			if (empty($info['Year']) || 
				empty($info['Model']) ||
				empty($info['Make']) ||
				empty($info['Trim']) ||
				empty($info['Engine'])
			) {
				continue;
			}
			
            $year = (int) $info['Year'];
            
            $sql = "SELECT 
                    t.id AS id 
                FROM ebay_auto_trim AS t
                LEFT JOIN ebay_auto_trim_engine AS e ON t.engine_id = e.id 
                LEFT JOIN ebay_auto_model_year AS y ON t.model_year_id = y.id 
                LEFT JOIN ebay_auto_model AS m ON y.model_id = m.id 
                LEFT JOIN ebay_auto_make AS k ON m.make_id = k.id 
                WHERE 
                    k.title = '" . trim($info['Make']) . "' AND 
                    m.title = '" . trim($info['Model']) . "' AND 
                    y.year = {$year} AND 
                    t.title = '" . trim($info['Trim']) . "' AND 
                    e.title = '" . trim($info['Engine']) . "'";
            
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            $rows = [];
            foreach ($items as $item) {
                $rows[] = "({$product->id}, {$item['id']})";
            }
            if (!empty($rows)) {
                $sql = "REPLACE INTO product_compatibility_temp (product_id, trim_id) VALUES " . implode(', ', $rows) . ";"; 
                Yii::app()->db->createCommand($sql)->execute();
            }
        }
    }
}