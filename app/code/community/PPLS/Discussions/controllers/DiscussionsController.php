<?php
class PPLS_Discussions_DiscussionsController extends Mage_Core_Controller_Front_Action
{
/**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_designProductSettingsApplied = array('post');

	

	public function listAction()
	{



	       if ($product = $this->_initProduct()) {
		    Mage::register('productId', $product->getId());

		    $design = Mage::getSingleton('catalog/design');
		    $settings = $design->getDesignSettings($product);
		    if ($settings->getCustomDesign()) {
		        $design->applyCustomDesign($settings->getCustomDesign());
		    }
		    $this->_initProductLayout($product);

		    // update breadcrumbs
		    if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
		        $breadcrumbsBlock->addCrumb('product', array(
		            'label'    => $product->getName(),
		            'link'     => $product->getProductUrl(),
		            'readonly' => true,
		        ));
		        $breadcrumbsBlock->addCrumb('discussions', array('label' => Mage::helper('review')->__('Product Discussions')));
		    }
		$model = Mage::getSingleton('discussions/discussions');
		$model->setFrom(0);
		$model->setRows(10);
		$model->setSort('relevance_score');
		$model->setFilters('no');
		$model->setProductName($product->getName());

		    $this->renderLayout();
		} elseif (!$this->getResponse()->isRedirect()) {
		    $this->_forward('noRoute');
		}



	}




     /**
     * Initialize requested product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);



        return Mage::helper('catalog/product')->initProduct($productId, $this, $params);
    }

    /**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        /* @var $product Mage_Catalog_Model_Product */
        if (!$product->getId() || !$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
            return false;
        }

        Mage::register('current_product', $product);
        Mage::register('product', $product);

        return $product;
    }
   /**
     * Load specific layout handles by product type id
     *
     */
    protected function _initProductLayout($product)
   {
        Mage::helper('catalog/product_view')->initProductLayout($product, $this);
        return $this;
    }

   /**
     * Recursively apply custom design settings to product if it's container
     * category custom_use_for_products option is setted to 1.
     * If not or product shows not in category - applyes product's internal settings
     *
     * @deprecated after 1.4.2.0-beta1, functionality moved to Mage_Catalog_Model_Design
     * @param Mage_Catalog_Model_Category|Mage_Catalog_Model_Product $object
     * @param Mage_Core_Model_Layout_Update $update
     */
    protected function _applyCustomDesignSettings($object, $update)
    {
        if ($object instanceof Mage_Catalog_Model_Category) {
            // lookup the proper category recursively
            if ($object->getCustomUseParentSettings()) {
                $parentCategory = $object->getParentCategory();
                if ($parentCategory && $parentCategory->getId() && $parentCategory->getLevel() > 1) {
                    $this->_applyCustomDesignSettings($parentCategory, $update);
                }
                return;
            }

            // don't apply to the product
            if (!$object->getCustomApplyToProducts()) {
                return;
            }
        }

        if ($this->_designProductSettingsApplied) {
            return;
        }

        $date = $object->getCustomDesignDate();
        if (array_key_exists('from', $date) && array_key_exists('to', $date)
            && Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])
        ) {
            if ($object->getPageLayout()) {
                $this->_designProductSettingsApplied['layout'] = $object->getPageLayout();
            }
            $this->_designProductSettingsApplied['update'] = $object->getCustomLayoutUpdate();
        }
    }


    /**
     * View product gallery action
     */
    public function galleryAction()
    {
        if (!$this->_initProduct()) {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display product image action
     *
     * @deprecated
     */
    public function imageAction()
    {
        /*
         * All logic has been cut to avoid possible malicious usage of the method
         */
        $this->_forward('noRoute');
    }


}
