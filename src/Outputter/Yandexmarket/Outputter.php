<?php
namespace ddGetDocuments\Outputter\Yandexmarket;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter
{
	protected
		/**
		 * @property $templates {stdClass}
		 * @property $templates->wrapper {string}
		 * @property $templates->offers_item {string}
		 * @property $templates->offers_item_elem {string}
		 * @property $templates->offers_item_elemParam {string}
		 */
		$shopData = [
			'shopName' => '',
			'agency' => '',
			'currencyId' => 'RUR',
			'platform' => '(MODX) Evolution CMS',
			'version' => '[(settings_version)]'
		],
		$offerFields = [
			'name' => [
				'docFieldName' => 'pagetitle',
				'tagName' => 'name'
			],
			'price' => [
				'docFieldName' => '',
				'tagName' => 'price'
			],
			'priceOld' => [
				'docFieldName' => '',
				'tagName' => 'oldprice'
			],
			'categoryId' => [
				'docFieldName' => 'parent',
				'tagName' => 'categoryId'
			],
			'picture' => [
				'docFieldName' => '',
				'tagName' => 'picture',
				'valuePrefix' => '[(site_url)]'
			],
			'model' => [
				'docFieldName' => '',
				'tagName' => 'model'
			],
			'vendor' => [
				'docFieldName' => '',
				'tagName' => 'vendor'
			],
			'available' => [
				'docFieldName' => '',
				//without template
				'templateName' => ''
			],
			'description' => [
				'docFieldName' => '',
				'tagName' => 'description',
				'valuePrefix' => '<![CDATA[',
				'valueSuffix' => ']]>'
			],
			'salesNotes' => [
				'docFieldName' => '',
				'tagName' => 'sales_notes'
			],
			'manufacturerWarranty' => [
				'docFieldName' => '',
				'tagName' => 'manufacturer_warranty'
			],
			'countryOfOrigin' => [
				'docFieldName' => '',
				'tagName' => 'country_of_origin'
			],
			'homeCourierDelivery' => [
				'docFieldName' => '',
				'tagName' => 'delivery'
			],
			'dimensions' => [
				'docFieldName' => '',
				'tagName' => 'dimensions'
			],
			'weight' => [
				'docFieldName' => '',
				'tagName' => 'weight'
			],
			'additionalParams' => [
				'docFieldName' => '',
				'templateName' => '',
				'tagName' => 'param'
			],
			'customData' => [
				'docFieldName' => '',
				'templateName' => ''
			]
		],
		$templates = [
			'wrapper' => '
				<?xml version="1.0" encoding="utf-8"?>
					<yml_catalog date="[+generationDate+]">
						<shop>
							<name>[+shopData.shopName+]</name>
							<company>[+shopData.companyName+]</company>
							<url>[(site_url)]</url>
							<platform>[+shopData.platform+]</platform>
							<version>[+shopData.version+]</version>
							[+shopData.agency+]
							<currencies>
								<currency id="[+shopData.currencyId+]" rate="1" />
							</currencies>
							<categories>[+ddGetDocuments_categories+]</categories>
							<offers>[+ddGetDocuments_items+]</offers>
						</shop>
					</yml_catalog>
			',
			'categories_item' => '<category id="[+id+]"[+attrs+]>[+value+]</category>',
			'offers_item' => '
				<offer id="[+id+]" available="[+available+]">
					<url>[(site_url)][~[+id+]~]</url>
					[+name+]
					[+price+]
					[+priceOld+]
					[+shopData.currencyId+]
					[+categoryId+]
					[+picture+]
					[+vendor+]
					[+model+]
					[+description+]
					[+salesNotes+]
					[+manufacturerWarranty+]
					[+countryOfOrigin+]
					[+delivery+]
					[+dimensions+]
					[+weight+]
					[+additionalParams+]
					[+customData+]
				</offer>
			',
			'offers_item_elem' => '<[+tagName+][+attrs+]>[+value+]</[+tagName+]>',
// 			'offers_item_elemAdditionalParams' => '<param name="[+name+]"[+attrs+]>[+value+]</param>',
		],
		$categoryIds_last
	;
	
	private
		$outputter_StringInstance,
		$categoryIds = []
	;
	
	/**
	 * __construct
	 * @version 1.2 (2019-10-29)
	 * 
	 * @note @link https://yandex.ru/support/partnermarket/export/yml.html
	 * 
	 * @param $params {array_associative}
	 * @param $params['shopData_shopName'] {string} — Короткое название магазина, не более 20 символов. @required
	 * @param $params['shopData_companyName'] {string} — Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней идентификации. @required
	 * @param $params['shopData_agency'] {string} — Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за работоспособность сайта. Default: —.
	 * @param $params['shopData_currencyId'] {string} — Currency code (https://yandex.ru/support/partnermarket/currencies.html). Default: 'RUR'.
	 * @param $params['shopData_platform'] {string} — Содержимое тега `<platform>`. Default: '(MODX) Evolution CMS'.
	 * @param $params['shopData_version'] {string} — Содержимое тега `<version>`. Default: '[(settings_version)]'.
	 * @param $params['categoryIds_last'] {string_commaSepareted} — id конечных категорий(parent). Если пусто то выводятся только непосредственный родитель товара. Defalut: —. 
	 * @param $params['offerFields_price'] {string_docField|''} — Поле, содержащее актуальную цену товара. @required
	 * @param $params['offerFields_priceOld'] {string_docField} — Поле, содержащее старую цену товара (должна быть выше актуальной цены). Default: —.
	 * @param $params['offerFields_picture'] {string_docField} — Поле, содержащее изображение товара. Defalut: —.
	 * @param $params['offerFields_name'] {string_docField} — Поле, содержащее модель товара. Default: 'pagetitle'.
	 * @param $params['offerFields_model'] {string_docField} — Поле, содержащее модель товара. Defalut: —.
	 * @param $params['offerFields_vendor'] {string_docField} — Поле, содержащее производителя товара. Defalut: —.
	 * @param $params['offerFields_available'] {string_docField|''} — Поле, содержащее статус товара ('true'|'false'). Default: '' (всегда выводить 'true').
	 * @param $params['offerFields_description'] {string_docField} — Поле, содержащее описание предложения (длина текста — не более 3000 символов). Default: —.
	 * @param $params['offerFields_salesNotes'] {string_docField} — Поле, содержащее «sales_notes» (https://yandex.ru/support/partnermarket/elements/sales_notes.html). Default: —.
	 * @param $params['offerFields_manufacturerWarranty'] {string_docField} — Поле, содержащее наличие официальной гарантии производителя ('true'|'false'). Default: —.
	 * @param $params['offerFields_countryOfOrigin'] {string_docField} — Поле, содержащее страну производства товара. Default: —.
	 * @param $params['offerFields_homeCourierDelivery'] {string_docField} — Поле, содержащее возможность курьерской доставки по региону магазина ('true'|'false'). Default: —.
	 * @param $params['offerFields_dimensions'] {string_docField} — Поле, содержащее габариты товара (длина, ширина, высота) в упаковке (размеры укажите в сантиметрах, формат: три положительных числа с точностью 0.001, разделитель целой и дробной части — точка, числа должны быть разделены символом «/» без пробелов). Default: —.
	 * @param $params['offerFields_weight'] {string_docField} — Поле, содержащее вес товара в килограммах с учетом упаковки (формат: положительное число с точностью 0.001, разделитель целой и дробной части — точка). Default: —.
	 * @param $params['offerFields_additionalParams'] {string_docField} — Поле, содержащее элементы «param» (https://yandex.ru/support/partnermarket/param.html). Default: —.
	 * @param $params['offerFields_customData'] {string_docField} — Поле, содержащее произвольный текст, который будет вставлен перед закрывающим тегом «</offer>». Default: —.
	 * @param $params['templates_wrapper'] {string_chunkName|string} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+]. Default: ''.
	 * @param $params['templates_categories_item'] {string_chunkName|string} — Available placeholders: [+id+], [+value+], [+parent+]. Default: '<category id="[+id+]"[+attrs+]>[+value+]</category>'.
	 * @param $params['templates_offers_item'] {string_chunkName|string} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: ''.
	 * @param $params['templates_offers_item_elem' . $FieldName] {string_chunkName|string} — Можно задать шаблон любого элемента offer, называем в соответствии с параметрами 'offerFields_', например: $params['templates_offers_item_elemCountryOfOrigin']. Default: —.
	 */
	function __construct($params = []){
		//Convert params to objects
		$this->shopData = (object) $this->shopData;
		$this->offerFields = (object) $this->offerFields;
		foreach (
			$this->offerFields as
			$offerFieldName =>
			$offerFieldValue
		){
			$this->offerFields->{$offerFieldName} = (object) $this->offerFields->{$offerFieldName};
		}
		$this->templates = (object) $this->templates;
		//Trim all templates
		foreach (
			$this->templates as
			$templateName =>
			$templateText
		){
			$this->templates->{$templateName} = trim($templateText);
		}
		
		//Call base constructor
		parent::__construct($params);
		
		//Save shop data and offer fields
		foreach (
			$params as
			$paramName =>
			$paramValue
		){
			//Shop data
			if (substr(
				$paramName,
				0,
				9
			) == 'shopData_'){
				$this->shopData->{substr(
					$paramName,
					9
				)} = $paramValue;
			//Offer field names
			}elseif (substr(
				$paramName,
				0,
				12
			) == 'offerFields_'){
				$this->offerFields->{substr(
					$paramName,
					12
				)}->docFieldName = $paramValue;
			//Templates
			}elseif (substr(
				$paramName,
				0,
				10
			) == 'templates_'){
				$this->templates->{substr(
					$paramName,
					10
				)} = \ddTools::$modx->getTpl($paramValue);
			}
		}
		
		//Offer
		$templateData = [
			'shopData' => (array) $this->shopData
		];
		
		$templateData['shopData']['currencyId'] = \ddTools::parseText([
			'text' => $this->templates->offers_item_elem,
			'data' => [
				'tagName' => 'currencyId',
				'value' => $templateData['shopData']['currencyId'],
				'attrs' => ''
			],
			'mergeAll' => false
		]);
		
		foreach (
			$this->offerFields as
			$fieldAlias =>
			$fieldData
		){
			$templateData[$fieldAlias] = $fieldData->docFieldName;
			
			//Replace to field name placeholder
			if (!empty($fieldData->docFieldName)){
				$templateData[$fieldAlias] = '[+' . $templateData[$fieldAlias] . '+]';
			}else{
				//Always available
				if ($fieldAlias == 'available'){
					$templateData[$fieldAlias] = 'true';
				}
			}
		}
		
		//Prepare offer item template
		$this->templates->offers_item = \ddTools::parseText([
			'text' => $this->templates->offers_item,
			'data' => $templateData,
			'mergeAll' => false
		]);
		
		//Wrapper
		$templateData = [
			'shopData' => (array) $this->shopData,
			'generationDate' => date('Y-m-d H:i')
		];
		
		//TODO Это здесь вообще надо делать или лучше в другое место перенести?
		//Prepare «agency» tag
		if (!empty($templateData['shopData']['agency'])){
			$templateData['shopData']['agency'] = \ddTools::parseText([
				'text' => $this->templates->offers_item_elem,
				'data' => [
					'tagName' => 'agency',
					'value' => $templateData['shopData']['agency'],
					'attrs' => ''
				],
				'mergeAll' => false
			]);
		}
		//Prepare wrapper template
		$this->templates->wrapper = \ddTools::parseText([
			'text' => $this->templates->wrapper,
			'data' => $templateData,
			'mergeAll' => false
		]);
		
		//save last parent id for category
		$this->categoryIds_last =
			isset($params['categoryIds_last']) ?
			trim($params['categoryIds_last']) :
			''
		;
		
		//We use the “String” Outputter as base
		$outputter_StringClass = \ddGetDocuments\Outputter\Outputter::includeOutputterByName('String');
		$outputter_StringParams = [
			'itemTpl' => $this->templates->offers_item,
			'wrapperTpl' => $this->templates->wrapper
		];
		//Transfer provider link
		if (isset($params['dataProvider'])){
			$outputter_StringParams['dataProvider'] = $params['dataProvider'];
		}
		$this->outputter_StringInstance = new $outputter_StringClass($outputter_StringParams);
	}
	
	/**
	 * escapeSpecialChars
	 * @version 0.2 (2018-10-11)
	 * 
	 * @param $inputString {string} — Строка, которую нужно экранировать. https://yandex.ru/support/partnermarket/export/yml.html
	 * 
	 * @return {string}
	 */
	private function escapeSpecialChars($inputString){
		return str_replace(
			[
				'"',
				'&',
				'>',
				'<',
				"'"
			],
			[
				'&quot;',
				'&amp;',
				'&gt;',
				'&lt;',
				'&apos;'
			],
			$inputString
		);
	}
	
	/**
	 * parse
	 * @version 1.3 (2019-10-29)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		//Foreach all docs-items
		foreach (
			$data->provider->items as
			$docIndex =>
			$docData
		){
			//Correct price
			if (
				empty($docData[$this->offerFields->price->docFieldName]) &&
				!empty($docData[$this->offerFields->priceOld->docFieldName])
			){
				$docData[$this->offerFields->price->docFieldName] = $docData[$this->offerFields->priceOld->docFieldName];
				
				unset($docData[$this->offerFields->priceOld->docFieldName]);
			}
			
			//Check required elements
			if (!empty($docData[$this->offerFields->price->docFieldName])){
				//Save category id
				if (is_numeric($docData[$this->offerFields->categoryId->docFieldName])){
					$this->categoryIds[] = $docData[$this->offerFields->categoryId->docFieldName];
				}
				
				//Foreach all offer fields
				foreach(
					$this->offerFields as
					$offerFieldName =>
					$offerFieldData
				){
					if (
						//If is set
						!empty($offerFieldData->docFieldName) &&
						//And data is set
						!empty($docData[$offerFieldData->docFieldName])
					){
						//Boolean fields
						if (
							in_array(
								$offerFieldName,
								[
									'available',
									'manufacturerWarranty',
									'delivery'
								]
							) &&
							(
								$docData[$offerFieldData->docFieldName] !== 'true' &&
								$docData[$offerFieldData->docFieldName] !== 'false'
							)
						){
							$data->provider->items[$docIndex][$offerFieldData->docFieldName] =
								(bool) $docData[$offerFieldData->docFieldName] ?
								'true' :
								'false'
							;
						}
						
						//Fields that may be set as document IDs
						if (
							in_array(
								$offerFieldName,
								[
									'vendor',
									'countryOfOrigin'
								]
							) &&
							//Set as document id
							is_numeric($data->provider->items[$docIndex][$offerFieldData->docFieldName])
						){
							//Try to get pagetitle
							$docData_fieldToGet_data = \ddTools::getDocument(
								$data->provider->items[$docIndex][$offerFieldData->docFieldName],
								'pagetitle'
							);
							//If success
							if (is_array($docData_fieldToGet_data)){
								$data->provider->items[$docIndex][$offerFieldData->docFieldName] = $docData_fieldToGet_data['pagetitle'];
							}
						}
						
						//Value prefix
						if (isset($offerFieldData->valuePrefix)){
							$data->provider->items[$docIndex][$offerFieldData->docFieldName] = $offerFieldData->valuePrefix . $data->provider->items[$docIndex][$offerFieldData->docFieldName];
						}
						//Value suffix
						if (isset($offerFieldData->valueSuffix)){
							$data->provider->items[$docIndex][$offerFieldData->docFieldName] .= $offerFieldData->valueSuffix;
						}
						
						//Try to search template by name
						$templateName = 'offers_item_elem' . ucfirst($offerFieldName);
						if (!isset($this->templates->{$templateName})){
							//Default element template
							if (!isset($offerFieldData->templateName)){
								$templateName = 'offers_item_elem';
							}else{
								$templateName = $offerFieldData->templateName;
							}
						}
						
						if (
							//If need to use template
							!empty($templateName) &&
							//Required data for template
							!empty($offerFieldData->tagName)
						){
							//Final element parsing
							$data->provider->items[$docIndex][$offerFieldData->docFieldName] = \ddTools::parseText([
								'text' => $this->templates->{$templateName},
								'data' => [
									'tagName' => $offerFieldData->tagName,
									'value' => $this->escapeSpecialChars($data->provider->items[$docIndex][$offerFieldData->docFieldName])
								],
								'mergeAll' => false
							]);
						}
					}
				}
			}else{
				//Remove invalid offers
				unset($data->provider->items[$docIndex]);
			}
		}
		
		//Prepare categories
		$categoriesString = '';
		$this->categoryIds = array_unique($this->categoryIds);
		$categoryIds_all = [];
		$categoryIds_last = $this->categoryIds;
		
		if(!empty($this->categoryIds_last)){
			$categoryIds_last = explode(
				',',
				$this->categoryIds_last
			);
		}
		
		$getCategories = function ($id) use (
			$categoryIds_last, 
			&$categoryIds_all, 
			&$categoriesString, 
			&$getCategories
		){
			if(!in_array(
				$id, 
				$categoryIds_all
			)){
				$category = \ddTools::getDocument(
					//id
					$id,
					'pagetitle,id,parent',
					//published
					'all',
					//deleted
					0
				);
				$categoryIds_all[] = $id;
				
				if(!in_array(
					$category['id'], 
					$categoryIds_last
				)){
					$categoriesString .= \ddTools::parseText([
						'text' => $this->templates->categories_item,
						'data' => [
							'id' => $category['id'],
							'value' => $this->escapeSpecialChars($category['pagetitle']),
							'parent' => $category['parent'],
							'attrs' => ' parentId="' . $category['parent'] . '"'
						],
						'mergeAll' => false
					]);
					$getCategories($category['parent']);
				}
			}
		};
		
		foreach(
			$this->categoryIds as 
			$id
		){
			$getCategories($id);
		}
		
		foreach(
			$categoryIds_last as 
			$id
		){
			$category = \ddTools::getDocument(
				//id
				$id,
				'pagetitle,id,parent',
				//published
				'all',
				//deleted
				0
			);
			$categoriesString .= \ddTools::parseText([
				'text' => $this->templates->categories_item,
				'data' => [
					'id' => $category['id'],
					'value' => $this->escapeSpecialChars($category['pagetitle']),
					'parent' => $category['parent']
				],
				'mergeAll' => false
			]);
		}
		
		$this->outputter_StringInstance->placeholders['ddGetDocuments_categories'] = $categoriesString;
		
		//Just use the “String” class
		return $this->outputter_StringInstance->parse($data);
	}
}