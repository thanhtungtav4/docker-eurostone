<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/12/2018
 * Time: 17:11
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\WooCommerce;


use Illuminate\Contracts\View\View;
use WP_Term;
use WPCCrawler\Objects\File\MediaFile;
use WPCCrawler\PostDetail\Base\BasePostDetailTester;
use WPCCrawler\Utils;

class WooCommerceTester extends BasePostDetailTester {

    /** @var WooCommerceData */
    private $wooData;

    /**
     * Create tester view. This view will be shown in the test results in the Tester page. The view can be created
     * by using {@link Utils::view()} method. If the view is outside of the plugin, it can be created using a custom
     * implementation of {@link Utils::view()}. In that case, check the source code of the method. Variables available
     * for the general post test result view are available for this view as well. See {@link GeneralPostTest::createView()}
     * for available variables. '$detailData' variable that is the data for this factory will be injected to the view.
     * '$postData' variable that is an instance of {@link PostData} and can be used to reach main post data will also
     * be injected to the view.
     *
     * @return null|View Not-rendered blade view
     * @since 1.8.0
     */
    protected function createTesterView() {
        return Utils::view('post-detail.woocommerce.tester.main')->with([
            'tableData' => $this->createTableData()
        ]);
    }

    /*
     *
     */

    /**
     * Creates the variables that will be shown in a table.
     *
     * @return array A key-value pair array. Main keys are the section names. Under each main key is a key-value pair
     *               array. In this array, keys are names of the data, and the values are their content. See the method
     *               to understand the structure.
     * @since 1.8.0
     */
    private function createTableData() {
        $variables = [];

        /** @var WooCommerceData $wooData */
        $wooData = $this->getDetailData();
        $this->wooData = $wooData;

        // Main
        $variables[_wpcc('Main')] = [
            _wpcc('Product URL')       => $wooData->getProductUrl(),
            _wpcc('Button Text')       => $wooData->getButtonText(),
            _wpcc('Sale Price')        => $wooData->getSalePrice(),
            _wpcc('Regular Price')     => $wooData->getRegularPrice(),
            _wpcc('Product Type')      => $this->getProductTypeName(),
            _wpcc('Virtual?')          => $wooData->isVirtual(),
            _wpcc('Downloadable?')     => $wooData->isDownloadable(),
        ];

        // Downloads
        if ($wooData->isDownloadable()) {
            $variables[_wpcc('Downloads')] = [
                _wpcc('Downloadable File URLs') => $this->getDownloadableFileUrls(),
                _wpcc('Download Limit')         => $wooData->getDownloadLimit(),
                _wpcc('Download Expiry')        => $wooData->getDownloadExpiry(),
            ];
        }

        // Inventory.
        $variables[_wpcc('Inventory')] = [
            _wpcc('SKU')                => $wooData->getSku(),
            _wpcc('Manage stock?')      => $wooData->isManageStock(),
            _wpcc('Stock Quantity')     => $wooData->getStockQuantity(),
            _wpcc('Backorders')         => $this->getBackorders(),
            _wpcc('Low Stock Amount')   => $wooData->getLowStockAmount(),
            _wpcc('Stock Status')       => $this->getStockStatus(),
            _wpcc('Sold individually?') => $wooData->isSoldIndividually(),
        ];

        // Shipping. It is only available for non-virtual products.
        if (!$wooData->isVirtual()) {
            $variables[_wpcc('Shipping')] = [
                _wpcc('Weight')         => $wooData->getWeight(),
                _wpcc('Length')         => $wooData->getLength(),
                _wpcc('Width')          => $wooData->getWidth(),
                _wpcc('Height')         => $wooData->getHeight(),
                _wpcc('Shipping Class') => $this->getShippingClass(),
            ];
        }

        // Attributes
        $variables[_wpcc('Attributes')] = $this->getAttributes();

        // Advanced
        $variables[_wpcc('Advanced')] = [
            _wpcc('Purchase Note')   => $wooData->getPurchaseNote(),
            _wpcc('Enable reviews?') => $wooData->isEnableReviews(),
            _wpcc('Menu Order')      => $wooData->getMenuOrder(),
        ];

        return $variables;
    }

    /**
     * Prepares the selected product type's name
     *
     * @return string
     * @since 1.8.0
     */
    private function getProductTypeName() {
        $result = WooCommerceSettings::getProductTypeOptionsForSelect()[$this->wooData->getProductType()];
        return is_array($result) ? $result['name'] : $result;
    }

    /**
     * Prepares downloadable file URLs for presentation.
     *
     * @return array|null
     * @since 1.8.0
     */
    private function getDownloadableFileUrls() {
        if (!$this->wooData->getDownloadableMediaFiles()) return null;

        return array_map(function($mediaFile) {
            /** @var MediaFile $mediaFile */

            return Utils::view('site-tester.partial.attachment-item')
                ->with(['item' => $mediaFile])
                ->render();
        }, $this->wooData->getDownloadableMediaFiles());
    }

    /**
     * Prepares backorders for presentation.
     *
     * @return null|string
     * @since 1.8.0
     */
    private function getBackorders() {
        $bo = $this->wooData->getBackorders();
        if ($bo === null) return null;

        $options = WooCommerceSettings::getBackorderOptionsForSelect();
        return Utils::array_get($options, $bo);
    }

    /**
     * Prepares stock status for presentation.
     *
     * @return null|string
     * @since 1.8.0
     */
    private function getStockStatus() {
        $ss = $this->wooData->getStockStatus();
        if (!$ss) return null;

        $options = WooCommerceSettings::getStockStatusOptionsForSelect();

        return Utils::array_get($options, $ss, null);
    }

    /**
     * Prepares shipping class name for presentation
     *
     * @return string
     * @since 1.8.0
     */
    private function getShippingClass() {
        $classId = $this->wooData->getShippingClassId();
        $classTerm = get_terms([
            'taxonomy'   => 'product_shipping_class',
            'include'    => $classId,
            'number'     => 1,
            'hide_empty' => false,
        ]);

        if (is_wp_error($classTerm) || !$classTerm) return _wpcc('No shipping class');
        if (is_array($classTerm)) $classTerm = $classTerm[0];

        /** @var WP_Term $classTerm */
        return $classTerm->name;
    }

    /**
     * Prepares attributes for presentation.
     *
     * @return array
     * @since 1.8.0
     */
    private function getAttributes() {
        // If there is no attribute no need to show anything.
        $productAttributes = $this->wooData->getAttributes();
        if (!$productAttributes) return ['-' => '-'];

        $result = [];
        foreach($productAttributes as $attribute) {
            $name    = $attribute->getKey();
            $options = $attribute->getValues();

            // Indicate if an attribute is a taxonomy
            if ($attribute->isTaxonomy()) $name .= ' (' . _wpcc('As taxonomy') . ')';

            $result[$name] = $options;
        }

        return $result;
    }
}