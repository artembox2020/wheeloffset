<?php

/**
 * Класс SitemapCommand
 */
class SitemapCommand extends CConsoleCommand
{

    const SITE_URL = 'https://autotk.com';

    public function init()
    {
        ini_set('max_execution_time', 3600 * 12);
        date_default_timezone_set('America/Los_Angeles');
        return parent::init();
    }
    
    public function actionRun()
    {
        $limit = 50000; //(int)SiteConfig::getInstance()->getValue('count_items_in_file');	

        $mapFiles = array(
            '/',
            '/0-60-times.html',
            '/wheels.html',
            '/tires.html',
            '/horsepower.html',
            '/dimensions.html',
            '/tuning.html',
            '/bulbs.html',
            '/weight/',
            '/parts/',
        );

        $mapModules = array(
            '/',
            '/0-60-times/',
            '/tires/',
            '/horsepower/',
            '/dimensions/',
            '/tuning/',
        );

        //print_r(ProductCategory::getRootItemsWithChildren());
        //die();
        $partsLinks = [];
        foreach (ProductCategory::getRootItemsWithChildren() as $root) {
            $category = ProductCategory::getRootItemByAlias($root['alias']);
            $treeMap = ProductCategory::getTreeMap();
            
            $partsLinks[] = $root['url'];
            
            $file = "/sitemap/parts_{$root['alias']}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);            

            $rootLinks = [];
            if (!empty($root['children'])) {
                foreach ($root['children'] as $childItem) {
                    $rootLinks[] = $childItem['url'];
                    
                    $child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childItem['alias']);

                    // generate children xml files
                    $fileChild = "/sitemap/parts_{$root['alias']}_{$childItem['alias']}.xml";
                    $childLinks = [];
                    $docChild = new DOMDocument("1.0", 'utf-8');
                    $urlsetChild = $docChild->createElement("urlset");
                    $docChild->appendChild($urlsetChild);
                    $xmlns = $docChild->createAttribute("xmlns");
                    $urlsetChild->appendChild($xmlns);
                    $value = $docChild->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
                    $xmlns->appendChild($value);            
                    foreach (AutoMake::getItemsByProductCategoryId($child['id']) as $make) {
                        $childLinks[] = $childItem['url'] . 'make-' .$make['alias'] . '/';
                        
                        foreach (ProductCategoryModel::getModelsByMakeIdAndCategoryIdsWithChildCategories($make['id'], $treeMap[$child['id']]['ids'], 3) as $model) {
                            $childLinks[] = $childItem['url'] . 'make-' .$make['alias'] . '/' . $model['alias'] . '/';
                        }
                    }
                    
                    if (!empty($childLinks)) {
                        foreach ($childLinks as $link) {
                            $this->addItem($docChild, $urlsetChild, array(
                               'url' => self::SITE_URL . $link,
                               'lastmod' => time(),
                            ));
                        }

                        $mapFiles[] = $fileChild;

                        $docChild->formatOutput = true;
                        $docChild->save(dirname(__FILE__) . "/../../" . $fileChild);
                    }
                    
                }
            }
            foreach (AutoMake::getItemsByProductCategoryId($root['id']) as $make) {
                $rootLinks[] = $root['url'] . 'make-' . $make['alias'] . '/';
                
                $models = ProductCategoryModel::getModelsByMakeIdAndCategoryIdsWithChildCategories($make['id'], $treeMap[$category['id']]['ids'], 2);
                foreach ($models as $model) {
                    $rootLinks[] = $root['url'] . 'make-' . $make['alias'] . '/' . $model['alias'] . '/';
                }
            }
            
            if (!empty($rootLinks)) {
                foreach ($rootLinks as $link) {
                    $this->addItem($doc, $urlset, array(
                       'url' => self::SITE_URL . $link,
                       'lastmod' => time(),
                    ));
                }

                $mapFiles[] = $file;

                $doc->formatOutput = true;
                $doc->save(dirname(__FILE__) . "/../../" . $file);
            }
        }
        
        foreach (AutoMake::getAllFrontFull() as $make) {
            $partsLinks[] = '/parts/make-' . $make['alias'] . '/';
            foreach (AutoModel::getModelsMake($make['id']) as $a => $t) {
                $partsLinks[] = '/parts/make-' . $make['alias'] . '/' . $a . '/';
            }
        }
        
        $file = "/sitemap/parts.xml";
        $mapFiles[] = $file;
        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);            
        foreach ($partsLinks as $url) {
            $this->addItem($doc, $urlset, array(
               'url' => self::SITE_URL . $url,
               'lastmod' => time(),
            ));
        }
        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);
        
        //guides
        $i = 0;
        do {
            $file = "/sitemap/parts_guides_{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $categoryTreeMap = ProductCategory::getTreeMap();
            $categoryIds = array_map(function($item) { return $item['id']; }, $categoryTreeMap);
           
            $condition = "is_active=1 AND category_id IN(" . implode(',', $categoryIds) . ")";
            
            $criteria = new CDbCriteria();
            $criteria->condition = $condition;
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;
            $items = ProductCategoryGuide::model()->findAll($criteria);

            foreach ($items as $item) {
                $this->addItem($doc, $urlset, array(
                    'url' => self::SITE_URL . $item->url,
                    'lastmod' => time(),
                ));
            }

            if (empty($items)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);
        
        
        $i = 0;
        do {
            $file = "/sitemap/make{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            $criteria->compare('is_active', 1);
            $criteria->compare('is_deleted', 0);
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;

            $makes = AutoMake::model()->findAll($criteria);

            foreach ($makes as $make) {

                foreach ($mapModules as $uri) {
                    $this->addItem($doc, $urlset, array(
                        'url' => self::SITE_URL . $uri . $make['alias'] . '/',
                        'lastmod' => time(),
                    ));
                }
            }

            if (empty($makes)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);

        $i = 0;
        do {
            $file = "/sitemap/model{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('Make.is_active', 1);
            $criteria->compare('Make.is_deleted', 0);
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;
            $criteria->with = array('Make');

            $models = AutoModel::model()->findAll($criteria);

            foreach ($models as $model) {

                foreach ($mapModules as $uri) {
                    $this->addItem($doc, $urlset, array(
                        'url' => self::SITE_URL . $uri . $model->Make->alias . '/' . $model->alias . '/',
                        'lastmod' => time(),
                    ));
                }
            }

            if (empty($models)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);


        $i = 0;
        do {
            $file = "/sitemap/model_year{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            $criteria->compare('t.is_active', 1);
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('Make.is_active', 1);
            $criteria->compare('Make.is_deleted', 0);
            $criteria->compare('Model.is_active', 1);
            $criteria->compare('Model.is_deleted', 0);
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;
            $criteria->with = array('Model', 'Model.Make');

            $models = AutoModelYear::model()->findAll($criteria);

            foreach ($models as $model) {

                $this->addItem($doc, $urlset, array(
                    'url' => self::SITE_URL . '/' . $model->Model->Make->alias . '/' . $model->Model->alias . '/' . $model->year . '/photos.html',
                    'lastmod' => time(),
                ));

                foreach ($mapModules as $uri) {
                    if (in_array($uri, array('/0-60-times/', '/tuning/'))) {
                        continue;
                    }

                    $this->addItem($doc, $urlset, array(
                        'url' => self::SITE_URL . $uri . $model->Model->Make->alias . '/' . $model->Model->alias . '/' . $model->year . '/',
                        'lastmod' => time(),
                    ));
                }
            }

            if (empty($models)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);

        $i = 0;
        do {
            $file = "/sitemap/tires_r{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;

            $models = TireRimDiameter::model()->findAll($criteria);

            foreach ($models as $model) {

                $this->addItem($doc, $urlset, array(
                    'url' => self::SITE_URL . '/tires/r' . $model->value . '.html',
                    'lastmod' => time(),
                ));
            }

            if (empty($models)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);


        $items = Yii::app()->db->createCommand("SELECT DISTINCT tire_id FROM `auto_model_year_tire`")->queryAll();
        $tireIds = array();
        foreach ($items as $item) {
            $tireIds[] = $item['tire_id'];
        }

        $i = 0;
        $makeTireUrls = array();
        do {
            $file = "/sitemap/tires_size{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;
            $criteria->addInCondition('t.id', $tireIds);

            $criteria->with = array(
                'VehicleClass',
                'SectionWidth',
                'AspectRatio',
                'RimDiameter',
                'LoadIndex',
                'RearRimDiameter',
                'RearAspectRatio',
                'RearSectionWidth',
            );

            $models = Tire::model()->findAll($criteria);

            echo count($models);

            foreach ($models as $model) {
                $tireAttr = array(
                    'vehicle_class' => $model->VehicleClass->code,
                    'section_width' => $model->SectionWidth->value,
                    'aspect_ratio' => $model->AspectRatio->value,
                    'rim_diameter' => $model->RimDiameter->value,
                );

                $makeModels = Tire::getMakeModelsByTireIds(array($model->id));
                foreach ($makeModels as $makeModel) {
                    $url = self::SITE_URL . '/tires/' . $makeModel['alias'] . '/' . Tire::url($tireAttr, true);
                    $makeTireUrls[$url] = $url;
                }

                $this->addItem($doc, $urlset, array(
                    'url' => self::SITE_URL . Tire::url($tireAttr),
                    'lastmod' => time(),
                ));
            }

            if (empty($models)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);

        ////////////////////////////////////////////////////////
        $file = "/sitemap/tires_make_size.xml";
        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);
        foreach ($makeTireUrls as $makeTireUrl) {
            $this->addItem($doc, $urlset, array(
                'url' => $makeTireUrl,
                'lastmod' => time(),
            ));
        }
        $mapFiles[] = $file;

        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);

        $i = 0;
        do {
            $file = "/sitemap/project_{$i}.xml";
            $doc = new DOMDocument("1.0", 'utf-8');
            $urlset = $doc->createElement("urlset");
            $doc->appendChild($urlset);
            $xmlns = $doc->createAttribute("xmlns");
            $urlset->appendChild($xmlns);
            $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
            $xmlns->appendChild($value);

            $criteria = new CDbCriteria();
            //$criteria->compare('t.is_active', 1);
            $criteria->compare('Make.is_active', 1);
            $criteria->compare('Make.is_deleted', 0);
            $criteria->compare('Model.is_active', 1);
            $criteria->compare('Model.is_deleted', 0);
            $criteria->limit = $limit / 2;
            $criteria->offset = $i * $limit / 2;
            $criteria->with = array('Model', 'Model.Make');

            $projects = Project::model()->findAll($criteria);

            foreach ($projects as $project) {

                $this->addItem($doc, $urlset, array(
                    'url' => self::SITE_URL . '/tuning/' . $project->Make->alias . '/' . $project->Model->alias . '/' . $project->id . '/',
                    'lastmod' => time(),
                ));
            }

            if (empty($projects)) {
                break;
            }

            $mapFiles[] = $file;

            $doc->formatOutput = true;
            $doc->save(dirname(__FILE__) . "/../../" . $file);

            $i++;
        } while (true);
        
        $mapFiles[] = $this->_weight();
        $mapFiles[] = $this->_wheels();
        $mapFiles[] = $this->_bulbs();
    
        foreach ($mapFiles as $mapFile) {
            $attributes = array(
                'url' => self::SITE_URL . $mapFile,
                'lastmod' => time(),
            );
            $this->addItem($doc, $urlset, $attributes);
        }


        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../sitemap.xml");


        print_r($mapFiles);
    }

    private function _weight()
    {
        //weight
        $file = "/sitemap/weight.xml";
        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);
        $mapFiles[] = $file;

        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);

        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);


        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);

        $makes = AutoMake::model()->findAll($criteria);

        foreach ($makes as $make) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/weight/' . $make->alias . '/',
                'lastmod' => time(),
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('Make.is_active', 1);
        $criteria->compare('Make.is_deleted', 0);
        $criteria->with = array('Make');

        $models = AutoModel::model()->findAll($criteria);

        foreach ($models as $model) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/weight/' . $model->Make->alias . '/' . $model->alias . '/',
                'lastmod' => time(),
            ));
        }


        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);

        return $file;
    }

    private function _wheels()
    {
        $file = "/sitemap/wheels.xml";
        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);
        $mapFiles[] = $file;

        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);

        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);


        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);

        $makes = AutoMake::model()->findAll($criteria);

        foreach ($makes as $make) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/wheels/' . $make->alias . '/',
                'lastmod' => time(),
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('Make.is_active', 1);
        $criteria->compare('Make.is_deleted', 0);
        $criteria->with = array('Make');

        $models = AutoModel::model()->findAll($criteria);

        foreach ($models as $model) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/wheels/' . $model->Make->alias . '/' . $model->alias . '/',
                'lastmod' => time(),
            ));
        }


        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);
        
        return $file;
    }

    private function _bulbs()
    {
        $file = "/sitemap/bulbs.xml";
        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);
        $mapFiles[] = $file;

        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);

        $doc = new DOMDocument("1.0", 'utf-8');
        $urlset = $doc->createElement("urlset");
        $doc->appendChild($urlset);
        $xmlns = $doc->createAttribute("xmlns");
        $urlset->appendChild($xmlns);
        $value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $xmlns->appendChild($value);


        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);

        $makes = AutoMake::model()->findAll($criteria);

        foreach ($makes as $make) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/bulbs/' . $make->alias . '/',
                'lastmod' => time(),
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_bulb', 1);
        $criteria->compare('Make.is_active', 1);
        $criteria->compare('Make.is_deleted', 0);
        $criteria->with = array('Make');

        $models = AutoModel::model()->findAll($criteria);

        foreach ($models as $model) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/bulbs/' . $model->Make->alias . '/' . $model->alias . '/',
                'lastmod' => time(),
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->compare('t.is_active', 1);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('Make.is_active', 1);
        $criteria->compare('Make.is_deleted', 0);
        $criteria->compare('Model.is_active', 1);
        $criteria->compare('Model.is_bulb', 1);
        $criteria->compare('Model.is_deleted', 0);
        $criteria->with = array('Model', 'Model.Make');

        $models = AutoModelYear::model()->findAll($criteria);
        foreach ($models as $model) {
            $this->addItem($doc, $urlset, array(
                'url' => self::SITE_URL . '/bulbs/' . $model->Model->Make->alias . '/' . $model->Model->alias . '/' . $model->year . '/',
                'lastmod' => time(),
            ));
        }
        

        $doc->formatOutput = true;
        $doc->save(dirname(__FILE__) . "/../../" . $file);
        
        return $file;
    }

    private function addItem(& $doc, & $urlset, $attributes)
    {
        $url = $doc->createElement("url");
        $urlset->appendChild($url);

        $loc = $doc->createElement("loc");
        $url->appendChild($loc);

        $value = $doc->createTextNode($attributes['url']);
        $loc->appendChild($value);

        $lastmod = $doc->createElement("lastmod");
        $url->appendChild($lastmod);
        $value = $doc->createTextNode(date("Y-m-d", $attributes['lastmod']));
        $lastmod->appendChild($value);
    }
}
