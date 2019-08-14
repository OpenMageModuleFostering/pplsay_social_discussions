<?php
class PPLS_Discussions_Block_Virality extends Mage_Core_Block_Template
{
	public $virality_base_url ="";
	public $count =0;
	public $power =0;
	public $debug ="use to debug, print from vitality.phtml";

	public function __construct()
	{
		parent::__construct();


	}


	public function getLink()
	{
		return "$this->virality_base_url{$this->power}.png";
	}

	public function initVirality($product)
	{
		$productID = $product->getId();
		$model = Mage::getModel('discussions/virality');
		$this->virality_base_url = Mage::getModel('core/variable')->loadByCode('ppls_cdn_url')->getData('store_plain_value')."/images/virality_";
		$thisProductDiscussionCount=null;
		$discussionCounts = Mage::getSingleton('core/session')->getDisccussionCounts();
		if($discussionCounts==null)
		{
			$thisProductDiscussionCount= $model->initVirality($product);
		}
		else
		{
			$countsContent = $discussionCounts["content"];
			$thisProductDiscussionCount = $countsContent[$productID];

		}

		if($thisProductDiscussionCount["status"]==4 && $discussionCounts==null)
		{
			$this->sendToDiscovery($product,$model);
			return;

		}
		else
		{
			if($thisProductDiscussionCount["status"]==0)
			{
				$content = $thisProductDiscussionCount["content"];
				$this->count=$content["count"];
				$this->power=$content["power"];
			}
		}
	}

	private function sendToDiscovery($product,$model)
	{

		$productID = $product->getId();

		//check if the item was already sent for discovery.
		//After sending to discovery we will store it on cookie
		if(Mage::getModel('core/cookie')->get($productID)==null)
		{
			$productBrand = $product->getAttributeText('manufacturer');
			$productCategory = "";
			$productName = $product->getName();

			$categoryIds = $product->getCategoryIds();
			if(count($categoryIds) ){
				$firstCategoryId = $categoryIds[0];
				$_category = Mage::getModel('catalog/category')->load($firstCategoryId);
				$productCategory=$_category->getName();
			}
			$model->discoverItem($productID,$productName,$productBrand,$productCategory);
//			$this->debug = $model->discoverItem($productID,$productName,$productBrand,$productCategory);
			Mage::getModel('core/cookie')->set($productID,1);
		}

	}

	public function getProductUrl($productId)
	{
		$_product = Mage::getModel('catalog/product')->load($productId);
		$_categories = $_product->getCategoryIds();
		return Mage::getBaseUrl()."discussions/discussions/list/id/".$productId."/category/".$_categories[0];
	}
}
