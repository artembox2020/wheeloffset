<?php

class CompletionCommand extends CConsoleCommand
{
    public function init()
    {
        ini_set('max_execution_time', 3600 * 12);
        date_default_timezone_set('America/Los_Angeles');

        return parent::init();
    }

    public function actionHp()
    {
        $this->moveHp(2018);
        $this->moveHp(2019);
        $this->moveHp(2020);
    }
    
    private function moveHp($year)
    {
        $limit = 1000;
        $offset = 0;
        
        $hasData = true;
        while ($hasData) {
            $sql = "SELECT 
                        c.*,
                        y.model_id AS model_id
                    FROM `auto_completion` AS c
                    LEFT JOIN auto_model_year AS y ON c.model_year_id = y.id
                    WHERE y.`year` = {$year} AND (
                        c.specs_0_60mph__0_100kmh_s_ IS NULL
                            OR
                        c.specs_1_4_mile_time IS NULL
                            OR
                        c.specs_1_4_mile_speed IS NULL) AND 
                        c.specs_horsepower IS NOT NULL AND 
                        c.specs_curb_weight IS NOT NULL 
                    LIMIT {$offset}, {$limit}";
                
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            if (!empty($items)) {
                $oldYear = $year - 1;
                foreach ($items as $item) {
                    $sqlYearModel = "SELECT * FROM auto_model_year WHERE `year`={$oldYear} AND model_id = " . $item['model_id']; 
                    $oldYearModel = Yii::app()->db->createCommand($sqlYearModel)->queryRow();
                    if (!empty($oldYearModel)) {
                        
                        $sqlItem = "SELECT 
                                specs_0_60mph__0_100kmh_s_,
                                specs_1_4_mile_time,
                                specs_1_4_mile_speed
                            FROM `auto_completion`
                            WHERE model_year_id = {$oldYearModel['id']} 
                              AND title = '{$item['title']}'  
                              AND specs_horsepower = '{$item['specs_horsepower']}'  
                              AND specs_curb_weight = '{$item['specs_curb_weight']}'  
                            ";
                              
                        $oldItem = Yii::app()->db->createCommand($sqlItem)->queryRow();
                        if (!empty($oldItem)) {
                            $updateAttrs = [];
                                
                            foreach (['specs_0_60mph__0_100kmh_s_', 'specs_1_4_mile_time', 'specs_1_4_mile_speed'] as $attr) {
                                if (empty($completion->{$attr}) && !empty($oldItem[$attr])) {
                                    $updateAttrs[] = "`{$attr}`='" . $oldItem[$attr] . "'";
                                }
                            }
                                
                            if (!empty($updateAttrs)) {
                                $updateAttrs = implode(', ', $updateAttrs);
                                $updateSql = "UPDATE `auto_completion` SET {$updateAttrs} WHERE `id` = {$item['id']}";
                                Yii::app()->db->createCommand($updateSql)->execute();
                                echo $updateSql . "\n\n";
                                
                            }
                        } else {
                            echo "oldItem empty \n";
                        }    
                    } else {
                        echo $sqlYearModel . "\n";
                        echo "oldYearModel empty \n";
                    }
               }
                
            } else {
                $hasData = false;
            }
            
            $offset += $limit;    
        }        
    }
    
    public function actionRpm()
    {
        $this->moveRpm('specs_horsepower');
        $this->moveRpm('specs_torque');
        //SELECT id, specs_torque, specs_torque_rpm FROM `auto_completion` WHERE specs_torque LIKE '%@%'
    }
    
    private function moveRpm($attr)
    {
        $rpmAttr = $attr . '_rpm';
        $limit = 10;
        $offset = 0;
        
        $hasData = true;
        while ($hasData) {
            $sql = "SELECT 
                       *
                    FROM `auto_completion`
                    WHERE `{$attr}` LIKE '%@%'
                    LIMIT {$offset}, {$limit}";
                
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            if (!empty($items)) {
                foreach ($items as $item) {
                    
                    $parts = explode('@', $item[$attr]);
                    $attrVal = trim($parts[0]);
                    $attrRpmVal = trim(str_replace(['rpm', ','], '', $parts[1]));
                    
                    $updateAttrs = [];
                    $updateAttrs[] = "`{$attr}` = '{$attrVal}'";
                    
                    if (empty($item[$rpmAttr])) {
                        $updateAttrs[] = "`{$rpmAttr}` = '{$attrRpmVal}'";
                    } else {
                        var_dump($item[$attr]);
                        var_dump($item[$rpmAttr]);
                        //die();
                    }
                    
                    if (!empty($updateAttrs)) {
                        $updateAttrs = implode(', ', $updateAttrs);
                        $updateSql = "UPDATE `auto_completion` SET {$updateAttrs} WHERE `id` = {$item['id']}";
                        Yii::app()->db->createCommand($updateSql)->execute();
                        echo "{$item['id']} / {$item['title']} \n";
                        echo $updateSql . "\n\n";
                    }
                    
                }
                
            } else {
                $hasData = false;
            }
            
            $offset += $limit;           
        }
    }
    
    
    public function actionSs()
    {
        $this->setSpecsSelect('transmission');
    }
    
    private function setSpecsSelect($alias)
    {
        $specAlias = "specs_{$alias}";
        Yii::app()->db->createCommand("UPDATE auto_completion SET {$specAlias} = NULL");
        
        $criteria = new CDbCriteria;
        $criteria->compare('alias', $alias);
        $spec = AutoSpecs::model()->find($criteria);
        
        $criteriaOpt = new CDbCriteria;
        $criteria->compare('specs_id', $spec->id);
        
        $optionsMap = CHtml::listData(AutoSpecsOption::model()->findAll($criteriaOpt), 'value', 'id');
        
        $limit = 100;
        $offset = 0;
        
        $hasData = true;
        while ($hasData) {
            $sql = "SELECT * FROM `auto_completion` LIMIT {$offset}, {$limit}";
                
            $items = Yii::app()->db->createCommand($sql)->queryAll();
            
            if (!empty($items)) {
                foreach ($items as $item) {
                    $sqlTV = "SELECT value FROM auto_completion_specs_temp WHERE completion_id = {$item['id']} AND specs_id = {$spec->id}";
                    $tempValue = Yii::app()->db->createCommand($sqlTV)->queryScalar();
                    if (!empty($tempValue)) {
                        $option_id = !empty($optionsMap[$tempValue]) ? $optionsMap[$tempValue] : null;
                        
                        if ($option_id !== null) {
                            $sqlUpdate = "UPDATE auto_completion SET {$specAlias} = $option_id WHERE id = {$item['id']}";
                            Yii::app()->db->createCommand($sqlUpdate)->execute();
                            echo $sqlUpdate . "\n";
                        } else {
                            echo " empty option: {$tempValue} \n";
                        }
                    } 
                }
                
            } else {
                $hasData = false;
            }
            
            $offset += $limit;           
        }
    }
    
}