<?php
namespace ddGetDocuments\Outputter\Yandexmarket;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	protected
		/**
		 * @property $docFields {array} — Document fields including TVs used in the output.
		 */
		$docFields = [
			'id',
			//May need for smart name
			'pagetitle'
		],
		
		/**
		 * @property $shopData {stdClass}
		 * @property $shopData->{$name} {string}
		 */
		$shopData = [
			'shopName' => '',
			'agency' => '',
			'currencyId' => 'RUR',
			'platform' => '(MODX) Evolution CMS',
			'version' => '[(settings_version)]'
		],
		
		/**
		 * @property $offerFields {stdClass}
		 * @property $offerFields->{$fieldName} {stdClass}
		 * @property $offerFields->{$fieldName}->docFieldName {string} — Название поля документа, содержащего значение.
		 * @property $offerFields->{$fieldName}->tagName {string} — Имя тега для вывода.
		 * @property $offerFields->{$fieldName}->valuePrefix {string} — Префикс, который будет добавлен к значению при выводе.
		 * @property $offerFields->{$fieldName}->valueSuffix {string} — Суффикс, который будет добавлен к значению при выводе.
		 * @property $offerFields->{$fieldName}->templateName {string} — Название шаблона, по которому парсить вывод.
		 * @property $offerFields->{$fieldName}->disableEscaping {boolean} — Отключить экранирование специальных символов?
		 */
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
		
		/**
		 * @property $templates {stdClass}
		 * @property $templates->wrapper {string}
		 * @property $templates->categories_item {string}
		 * @property $templates->offers_item {string}
		 * @property $templates->offers_item_elem {string}
		 * @property $templates->{'offers_item_elem' . $FieldName} {string}
		 */
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
		
		$categoryIds_last = []
	;
	
	private
		$outputter_StringInstance,
		$categoryIds = []
	;
	
	/**
	 * __construct
	 * @version 2.1.3 (2023-05-02)
	 * 
	 * @note @link https://yandex.ru/support/partnermarket/export/yml.html
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->shopData {stdClass} — Shop data. @required
	 * @param $params->shopData->shopName {string} — Короткое название магазина, не более 20 символов. @required
	 * @param $params->shopData->companyName {string} — Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней идентификации. @required
	 * @param $params->shopData->agency {string} — Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за работоспособность сайта. Default: —.
	 * @param $params->shopData->currencyId {string} — Currency code (https://yandex.ru/support/partnermarket/currencies.html). Default: 'RUR'.
	 * @param $params->shopData->platform {string} — Содержимое тега `<platform>`. Default: '(MODX) Evolution CMS'.
	 * @param $params->shopData->version {string} — Содержимое тега `<version>`. Default: '[(settings_version)]'.
	 * @param $params->offerFields {stdClass} — Offer fields parameters.
	 * @param $params->offerFields->price {stringTvName|''} — Поле, содержащее актуальную цену товара. @required
	 * @param $params->offerFields->priceOld {stringTvName} — Поле, содержащее старую цену товара (должна быть выше актуальной цены). Default: —.
	 * @param $params->offerFields->picture {stringTvName} — Поле, содержащее изображение товара. Defalut: —.
	 * @param $params->offerFields->name {stringDocFieldName|stringTvName} — Поле, содержащее модель товара. Default: 'pagetitle'.
	 * @param $params->offerFields->model {stringDocFieldName|stringTvName} — Поле, содержащее модель товара. Defalut: —.
	 * @param $params->offerFields->vendor {stringDocFieldName|stringTvName} — Поле, содержащее производителя товара. Defalut: —.
	 * @param $params->offerFields->available {stringDocFieldName|''} — Поле, содержащее статус товара ('true'|'false'). Default: '' (всегда выводить 'true').
	 * @param $params->offerFields->description {stringDocFieldName|stringTvName} — Поле, содержащее описание предложения (длина текста — не более 3000 символов). Default: —.
	 * @param $params->offerFields->salesNotes {stringDocFieldName|stringTvName} — Поле, содержащее «sales_notes» (https://yandex.ru/support/partnermarket/elements/sales_notes.html). Default: —.
	 * @param $params->offerFields->manufacturerWarranty {stringTvName} — Поле, содержащее наличие официальной гарантии производителя ('true'|'false'). Default: —.
	 * @param $params->offerFields->countryOfOrigin {stringDocFieldName|stringTvName} — Поле, содержащее страну производства товара. Default: —.
	 * @param $params->offerFields->homeCourierDelivery {stringTvName} — Поле, содержащее возможность курьерской доставки по региону магазина ('true'|'false'). Default: —.
	 * @param $params->offerFields->dimensions {stringDocFieldName|stringTvName} — Поле, содержащее габариты товара (длина, ширина, высота) в упаковке (размеры укажите в сантиметрах, формат: три положительных числа с точностью 0.001, разделитель целой и дробной части — точка, числа должны быть разделены символом «/» без пробелов). Default: —.
	 * @param $params->offerFields->weight {stringDocFieldName|stringTvName} — Поле, содержащее вес товара в килограммах с учетом упаковки (формат: положительное число с точностью 0.001, разделитель целой и дробной части — точка). Default: —.
	 * @param $params->offerFields->additionalParams {stringDocFieldName|stringTvName} — Поле, содержащее элементы «param» (https://yandex.ru/support/partnermarket/param.html). Default: —.
	 * @param $params->offerFields->customData {stringDocFieldName|stringTvName} — Поле, содержащее произвольный текст, который будет вставлен перед закрывающим тегом «</offer>». Default: —.
	 * @param $params->templates {stdClass} — Templates. Default: —
	 * @param $params->templates->wrapper {stringChunkName|string} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+]. Default: ''.
	 * @param $params->templates->categories_item {stringChunkName|string} — Available placeholders: [+id+], [+value+], [+parent+]. Default: '<category id="[+id+]"[+attrs+]>[+value+]</category>'.
	 * @param $params->templates->offers_item {stringChunkName|string} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: ''.
	 * @param $params->templates->{'offers_item_elem' . $FieldName} {stringChunkName|string} — Можно задать шаблон любого элемента offer, называем в соответствии с параметрами 'offerFields_', например: $params->templates_offers_item_elemCountryOfOrigin. Default: —.
	 * @param $params->categoryIds_last {stringCommaSepareted} — id конечных категорий(parent). Если пусто то выводятся только непосредственный родитель товара. Defalut: —. 
	 */
	public function __construct($params = []){
		$params = (object) $params;
		
		
		//# Prepare object fields
		$this->construct_prepareFields();
		
		
		//# Save shopData
		//If parameter is passed
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $params,
				'propName' => 'shopData'
			])
		){
			$this->shopData = \DDTools\ObjectTools::extend([
				'objects' => [
					$this->shopData,
					$params->shopData
				]
			]);
			
			//Remove from params to prevent overwriting through `$this->setExistingProps`
			unset($params->shopData);
		}
		
		
		//# Save offerFields
		foreach (
			$params->offerFields as
			$offerFieldName =>
			$offerFieldParamValue
		){
			//If parameter set as full data
			if (is_object($offerFieldParamValue)){
				$this->offerFields->{$offerFieldName} = \DDTools\ObjectTools::extend([
					'objects' => [
						$this->offerFields->{$offerFieldName},
						$offerFieldParamValue
					]
				]);
			//If parameter set as doc field name only
			}else{
				$this->offerFields->{$offerFieldName}->docFieldName = $offerFieldParamValue;
			}
		}
		
		//Remove from params to prevent overwriting through `$this->setExistingProps`
		unset($params->offerFields);
		
		//If name doc field is not set
		if (empty($this->offerFields->name->docFieldName)){
			//Pagetitle will be used
			$this->offerFields->name->docFieldName = 'pagetitle';
		}
		
		
		//# Call base constructor
		parent::__construct($params);
		
		
		//# Prepare last parent category IDs
		if (!is_array($this->categoryIds_last)){
			$this->categoryIds_last = explode(
				',',
				trim($this->categoryIds_last)
			);
		}
		
		
		//# Prepare templates
		$this->construct_prepareTemplates();
		
		
		//We use the “String” Outputter as base
		$outputter_StringParams = (object) [
			'templates' => (object) [
				'item' => $this->templates->offers_item,
				'wrapper' => $this->templates->wrapper
			]
		];
		//Transfer provider link
		if (isset($params->dataProvider)){
			$outputter_StringParams->dataProvider = $params->dataProvider;
		}
		$this->outputter_StringInstance = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => 'String',
			'params' => $outputter_StringParams
		]);
	}
	
	/**
	 * construct_prepareFields
	 * @version 1.0.2 (2022-09-20)
	 * 
	 * @return {void}
	 */
	private function construct_prepareFields(){
		//Convert fields to objects
		$this->shopData = (object) $this->shopData;
		$this->offerFields = (object) $this->offerFields;
		$this->templates = (object) $this->templates;
		foreach (
			$this->offerFields as
			$offerFieldName =>
			$offerFieldValue
		){
			$this->offerFields->{$offerFieldName} = (object) $this->offerFields->{$offerFieldName};
		}
		
		//Trim all templates
		foreach (
			$this->templates as
			$templateName =>
			$templateText
		){
			$this->templates->{$templateName} = trim($templateText);
		}
	}
	
	/**
	 * construct_prepareTemplates
	 * @version 1.0.1 (2024-07-13)
	 * 
	 * @return {void}
	 */
	private function construct_prepareTemplates(){
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
			'isCompletelyParsingEnabled' => false
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
			'isCompletelyParsingEnabled' => false
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
				'isCompletelyParsingEnabled' => false
			]);
		}
		
		//Prepare wrapper template
		$this->templates->wrapper = \ddTools::parseText([
			'text' => $this->templates->wrapper,
			'data' => $templateData,
			'isCompletelyParsingEnabled' => false
		]);
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
	 * @version 1.6.1 (2024-07-13)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		//# Items
		foreach (
			//Foreach all docs-items
			$data->provider->items as
			$docIndex =>
			//Value must be assigned by reference for modifying inside the cycle
			&$docData
		){
			//Correct price
			if (
				//Main price is not set
				empty($docData[$this->offerFields->price->docFieldName]) &&
				//But old price is set
				!empty($docData[$this->offerFields->priceOld->docFieldName])
			){
				//Use old price as main price
				$docData[$this->offerFields->price->docFieldName] = $docData[$this->offerFields->priceOld->docFieldName];
				
				//And old price is no needed
				unset($docData[$this->offerFields->priceOld->docFieldName]);
			}
			
			//Check required elements
			if (
				//Price
				!empty($docData[$this->offerFields->price->docFieldName])
			){
				//If category is set
				if (is_numeric($docData[$this->offerFields->categoryId->docFieldName])){
					//Save category ID
					$this->categoryIds[] = $docData[$this->offerFields->categoryId->docFieldName];
				}
				
				//Foreach all offer fields
				foreach(
					$this->offerFields as
					$offerFieldName =>
					$offerFieldData
				){
					//Smart offer name
					if (
						$offerFieldName == 'name' &&
						//If doc name is empty
						empty($docData[$offerFieldData->docFieldName])
					){
						//Pagetitle will be used
						$docData[$offerFieldData->docFieldName] = $docData['pagetitle'];
					}
					
					
					//Numeric fields
					if (
						//If weight is set
						$offerFieldName == 'weight' &&
						//But invalid
						$docData[$offerFieldData->docFieldName] == '0'
					){
						//Clear it
						$docData[$offerFieldData->docFieldName] = '';
					}
					
					
					if (
						//If object field is set
						!empty($offerFieldData->docFieldName) &&
						//And doc data is set
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
							$docData[$offerFieldData->docFieldName] =
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
							is_numeric($docData[$offerFieldData->docFieldName])
						){
							//Try to get pagetitle
							$docData_fieldToGet_data = \ddTools::getDocument(
								$docData[$offerFieldData->docFieldName],
								'pagetitle'
							);
							//If success
							if (is_array($docData_fieldToGet_data)){
								$docData[$offerFieldData->docFieldName] = $docData_fieldToGet_data['pagetitle'];
							}
						}
						
						
						//Value prefix
						if (isset($offerFieldData->valuePrefix)){
							$docData[$offerFieldData->docFieldName] =
								$offerFieldData->valuePrefix .
								$docData[$offerFieldData->docFieldName]
							;
						}
						//Value suffix
						if (isset($offerFieldData->valueSuffix)){
							$docData[$offerFieldData->docFieldName] .= $offerFieldData->valueSuffix;
						}
						
						
						//Try to search template by name
						$templateName =
							'offers_item_elem' .
							ucfirst($offerFieldName)
						;
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
							$docData[$offerFieldData->docFieldName] = \ddTools::parseText([
								'text' => $this->templates->{$templateName},
								'data' => [
									'tagName' => $offerFieldData->tagName,
									'value' =>
										//If escaping is disabled
										(
											\DDTools\ObjectTools::isPropExists([
												'object' => $offerFieldData,
												'propName' => 'disableEscaping'
											]) &&
											$offerFieldData->disableEscaping
										) ?
										//Unescaped value
										$docData[$offerFieldData->docFieldName] :
										//Escaped value
										$this->escapeSpecialChars($docData[$offerFieldData->docFieldName])
								],
								'isCompletelyParsingEnabled' => false
							]);
						}
					}
				}
			}else{
				//Remove invalid offers
				unset($data->provider->items[$docIndex]);
			}
		}
		
		//Destroy unused referenced variable
		unset($docData);
		
		$this->categoryIds = array_unique($this->categoryIds);
		
		
		//# Categories
		$this->outputter_StringInstance->placeholders['ddGetDocuments_categories'] = $this->parse_categories();
		
		
		//Just use the “String” class
		return $this->outputter_StringInstance->parse($data);
	}
	
	/**
	 * parse_categories
	 * @version 1.1.8 (2024-07-13)
	 *
	 * @return {string}
	 */
	private function parse_categories(){
		$result = '';
		
		$categoryIds_all = [];
		
		//TODO: Avoid to use global variables
		$getCategories = function ($id) use (
			&$categoryIds_all, 
			&$getCategories
		){
			$result = '';
			
			if(
				!in_array(
					$id, 
					$categoryIds_all
				)
			){
				$categoryIds_all[] = $id;
				
				//Get category doc data
				$categoryDocData = \ddTools::getDocument(
					//id
					$id,
					'pagetitle,id,parent',
					//published
					'all',
					//deleted
					0
				);
				
				$hasParentCategory = 
					//If root categories are set
					!empty($this->categoryIds_last) &&
					//And it is not one of the “root” category
					!in_array(
						$id, 
						$this->categoryIds_last
					)
				;
				
				$result .= \ddTools::parseText([
					'text' => $this->templates->categories_item,
					'data' => [
						'id' => $categoryDocData['id'],
						'value' => $this->escapeSpecialChars($categoryDocData['pagetitle']),
						'parent' => $categoryDocData['parent'],
						'attrs' =>
							$hasParentCategory ?
							' parentId="' . $categoryDocData['parent'] . '"' :
							''
					],
					'isCompletelyParsingEnabled' => false
				]);
				
				if($hasParentCategory){
					//Get parent category
					$result .= $getCategories($categoryDocData['parent']);
				}
			}
			
			return $result;
		};
		
		foreach(
			array_unique(array_merge(
				$this->categoryIds,
				$this->categoryIds_last
			)) as 
			$id
		){
			$result .= $getCategories($id);
		}
		
		return $result;
	}
}