<?php
 
class PPLS_Discussions_Model_Observer
{
/*This observer listening to event of loading product price block , could be on catalog/product page
The flow:   product_price load event=> insertBlock observer => virality.phtml => initVirality of virality block=> get virality counts from session stored by  getDiscussionCounts (see below function)
*/
     public function insertBlock($observer)
    {
        /** @var $_block Mage_Core_Block_Abstract */
        /*Get block instance*/
	if(Mage::getStoreConfig('advanced/modules_disable_output/PPLS_Discussions'))
	{
		return;
	}
		$_block = $observer->getBlock();
		/*get Block type*/
		$_type = $_block->getType();
		$_template = $_block->getTemplate();
		$virality_block=$_block->getLayout()->createBlock('discussions/virality');
		$virality_block->setTemplate('discussions/virality.phtml');
	       /*Check block type*/
		if ($_type == 'catalog/product_price' && $_template!='catalog/product/view/tierprices.phtml') {
		    /*Clone block instance*/
		    $_child = clone $_block;
		    /*set another type for block*/
		    $_child->setType('discussions/block');
		    /*set child for block*/
		    $_block->setChild('child', $_child);
		    /*set our template*/
		    $_block->setTemplate('discussions/virality.phtml');
		    $_block->setChild('virality',$virality_block);
		}
	
    }


/* Observer listening to event of product collection load- usually loaded on catalog view page
When list of products loaded we want to calculate vitality count and power of each product.
The flow: observer => discussions model => store on session 
The output of generateDiscussionCounts is stored on session as map of product id and pair of (count and power)
When product block is loaded the insertBlock event is fired ->see the above event description  
Note:session in this case (php limit) is stateless and request limited => each request will refresh counts even if it's pagenation
*/

     public function getDiscussionCounts(Varien_Event_Observer $observer)
    {
	if(Mage::getStoreConfig('advanced/modules_disable_output/PPLS_Discussions'))
	{
		return;
	}
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof Varien_Data_Collection) {
            $productCollection->load();
            Mage::getModel('discussions/discussions')->generateDiscussionCounts($productCollection);
        }

        return $this;
	
    }
}
