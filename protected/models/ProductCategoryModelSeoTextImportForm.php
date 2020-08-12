<?php

/**
 * ProductCategoryModelSeoTextImportForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ProductCategoryModelSeoTextImportForm extends CFormModel
{
	public $file; 

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('file', 'required'),
			array(
				'file', 
				'file', 
				'types'=>'csv',
			),	
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'file' => 'Csv file',
		);
	}
    
    public function import()
    {
        $rows = array_map(function($v){return str_getcsv($v, ";");}, file($this->file->getTempName()));
        $header = array_shift($rows);
        $csv    = [];
        foreach($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        foreach ($rows as $row) {
            $seoText = new ProductCategoryModelSeoText;
            $seoText->attributes = [
                'category_id' => $row[0],
                'header_text' => $row[1],
                'footer_text' => $row[2],
            ];
            $seoText->save();
        }
        
        return true;
    }
}
