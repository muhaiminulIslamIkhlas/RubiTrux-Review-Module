<?php

namespace Harriswebworks\feeforeview\Helper;

use Magento\Framework\App\Helper\Context;
use Harriswebworks\feeforeview\Model\ItemFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

    protected $_dataExample;
    protected $productCollectionFactory;
    protected $productRepository;

    public function __construct(
            Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            ItemFactory $dataExample,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->_dataExample = $dataExample;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    public function saveTODatabase($jsonData, $product_id) {

        print_r($jsonData);
        $additional_param = array();
        $ratingValue = "";
        $reviewId = "";
        $review_data = "";
        $reviewUrl = "";
        $customerDisplayName = "";
        $createdAt = "";
        $lastUpdatedAt = "";

        $review = $jsonData[0]['reviews'];

        foreach ($review as $item) {

            if (isset($item['products']['review'])) {
                $review_data = $item['products']['review'];
            } else {
                $review_data = "No data found";
            }


            if (isset($item['last_updated_date'])) {
                $lastUpdatedAt = $item['last_updated_date'];
            } else {
                $lastUpdatedAt = "No data found";
            }

            if (isset($item['products']['created_at'])) {
                $createdAt = $item['products']['created_at'];
            } else {
                $createdAt = "No data found";
            }

            if (isset($item['customer'])) {
                $customerDisplayName = $item['customer']['display_name'];
            } else {
                $customerDisplayName = "No data found";
            }

            if (isset($item['url'])) {
                $reviewUrl = $item['url'];
            } else {
                $reviewUrl = "No data found";
            }

            if (isset($item['products'][0]['id'])) {
                $reviewId = $item['products'][0]['id'];
            } else {
                $reviewId = "No data found";
            }

            if (isset($item['products'][0]['rating']['rating'])) {
                $ratingValue = $item['products'][0]['rating']['rating'];
            } else {
                $ratingValue = "No data found";
            }



            if (isset($item['products_purchased'])) {
                $additional_param['products_purchased'] = $item['products_purchased'];
            } else {
                $additional_param['products_purchased'] = "No data found";
            }

            if (isset($item['social'])) {
                $additional_param['social'] = $item['social'];
            } else {
                $additional_param['social'] = "No data found";
            }

            $additionalParam = json_encode($additional_param);

            $model = $this->_dataExample->create();
            $model->addData([
                "product_id" => $product_id,
                "rating_value" => $ratingValue,
                "review_id" => $reviewId,
                "review" => $review_data,
                "reviews_url" => $reviewUrl,
                "customer_display_name" => $customerDisplayName,
                "created_at" => $createdAt,
                "last_updated_date" => $lastUpdatedAt,
                "additional_param" => $additionalParam
            ]);
            $saveData = $model->save();
        }
    }

    public function fetchData($product_id) {
        $url = "https://api.feefo.com/api/10/importedreviews/product?page=1&page_size="
                . "10&since_period=ALL&full_thread=include&unanswered_feedback=include&source="
                . "on_page_product_integration&sort"
                . "=-created_date&feefo_parameters=include&media=include&merchant_identifier"
                . "=rubitrux&origin=www.rubitrux.com&parent_product_sku=" . $product_id;

        $parts = parse_url($url);

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $jsonData = json_decode(curl_exec($curlSession), true);
        curl_close($curlSession);

        $jsonData = array($jsonData);
        return $jsonData;
    }
    
    public function test(){
        return "Hello world";
    }

}
