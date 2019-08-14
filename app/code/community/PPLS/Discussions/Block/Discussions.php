<?php
class PPLS_Discussions_Block_Discussions extends Mage_Core_Block_Template
{  

    public function __construct()
    {
       $this->setTemplate('discussions/discussions.phtml');
       parent::__construct();
    }
 	
    public function getDiscussions()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$searchParams =$model->getSearchParams();
	if(null==$searchParams)
	{
		$myProductName =$model->getProductName();
		$searchParams="{\"item_name\":\"".$myProductName."\"}";
	}

	return $model->getDiscussionsVer1($searchParams);

    }
    public function getURLparams()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$from =$model->getFrom();
	$rows =$model->getRows();
	$sort =$model->getSort();
	$filters =$model->getFilters();
	$myProductName =$model->getProductName();

	return "?productName=".$myProductName."&from=".$from."&rows=".$rows."&sort=".$sort."&filters=".$filters;

    }

   public function getProductName()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$myProductName =$model->getProductName();

	return $myProductName;

    }

   public function getDiscussionByID()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$discussionParams =$model->getDiscussionParams();

	return $model->getDiscussionById($discussionParams);

    }
 

   public function sendFeedback()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$feedbackParams =$model->getFeedbackParams();

	return $model->sendFeedback($feedbackParams);

    }



}


