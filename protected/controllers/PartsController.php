<?php

class PartsController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = conf('parts_meta_title');
		$this->meta_keywords = conf('parts_meta_keywords');
		$this->meta_description = conf('parts_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Parts',
		);		

		$this->render('index', array(
			'h1' => conf('parts_h1'),
			'header_text' => conf('parts_seo_header_text'),
			'footer_text' => conf('parts_seo_footer_text'),
            'categories' => ProductCategory::getRootItemsWithChildren(),
            'makes' => ProductCategoryModel::getMakes(),
            'guides' => ProductCategoryGuide::getLatest(3),
		));
	}
    
    public function actionCategory($categoryAlias)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = ['[category]' => $category['title']];
        
        $this->pageTitle = conf('parts_category_meta_title', $params);
		$this->meta_keywords = conf('parts_category_meta_keywords', $params);
		$this->meta_description = conf('parts_category_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            '#' => $category['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $this->render('category', array(
			'h1' => conf('parts_category_h1', $params),
			'header_text' => !empty($category['header_text']) 
                ? $category['header_text']
                : conf('parts_category_seo_header_text', $params),
			'footer_text' => !empty($category['footer_text'])
                ? $category['footer_text'] 
                : conf('parts_category_seo_footer_text', $params),
            'categories' => ProductCategory::getChildItemsWithSubs($category['id']),
            'makes' => AutoMake::getItemsByProductCategoryId($category['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$category['id']]['ids']),
            'roots' => ProductCategory::getRootItemsWithChildren(),
            'category' => $category,
		));
    }
   
	public function actionMake($alias)
	{
        $make = AutoMake::getMakeByAlias($alias);
		
      	if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		
        $params = ['[make]' => $make['title']];
        
		$this->pageTitle = conf('parts_make_meta_title', $params);
		$this->meta_keywords = conf('parts_make_meta_keywords', $params);
		$this->meta_description = conf('parts_make_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
			'#' => $make['title'],
		);
		
        $this->render('make', array(
			'h1' => conf('parts_make_h1', $params),
			'header_text' => conf('parts_make_seo_header_text', $params),
			'footer_text' => conf('parts_make_seo_footer_text', $params),
			'make' => $make,
			'models' => ProductCategoryModel::getModelsByMake($make['id']),
            'categories' => ProductCategoryModel::getByMakeCategoryWithChildren($make['id']),
            'guides' => ProductCategoryGuide::getLatest(3),
		));
	}

	public function actionModel($makeAlias, $modelAlias)
	{
        $make = AutoMake::getMakeByAlias($makeAlias);		
      	if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);
		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[make]' => $make['title'],
            '[model]' => $model['title'],
        ];
        
		$this->pageTitle = conf('parts_model_meta_title', $params);
		$this->meta_keywords = conf('parts_model_meta_keywords', $params);
		$this->meta_description = conf('parts_model_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
			'/parts/make-' . $make['alias'] . '/' => $make['title'],
			'#' => $model['title'],
		);
		
        $this->render('model', array(
			'h1' => conf('parts_model_h1', $params),
			'header_text' => conf('parts_model_seo_header_text', $params),
			'footer_text' => conf('parts_model_seo_footer_text', $params),
			'make' => $make,
			'model' => $model,
	        'categories' => ProductCategoryModel::getByModelCategoryWithChildren($model['id']),
            'guides' => ProductCategoryGuide::getLatest(3),
            'lastModelYear' => AutoModel::getLastYear($model['id']),
		));
	}
  
    
    public function actionCategoryMake($categoryAlias, $makeAlias)
    {
        $category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$make = ProductCategoryModel::getMakeByAliasAndCategoryId($category['id'], $makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[make]' => $make['title'],
        ];
        
        $this->pageTitle = !empty($category['make_meta_title']) 
            ? str_replace('[make]', $make['title'], $category['make_meta_title']) 
            : conf('parts_category_make_meta_title', $params);
        $this->meta_keywords = !empty($category['make_meta_keywords']) 
            ? str_replace('[make]', $make['title'], $category['make_meta_keywords']) 
            : conf('parts_category_make_meta_keywords', $params);
        $this->meta_description = !empty($category['make_meta_description']) 
            ? str_replace('[make]', $make['title'], $category['make_meta_description']) 
            : conf('parts_category_make_meta_description', $params);
        
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            $category['url'] => $category['title'],
            '#' => $make['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
       
        $this->render('category_make', array(
			'h1' => conf('parts_category_make_h1', $params),
			'header_text' => !empty($category['make_header_text']) 
                ? str_replace('[make]', $make['title'], $category['make_header_text']) 
                : conf('parts_category_make_seo_header_text', $params),
			'footer_text' => !empty($category['make_footer_text']) 
                ? str_replace('[make]', $make['title'], $category['make_footer_text']) 
                : conf('parts_category_make_seo_footer_text', $params),
            'categories' => ProductCategoryModel::getByMakeIdAndCategoryIdChildItemsWithSubs($category['id'], $make['id']),
            'makes' => AutoMake::getItemsByProductCategoryId($category['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$category['id']]['ids']),
            'roots' => ProductCategoryModel::getRootItemsByMakeId($make['id']),
            'models' => ProductCategoryModel::getModelsByMakeIdAndCategoryIdsWithChildCategories($make['id'], $treeMap[$category['id']]['ids'], 2),
            'category' => $category,
            'make' => $make,
		));
    }

    public function actionCategoryModel($categoryAlias, $makeAlias, $modelAlias)
    {
        $category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$make = ProductCategoryModel::getMakeByAliasAndCategoryId($category['id'], $makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$model = ProductCategoryModel::getModelByAliasAndMakeIdAndCategoryId($category['id'], $make['id'], $modelAlias);
		if (empty($model)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[make]' => $make['title'],
            '[model]' => $model['title'],
        ];
        
        $this->pageTitle = !empty($model['meta_title']) ? $model['meta_title'] : conf('parts_category_model_meta_title', $params);
		$this->meta_keywords = !empty($model['meta_keywords']) ? $model['meta_keywords'] : conf('parts_category_model_meta_keywords', $params);
		$this->meta_description = !empty($model['meta_description']) ? $model['meta_description'] : conf('parts_category_model_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            $category['url'] => $category['title'],
            $category['url'] . 'make-' . $make['alias'] => $make['title'],
            '#' => $model['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $this->render('category_model', array(
			'h1' => !empty($model['meta_h1']) ? $model['meta_h1'] : conf('parts_category_model_h1', $params),
			'header_text' => !empty($model['header_text']) ? $model['header_text'] : conf('parts_category_model_seo_header_text', $params),
			'footer_text' => !empty($model['footer_text']) ? $model['footer_text'] : conf('parts_category_model_seo_footer_text', $params),
            'categories' => ProductCategoryModel::getByModelIdAndCategoryIdChildItemsWithSubs($category['id'], $model['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$category['id']]['ids']),
            'category' => $category,
            'make' => $make,
            'model' => $model,
            'lastModelYear' => AutoModel::getLastYear($model['id']), 
            'rightCats' => ProductCategoryModel::getByModelCategoryWithChildren($model['id']),
		));
    }
    
    public function actionCategoryChild($categoryAlias, $childAlias)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childAlias);
		if (empty($child)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[child]' => $child['title'],
        ];
        
        $this->pageTitle = conf('parts_category_child_meta_title', $params);
		$this->meta_keywords = conf('parts_category_child_meta_keywords', $params);
		$this->meta_description = conf('parts_category_child_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            $category['url'] => $category['title'],
            '#' => $child['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $this->render('category_child', array(
			'h1' => conf('parts_category_child_h1', $params),
			'header_text' => !empty($child['header_text'])
                ? $child['header_text'] 
                : conf('parts_category_child_seo_header_text', $params),
			'footer_text' => !empty($child['footer_text'])
                ? $child['footer_text'] 
                : conf('parts_category_seo_child_footer_text', $params),
            'categories' => ProductCategory::getChildItemsWithSubs($child['id']),
            'makes' => AutoMake::getItemsByProductCategoryId($child['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$child['id']]['ids']),
            'childs' => ProductCategory::getChildItemsWithSubs($category['id']),
            'category' => $category,
            'child' => $child,
		));
    }
    
    public function actionCategoryChildMake($categoryAlias, $childAlias, $makeAlias)
    {
        $category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childAlias);
		if (empty($child)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$make = ProductCategoryModel::getMakeByAliasAndCategoryId($child['id'], $makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[child]' => $child['title'],
            '[make]' => $make['title'],
        ];
        		
        $this->pageTitle = !empty($child['make_meta_title']) 
            ? str_replace('[make]', $make['title'], $child['make_meta_title']) 
            : conf('parts_category_child_make_meta_title', $params);
        $this->meta_keywords = !empty($child['make_meta_keywords']) 
            ? str_replace('[make]', $make['title'], $child['make_meta_keywords']) 
            : conf('parts_category_child_make_meta_keywords', $params);
        $this->meta_description = !empty($child['make_meta_description']) 
            ? str_replace('[make]', $make['title'], $child['make_meta_description']) 
            : conf('parts_category_child_make_meta_description', $params);
        
        
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            $category['url'] => $category['title'],
            $child['url'] => $child['title'],
            '#' => $make['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        //dd(ProductCategoryModel::getModelsByMakeIdAndCategoryIdsWithChildCategories($make['id'], $treeMap[$child['id']]['ids'], 3));
        $this->render('category_child_make', array(
			'h1' => conf('parts_category_child_make_h1', $params),
			'header_text' => !empty($child['make_header_text']) 
                ? str_replace('[make]', $make['title'], $child['make_header_text']) 
                : conf('parts_category_child_make_seo_header_text', $params),
			'footer_text' => !empty($child['make_footer_text']) 
                ? str_replace('[make]', $make['title'], $child['make_footer_text']) 
                : conf('parts_category_child_make_seo_footer_text', $params),
            'categories' => ProductCategoryModel::getByMakeIdAndCategoryIdChildItemsWithSubs($category['id'], $make['id']),
            'makes' => AutoMake::getItemsByProductCategoryId($child['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$child['id']]['ids']),
            'childs' => ProductCategoryModel::getChildItemsByParentIdMakeId($category['id'], $make['id']),
            'models' => ProductCategoryModel::getModelsByMakeIdAndCategoryIdsWithChildCategories($make['id'], $treeMap[$child['id']]['ids'], 3),
            'category' => $category,
            'child' => $child,
            'make' => $make,
		));
    }
    
    public function actionCategoryChildModel($categoryAlias, $childAlias, $makeAlias, $modelAlias)
    {
        $category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childAlias);
		if (empty($child)) {
			throw new CHttpException(404,'Page cannot be found.');
		}        
		$make = ProductCategoryModel::getMakeByAliasAndCategoryId($child['id'], $makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$model = ProductCategoryModel::getModelByAliasAndMakeIdAndCategoryId($child['id'], $make['id'], $modelAlias);
		if (empty($model)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[child]' => $child['title'],
            '[make]' => $make['title'],
            '[model]' => $model['title'],
        ];
        
        $this->pageTitle = !empty($model['meta_title']) ? $model['meta_title'] : conf('parts_category_child_model_meta_title', $params);
		$this->meta_keywords = !empty($model['meta_keywords']) ? $model['meta_keywords'] : conf('parts_category_child_model_meta_keywords', $params);
		$this->meta_description = !empty($model['meta_description']) ? $model['meta_description'] : conf('parts_category_child_model_meta_description', $params);		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/parts/' => 'Parts',
            $category['url'] => $category['title'],
            $child['url'] => $child['title'],
            $child['url'] . 'make-' . $make['alias'] . '/' => $make['title'],
            '#' => $model['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $ebayModel = EbayAutoModel::getItemByAtkId($model['id']);
        $products = [];
        if (!empty($ebayModel)) {
            $products = Product::getItemsByCategoriesAndModel($treeMap[$child['id']]['ebay_category_ids'], $ebayModel['id']);
            if (isset($_GET['t'])) {
                d($products);
            }
        }    
        
        //dd($products);
            
        $this->render('category_child_model', array(
			'h1' => !empty($model['meta_h1']) ? $model['meta_h1'] : conf('parts_category_child_model_h1', $params),
			'header_text' => !empty($model['header_text']) ? $model['header_text'] : conf('parts_category_child_model_seo_header_text', $params),
			'footer_text' => !empty($model['footer_text']) ? $model['footer_text'] : conf('parts_category_child_model_seo_footer_text', $params),
            'categories' => ProductCategoryModel::getByModelIdAndCategoryIdChildItemsWithSubs($child['id'], $model['id']),
            'rightCats' => ProductCategoryModel::getByModelIdAndCategoryIdChildItemsWithSubs($category['id'], $model['id']),
            'guides' => ProductCategoryGuide::getItemsByCategoryIds($treeMap[$child['id']]['ids']),
            'category' => $category,
            'child' => $child,
            'make' => $make,
            'model' => $model,
            'lastModelYear' => AutoModel::getLastYear($model['id']), 
            'products' => $products,
       ));
    }

    public function actionGuides($page = 1)
    {
        $this->pageTitle = conf('parts_guides_meta_title') . ($page > 1 ? " page {$page}" : "");
		$this->meta_keywords = conf('parts_guides_meta_keywords');
		$this->meta_description = conf('parts_guides_meta_description');		
		
        $this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Guides',
		);		
		
        list($count, $items, $limit) = ProductCategoryGuide::getData($page);
       
        $this->render('guides', array(
			'h1' => conf('parts_guides_h1'),
			'header_text' => conf('parts_guides_seo_header_text'),
			'footer_text' => conf('parts_guides_seo_footer_text'),
            'count' => $count,
            'items' => $items,
            'limit' => $limit,
            'categories' => ProductCategory::getRootItemsWithChildren(),
       ));
    }

    public function actionCategoryGuides($categoryAlias, $page = 1)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}        
        $params = ['[category]' => $category['title']];
        
        $this->pageTitle = conf('parts_category_guides_meta_title', $params) . ($page > 1 ? " page {$page}" : "");
		$this->meta_keywords = conf('parts_category_guides_meta_keywords', $params);
		$this->meta_description = conf('parts_category_guides_meta_description', $params);		
		
        $this->breadcrumbs = array(
			'/' => 'Home',
			'/guides/' => 'Guides',
			'#' => $category['title'],
		);		
		
        list($count, $items, $limit) = ProductCategoryGuide::getData($page, $category['id']);
       
        $this->render('guides', array(
			'h1' => conf('parts_category_guides_h1', $params),
			'header_text' => conf('parts_category_guides_seo_header_text', $params),
			'footer_text' => conf('parts_category_guides_seo_footer_text', $params),
            'count' => $count,
            'items' => $items,
            'limit' => $limit,
            'categories' => ProductCategoryGuide::getRootChildren($category['id']),
       ));
    }
    
    public function actionCategoryChildGuides($categoryAlias, $childAlias, $page = 1)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}  
		$child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childAlias);
		if (empty($child)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
        
        $params = [
            '[category]' => $category['title'],
            '[child]' => $child['title'],
        ];
        
        $this->pageTitle = conf('parts_category_child_guides_meta_title', $params) . ($page > 1 ? " page {$page}" : "");
		$this->meta_keywords = conf('parts_category_child_guides_meta_keywords', $params);
		$this->meta_description = conf('parts_category_child_guides_meta_description', $params);		
		
        $this->breadcrumbs = array(
			'/' => 'Home',
			'/guides/' => 'Guides',
			$category['url'] . 'guides/' => $category['title'],
			'#' => $child['title'],
		);		
		
        list($count, $items, $limit) = ProductCategoryGuide::getData($page, $child['id']);
       
        $this->render('guides', array(
			'h1' => conf('parts_category_child_guides_h1', $params),
			'header_text' => conf('parts_category_child_guides_seo_header_text', $params),
			'footer_text' => conf('parts_category_child_guides_seo_footer_text', $params),
            'count' => $count,
            'items' => $items,
            'limit' => $limit,
            'categories' => ProductCategory::getItemsByParentId($child['id']),
       ));
    }
    
    public function actionCategoryGuidesItem($categoryAlias, $guideAlias)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}        
		$guide = ProductCategoryGuide::getItem($guideAlias, $category['id']);
		if (empty($guide)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}        
        $params = [
            '[category]' => $category['title'],
            '[guide]' => $guide['title'],
        ];
        
        $this->pageTitle = conf('parts_category_guides_item_meta_title', $params);
		$this->meta_keywords = conf('parts_category_guides_item_meta_keywords', $params);
		$this->meta_description = conf('parts_category_guides_item_meta_description', $params);		
		
        $this->breadcrumbs = array(
			'/' => 'Home',
			'/guides/' => 'Guides',
			$category['url'] . 'guides/' => $category['title'],
			'#' => $guide['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $this->render('guides_item', array(
			'h1' => conf('parts_category_guides_item_h1', $params),
			'header_text' => conf('parts_category_guides_item_seo_header_text', $params),
			'footer_text' => conf('parts_category_guides_item_seo_footer_text', $params),
			'guide' => $guide,
			'currentCategory' => $category,
            'categories' => ProductCategoryGuide::getRootCategories(),
            'latest' => ProductCategoryGuide::getLatest(3, $treeMap[$category['id']]['ids'], $guide['id']),
       ));
    }

    public function actionCategoryChildGuidesItem($categoryAlias, $childAlias, $guideAlias)
    {
		$category = ProductCategory::getRootItemByAlias($categoryAlias);
		if (empty($category)) {
			 throw new CHttpException(404,'Page cannot be found.');
		} 
		$child = ProductCategory::getChildItemByParentIdAndAlias($category['id'], $childAlias);
		if (empty($child)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		$guide = ProductCategoryGuide::getItem($guideAlias, $child['id']);
		if (empty($guide)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}        
        $params = [
            '[category]' => $category['title'],
            '[child]' => $child['title'],
            '[guide]' => $guide['title'],
        ];
        
        $this->pageTitle = conf('parts_category_child_guides_item_meta_title', $params);
		$this->meta_keywords = conf('parts_category_child_guides_item_meta_keywords', $params);
		$this->meta_description = conf('parts_category_child_guides_item_meta_description', $params);		
		
        $this->breadcrumbs = array(
			'/' => 'Home',
			'/guides/' => 'Guides',
			$category['url'] . 'guides/' => $category['title'],
			$child['url'] . 'guides/' => $child['title'],
			'#' => $guide['title'],
		);		
		
        $treeMap = ProductCategory::getTreeMap();
        
        $this->render('guides_item', array(
			'h1' => conf('parts_category_child_guides_item_h1', $params),
			'header_text' => conf('parts_category_child_guides_item_seo_header_text', $params),
			'footer_text' => conf('parts_category_child_guides_item_seo_footer_text', $params),
			'guide' => $guide,
            'categories' => ProductCategoryGuide::getRootCategories(),
            'latest' => ProductCategoryGuide::getLatest(3, $treeMap[$child['id']]['ids'], $guide['id']),
			'currentCategory' => $child,
       ));
    }

    
}