<?php

class SeoText extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BodyStyle the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seo_text';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('page, text', 'required'),
            array('page', 'unique'),
            array('id', 'safe'),
        );
    }

    public function afterSave()
    {
        $this->clearCache();

        return parent::afterSave();
    }

    public function afterDelete()
    {
        return parent::afterDelete();
    }

    private function clearCache()
    {
        Yii::app()->cache->clear(Tags::SEO_TEXT);
    }

    public static function getText($page, $id, $params = [])
    {
        $key = Tags::SEO_TEXT . '_getText_' . $page . '_' . $id;
        $data = Yii::app()->cache->get($key);

        if ($data === false) {
            $data = '';
            $seo = self::model()->findByAttributes(['page' => $page . '_' . $id]);
            if ($seo === null) {
                $seoSource = SeoTextSource::model()->findByAttributes(['page' => $page]);

                if ($seoSource !== null) {

                    $data = $seoSource->text;

                    if (!empty($params)) {
                        $data = str_replace(array_keys($params), array_values($params), $data);
                    }

                    $seo = new self;
                    $seo->text = $data;
                    $seo->page = $page . '_' . $id;
                    if ($seo->save()) {
                        $seoSource->delete();
                    }
                }
            } else {
                $data = $seo->text;
            }

            Yii::app()->cache->set($key, $data, 0, new Tags(Tags::SEO_TEXT));
        }

        return $data;
    }
}
