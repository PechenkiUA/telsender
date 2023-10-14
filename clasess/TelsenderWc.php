<?php

namespace pechenki\Telsender\clasess;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class TelsenderWc
 * @package pechenki\Telsender\clasess
 */
class TelsenderWc
{
    /**
     * @var $replace :array
     * @var $order :object
     * @var $order_id :integer
     * @var $status_accses :array
     */

    public $replace = array();

    public $order;
    public $order_id;
    public $status_accses = array();

    /**
     * TelsenderWc constructor.
     * @param integer $order_id
     */
    function __construct(int $order_id)
    {
        $this->order = wc_get_order($order_id);
        $this->order_id = $order_id;
        add_filter('tscf_filter_codetemplate', array($this, 'tscf_dew_temlate'), 10, 2);

    }

    /**
     * @param string $str
     * @return string
     */
    public function getBillingDetails(string $str): string
    {
        $this->decodeShortcode($str);
        $pr = $this->Products();
        $str = str_replace(array_keys($pr), array_values($pr), $str);
        return str_replace(array_keys($this->replace), array_values($this->replace), $str);
    }

    /**
     * @return bool
     */
    public function isStatusAccsec(): bool
    {
        $list = $this->status_accses;
        $status = 'wc-' . $this->order->status;
        if (in_array($status, $list)) {
            return true;
        }
        return false;
    }


    /**
     * @param string $str
     */
    private function decodeShortcode(string $str)

    {

        $re = '/\{.+?}/m';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        array_walk_recursive($matches, function ($item, $key) {

            $ed = explode('-', preg_replace('/\{|\}/', '', $item));

            if (count($ed) > 1) {

                $this->replace[$item] = (string)$this->order->data[$ed[0]][$ed[1]];

            } else {

                $res = preg_replace('/\{|\}/', '', $item);

                    $_result = $this->order->data[$res];
                    if ($_result) {
                        $this->replace[$item] = $_result;
                    } else {
                        $this->replace[$item] = $this->order->get_meta($res) ?: '';
                    }



            }

        });

        $this->replace = apply_filters('tscf_filter_codetemplate', $this->replace, $this->order_id);
    }

    /**
     * @return array
     */
    public function Products(): array
    {
        $items = $this->order->get_items();
        $curents = get_woocommerce_currency_symbol();

        $product = '';
        $product_v2 = '';
        $product_v3  = '';
        foreach ($items as $item) {

            $metaProduct = $item->get_formatted_meta_data();
            $metaText = '';

            foreach ($metaProduct as $key => $value) {
                $metaText .= $value->display_key . ' : ' . $value->value . chr(10);
            }

            $product_item = $item->get_product();
            if ($product_item) {
                $sku = $product_item->get_sku();
                $product .= $item['name'] . ' x' . $item['quantity'] . ' ' . $item['total'] . $curents . chr(10);
                $product_v2 .= $item['name'] . ' x' . $item['quantity'] . ' ' . $item['total'] . $curents . ' sku(' . $sku . ')' . chr(10);
                $product_v3 .= $item['name'] . ' x' . $item['quantity'] . ' ' . $item['total'] . $curents . ' ' . $metaText . chr(10);
            }

        }

        $return['{products}'] = $product;
        $return['{products_v2}'] = $product_v2;
        $return['{products_v3}'] = $product_v3;


        $shop = $this->order->get_items('shipping');
        if ($shop) {
            $shipping = end($shop)->get_data();
            $return['{shipping_method_title}'] = $shipping['method_title'];
        }

        return $return;
    }

    /**
     * @param array $replace
     * @return array
     */

    public function tscf_dew_temlate(array $replace): array
    {

        $replace['{order_n}'] = $this->order_id;
        $replace['{order_date}'] = wp_date('Y-m-d');
        $replace['{order_time}'] = wp_date('G:i:s');
        return $replace;
    }

}
