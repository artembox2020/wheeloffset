<?php

include_once(dirname(__FILE__) . '/../components/phpQuery.php');

class BulbCommand extends CConsoleCommand
{
    public static $state = [];
    private $cliColor;

    public function init()
    {
        $this->cliColor = new CliColors();
        ini_set('max_execution_time', 3600 * 12);
        date_default_timezone_set('America/Los_Angeles');

        return parent::init();
    }

    public function actionSeoText()
    {
        $file = Yii::getPathOfAlias('webroot') . '/../bulb_model_year.txt';
        $items = file($file);
        foreach ($items as $text) {
            $model = new SeoTextSource;
            $model->text = $text;
            $model->page = 'bulb_model_year_header';
            $model->save();

            echo $model->id . "\n";
        }
    }

    public function actionBulb()
    {
        $file = 'log.txt';
        //@unlink($file);

        $end = false;
        $limit = 1000;
        $page = 0;
        $countNotFounded = 0;
        while (!$end) {
            $offset = $page * $limit;
            $sql = "SELECT * FROM donor_product_bulb LIMIT {$offset}, {$limit}";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($rows as $row) {
                $position = ProductBulbPosition::getItem(['title' => $row['app'], 'type' => 0]);
                if ($row['part'] === 'NEON') {
                    $row['part'] = 'LED';
                }
                
                $attrs = [
                    'part' => ProductBulb::resolvePartName($row['part']),
                ];
                $bulb = ProductBulb::model()->findByAttributes($attrs);
                if (empty($bulb)) {
                    $bulb = new ProductBulb;
                    $bulb->attributes = $attrs;
                    if (!$bulb->save()) {
                        print_r($bulb->errors);
                    }
                    //die();
                }

                echo $bulb->id . "\n";

                if (!array_key_exists($row['make'], self::$state)) {
                    $make = AutoMake::model()->findByAttributes(['title' => $row['make']]);
                    if (empty($make)) {
                        file_put_contents($file, 'make: ' . $row['make'] . "\n", FILE_APPEND);
                        self::$state[$row['make']] = null;
                    } else {
                        self::$state[$row['make']]['id'] = $make->id;
                    }
                }

                if (empty(self::$state[$row['make']])) {
                    continue;
                }

                if (empty(self::$state[$row['make']]['items']) ||
                    !array_key_exists($row['model'], self::$state[$row['make']]['items'])
                ) {
                    $model = AutoModel::model()->findByAttributes([
                        'make_id' => self::$state[$row['make']]['id'],
                        'title' => $row['model'],
                    ]);

                    if (empty($model)) {
                        file_put_contents($file, 'make: ' . $row['make'] . ' - model: ' . $row['model'] . "\n", FILE_APPEND);
                        self::$state[$row['make']]['items'][$row['model']] = null;
                    } else {
                        self::$state[$row['make']]['items'][$row['model']] = $model->id;
                    }
                }

                if (empty(self::$state[$row['make']]['items'][$row['model']])) {
                    continue;
                }

                $criteria = new CDbCriteria;
                $criteria->with = ['Model', 'Model.Make'];
                $criteria->compare('t.year', $row['year']);
                $criteria->compare('LOWER(Model.title)', $row['model']);
                $criteria->compare('LOWER(Make.title)', $row['make']);
                $modelYear = AutoModelYear::model()->find($criteria);

                if (!empty($modelYear)) {
                    $res = (int) Yii::app()->db->createCommand("
                        SELECT 
                            COUNT(*) 
                        FROM auto_model_year_bulb 
                        WHERE 
                            model_year_id = {$modelYear->id} AND 
                            bulb_id = {$bulb->id} AND
                            position_id = {$position->id}    
                     ")->queryScalar();
                    if (!$res) {
                        $sql = "INSERT INTO auto_model_year_bulb (model_year_id, bulb_id, position_id) VALUES ({$modelYear->id}, {$bulb->id}, {$position->id})";
                        Yii::app()->db->createCommand($sql)->execute();
                    }
                } else {
                    $countNotFounded++;
                }
            }

            if (empty($rows)) {
                $end = true;
            }
            $page++;
        }
        echo $countNotFounded;
    }
    
    public function actionImport()
    {
        $criteria = new CDbCriteria();
       
        //$year = 2020;
		$fileLog = '../bulb_2020.log';
        
        /*
        $content = file_get_contents($fileLog);
        $expl = explode('****', $content);
        $content = end($expl);
        preg_match_all("/\[(.*?)]/", $content, $match);
        
		$modelYearIds = [];
        foreach ($match[1] as $id) {
            $modelYearIds[] = $id;
        }
        print_r($modelYearIds);
        if (!empty($modelYearIds)) {
            $criteria->addInCondition('t.id', $modelYearIds);
        }
        */
        
        $rows = Yii::app()->db->createCommand("SELECT DISTINCT model_year_id AS model_year_id FROM auto_model_year_bulb")->queryAll();
        foreach ($rows as $row) {
            $modelYearIds[] = $row['model_year_id'];
        }
        if (!empty($modelYearIds)) {
            $criteria->addNotInCondition('t.id', $modelYearIds);
        }

        $criteria->with = ['Model', 'Model.Make'];
        $criteria->order = 'year DESC';
        
        $modelYears = AutoModelYear::model()->findAll($criteria);
        
        foreach ($modelYears as $modelYear) {
            $modelTitle = !empty($modelYear->Model->title_sa) ? $modelYear->Model->title_sa : $modelYear->Model->title;
            
            $url = sprintf('https://www.sylvania-automotive.com/apps/vlrg-us/Vlrg/getPositions/%s/%s/%s/', 
                $modelYear->year, 
                trim($modelYear->Model->Make->title), 
                trim($modelTitle)
            );
            
            echo $url . "\n";
            
            $content = CUrlHelper::getPage($url);
            
            sleep(3);
            
            $document = phpQuery::newDocumentHTML($content);
            $elem = $document->find('a.picto-left:first');
            
            $expl = explode('/', trim(pq($elem)->attr('data-action'), '/'));
            $vID = $expl[count($expl)-1];

            echo "\t {$vID} \n";
            
            if (!empty($vID)) {
                foreach (ProductBulbPosition::getTypes() as $posTypeId => $posTypeTitle) {
                    $typeUrl = 'https://www.sylvania-automotive.com/apps/vlrg-us/Vlrg/getBulbTable/' . $posTypeTitle . '/' . $vID . '/';
                    echo "\t {$typeUrl} \n";
                    $contentType = CUrlHelper::getPage($typeUrl);

                    $documentType = phpQuery::newDocumentHTML($contentType);
                    $items = $documentType->find('table tbody tr');                

                    foreach ($items as $item) {
                        $position = ProductBulbPosition::getItem([
                            'title' => trim(pq($item)->find('th')->text()),
                            'type' => $posTypeId
                        ]);

                        $bulb = ProductBulb::getItem([
                            'part' => ProductBulb::resolvePartName(trim(pq($item)->find('td a.icon-link-b')->text())),
                        ]);

                        $res = (int) Yii::app()->db->createCommand("
                             SELECT 
                                 COUNT(*) 
                             FROM auto_model_year_bulb 
                             WHERE 
                                 model_year_id = {$modelYear->id} AND 
                                 bulb_id = {$bulb->id} AND
                                 position_id = {$position->id}    
                          ")->queryScalar();
                         if (!$res) {
                             $sql = "INSERT INTO auto_model_year_bulb (model_year_id, bulb_id, position_id) VALUES ({$modelYear->id}, {$bulb->id}, {$position->id})";
                             Yii::app()->db->createCommand($sql)->execute();
                        }
                    }

					sleep(2);
                }
            } else {
                echo $content . "\n";
                sleep(3);
                $logLine = sprintf("[%s] %s \n", $modelYear->id, $url);
                file_put_contents($fileLog, $logLine, FILE_APPEND);
            }
            
            sleep(3);
        }
        
        file_put_contents($fileLog, "\n\n **** \n\n", FILE_APPEND);
    }

}