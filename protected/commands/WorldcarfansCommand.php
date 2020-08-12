<?php

class WorldcarfansCommand extends CConsoleCommand {

    public function init() {
        ini_set('max_execution_time', 3600 * 12);
        return parent::init();
    }

    public function actionPhoto() {
        $url = 'http://www.worldcarfans.com/photos';
        $content = CUrlHelper::getPage($url, '', '');
        preg_match('/<div class="navs"><a href="photos\/2" class="nextarrow" style="float:right;"><\/a>Page 1 of (.*?)<\/div>/', $content, $m);
        $countPage = isset($m[1]) ? $m[1] : 63;

        for ($i = 1; $i <= $countPage; $i++) {
            $url = "http://www.worldcarfans.com/photos/{$i}";
            $content = Yii::app()->cache->get($url);
            if ($content == false) {
                $content = CUrlHelper::getPage($url, '', '');
                Yii::app()->cache->get($url, $content, 60 * 60 * 24);
            }

            $content = str_replace(array("\n", "\t", "\r", "                    ", "                "), "", $content);
            $expl = explode('<div id="postsarea">', $content);
            $content = end($expl);
            preg_match_all('/<a href="(.*?)" class="medialistitem"><img src="(.*?)" \/>(.*?)<div class="data">(.*?)photos<\/div>/', $content, $matches);

            foreach ($matches[0] as $key => $value) {
                $expl = explode('<a href="', $matches[1][$key]);
                $url = trim(end($expl));

                $expl = explode('<div class="', $matches[3][$key]);
                $title = trim($expl[0]);

                $album = $this->getParsingWorldcarfansAlbum(array(
                    'url' => $url,
                    'title' => $title,
                        ), $matches[2][$key]);

                echo $album->id . "\n";
            }
        }
    }

    public function actionP() {
        $limit = 100;

        $to = Yii::app()->db->createCommand('SELECT MAX(id) FROM parsing_worldcarfans_album')->queryScalar();

        for ($offset = 0; $offset <= $to; $offset+=$limit) {

            $criteria = new CDbCriteria();
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $criteria->addCondition('id > 1198');
            
            $albums = ParsingWorldcarfansAlbum::model()->findAll($criteria);
            if (empty($albums))
                die();

            foreach ($albums as $key => $album) {
                echo $album->id . "\n";
                $content = Yii::app()->cache->get($album->url);
                if ($content == false) {
                    $content = CUrlHelper::getPage($album->url, '', '');
                    Yii::app()->cache->get($album->url, $content, 60 * 60 * 24);
                }
                $content = str_replace(array("\n", "\t", "\r", "        ", "        ", "    "), "", $content);
                preg_match_all('/<li><a class="thumb" href="(.*?)" title="(.*?)" name="image"><img src="(.*?)" alt="(.*?)" height="80" width="80" \/><\/a><div class="caption">(.*?)<\/div><\/li>/', $content, $matches);

                foreach ($matches[0] as $key => $value) {
                    $photo = $this->getParsingWorldcarfansAlbumPhoto(array(
                        'url' => $matches[1][$key],
                        'album_id' => $album->id,
                        'title' => $matches[2][$key],
                    ));

                    echo "\t" . $photo->id . "\n";
                }
            }
        }
    }

    private function getParsingWorldcarfansAlbum($attributes, $logo_url) {
        $model = ParsingWorldcarfansAlbum::model()->findByAttributes($attributes);
        if (empty($model)) {
            $model = new ParsingWorldcarfansAlbum;
            $model->attributes = $attributes;
            $model->logo_url = $logo_url;

            $modelYears = $this->getDataModelYear();
            $model_year_id = 0;

            foreach ($modelYears as $modelYearId => $modelYearTitle) {
                if (strpos($model->title, $modelYearTitle)) {
                    $model_year_id = $modelYearId;
                    break;
                }
            }

            $model->model_year_id = $model_year_id;
            $model->save();
        }

        return $model;
    }

    private function getParsingWorldcarfansAlbumPhoto($attributes) {
        $model = ParsingWorldcarfansAlbumPhoto::model()->findByAttributes($attributes);
        if (empty($model)) {
            $model = new ParsingWorldcarfansAlbumPhoto;
            $model->attributes = $attributes;
            $model->save();
        }

        return $model;
    }

    private function getDataModelYear() {
        $key = 'getDataModelYear';
        $data = Yii::app()->cache->get($key);

        if ($data == false) {

            $criteria = new CDbCriteria;
            $criteria->with = array(
                'Model' => array('together' => true),
                'Model.Make' => array('together' => true),
            );
            $items = AutoModelYear::model()->findAll($criteria);
            $data = array();
            foreach ($items as $item) {
                $data[$item->id] = $item->year . ' ' . $item->Model->Make->title . ' ' . $item->Model->title;
            }

            Yii::app()->cache->get($key, $data, 60 * 60);
        }

        return $data;
    }

}

?>