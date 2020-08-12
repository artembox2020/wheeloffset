<?php

class EbayAutoCommand extends CConsoleCommand
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
        $fileInc = '../csc_auto_inc.log';

        $file = dirname(__FILE__) . '/../../data/MVL092519.csv';
        $lastIndex = is_file($fileInc) ? (int) file_get_contents($fileInc) : 0;
        
        if (($h = fopen($file, "r")) !== false) {
            $i = 0;
            $keys = [];
            while (($data = fgetcsv($h, 1000, ",")) !== false) {		
                $lineData = $data;
                echo $i . "\n";
                
                if ($i === 0) {
                    $keys = $data;
                } elseif (count($keys) === count($data)) {
                    echo "\t handle: \n";
                    
                    $data = array_map(function($value) {
                        return trim($value, '"');
                    }, $data);
                    
                    
                    $data = array_combine($keys, $data);
                    print_r($data);
                    
                    $make = $this->getMake($data['Make']);
                    $model = $this->getModel($make, $data['Model']);
                    $modelYear = $this->getModelYear($model, $data['Year']);
                    $driveTypeId = $this->getDriveTypeId($data['Drive Type']);
                    $bodyId = $this->getBodyId($data['Body']);
                    $engine = $this->getEngine([
                        'title' => $data['Engine'],
                        'block_type' => $data['Engine - Block Type'],
                        'cc' => $data['Engine - CC'], 
                        'cid' => $data['Engine - CID'], 
                        'cylinders' => $data['Engine - Cylinders'], 
                        'liter' => (float) $data['Engine - Liter_Display'],
                        'aspiration' => $data['Aspiration'],
                        'cylinder_type' => $data['Cylinder Type Name'],
                        'fuel_type' => $data['Fuel Type Name'],
                    ]);
                    
                    $trim = $this->getTrim([
                        'title' => str_replace("'", "\\'", $data['Trim']), 
                        'epid' => $data['ePID'], 
                        'body_id' => $bodyId, 
                        'drive_type_id' => $driveTypeId, 
                        'engine_id' => $engine->id, 
                        'model_year_id' => $modelYear->id, 
                        'num_doors' => $data['NumDoors'],
                        'post_regions' => !empty($data['Region']) ? explode('|', $data['Region']) : [],
                    ]);
                    
                    echo " \t TRIM: " . $trim->id . "\n";
                    
                    file_put_contents($fileInc, $i);
                }
                
                $i++;// > 100 && die();
            }
            fclose($h);
        }       
    }
    
    public function getDriveTypeId($title)
    {
        if (empty(self::$storeMap['driveType'][$title])) {
        
            $driveType = EbayAutoTrimDriveType::model()->findByAttributes(['title' => $title]);
            if (empty($driveType)) {
                $driveType = new EbayAutoTrimDriveType;
                $driveType->title = $title;
                $driveType->save(); 
            }
            self::$storeMap['driveType'][$title] = $driveType->id;
        }
        return self::$storeMap['driveType'][$title];
    }
    
    public function getMake($title)
    {
        $make = EbayAutoMake::model()->findByAttributes(['title' => $title]);
        if (empty($make)) {
            $atkMake = AutoMake::model()->findByAttributes(['title' => $title]);


            $make = new EbayAutoMake;
            $make->title = $title;
            $make->is_active = 1;
            if (!empty($atkMake)) {
                $make->atk_id = $atkMake->id;
            }    
            $make->save(); 
        }
        return $make;
    }

    public function getModel($make, $title)
    {
        $model = EbayAutoModel::model()->findByAttributes(['title' => $title, 'make_id' => $make->id]);
        if (empty($model)) {
            $atkModel = !empty($make->atk_id) 
                ? AutoModel::model()->findByAttributes(['title' => $title, 'make_id' => $make->atk_id])
                : null;


            $model = new EbayAutoModel;
            $model->title = $title;
            $model->make_id = $make->id;
            $model->is_active = 1;
            if (!empty($atkModel)) {
                $model->atk_id = $atkModel->id;
            }    
            $model->save(); 
        }
        return $model;
    }

    public function getModelYear($model, $year)
    {
        $modelYear = EbayAutoModelYear::model()->findByAttributes(['year' => $year, 'model_id' => $model->id]);
        if (empty($modelYear)) {
            $atkModelYear = !empty($model->atk_id) 
                ? AutoModelYear::model()->findByAttributes(['year' => $year, 'model_id' => $model->atk_id])
                : null;


            $modelYear = new EbayAutoModelYear;
            $modelYear->year = $year;
            $modelYear->model_id = $model->id;
            $modelYear->is_active = 1;
            if (!empty($atkModelYear)) {
                $modelYear->atk_id = $atkModelYear->id;
            }    
            $modelYear->save(); 
        }
        return $modelYear;
    }

    public function getBodyId($title)
    {
        if (empty(self::$storeMap['body'][$title])) {
        
            $body = EbayAutoTrimBody::model()->findByAttributes(['title' => $title]);
            if (empty($body)) {
                $body = new EbayAutoTrimBody;
                $body->title = $title;
                $body->save(); 
            }
            self::$storeMap['body'][$title] = $body->id;
        }
        return self::$storeMap['body'][$title];
    }
        
    public function getEngine($attrs)
    {
        $conditions = [];
        foreach ($attrs as $k => $v) {
            $conditions[] = empty($v) ? "{$k} IS NULL" : "{$k} = '{$v}'";
        }
        $criteria = new CDbCriteria;
        $criteria->condition = implode(' AND ', $conditions);
        
        $engine = EbayAutoTrimEngine::model()->find($criteria);
        
        if (empty($engine)) {
            $engine = new EbayAutoTrimEngine;
            $engine->attributes = $attrs;
            $engine->save(); 
        }
        return $engine;
    }
    
    public function getTrim($attrs)
    {
        $conditions = [];
        foreach ($attrs as $k => $v) {
            if (in_array($k, ['post_regions'])) {
                continue;
            }
            
            $conditions[] = empty($v) ? "{$k} IS NULL" : "{$k} = '{$v}'";
        }
        $criteria = new CDbCriteria;
        $criteria->condition = implode(' AND ', $conditions);
        
        $trim = EbayAutoTrim::model()->find($criteria);
        
        if (empty($trim)) {
            $trim = new EbayAutoTrim;
            $trim->attributes = $attrs;
            $trim->save();
        }
        return $trim;
    }
    
}