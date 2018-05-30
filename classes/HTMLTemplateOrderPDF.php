<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HTMLTemplateOrderPDF extends HTMLTemplateCore
{
    public $smarty;
    public $id_lang;
    public $id_shop;
    private $id_order;
    private $order;
    private $payments;
    private $customer;
    private $address_invoice;
    private $address_delivery;
    private $pdf_folder;
    
    public function __construct($id_order, $smarty, $pdf_folder)
    {
        $this->id_order = (int)$id_order;
        $this->smarty = $smarty;
        $this->pdf_folder = $pdf_folder;
        
        if ($this->id_order == 0) {
            return false;
        }
        $this->order = new OrderCore($this->id_order);
        $payments = $this->order->getOrderPayments();
        $this->payments = array();
        foreach ($payments as $payment) {
            $this->payments[] = array(
                'order_reference' => $payment->order_reference,
                'amount' => Tools::displayPrice($payment->amount),
                'payment_method' => $payment->payment_method,
            );
        }
        $this->customer = new CustomerCore($this->order->id_customer);
        $this->address_delivery = $this->getAddressDelivery();
        $this->address_invoice = $this->getAddressInvoice();
        $this->id_lang = Context::getContext()->language->id;
        $this->id_shop = Context::getContext()->shop->id;
    }
    
    public function getBulkFilename()
    {
        //nothing
    }

    public function getContent()
    {
        $this->smarty->assign(array(
            'order' => $this->order,
            'address_invoice' => $this->address_invoice,
            'address_delivery' => $this->address_delivery,
            'products' => $this->getProducts(),
            'payments' => $this->getPayments(),
            'messages' => $this->getMessages(),
        ));
        $content = $this->smarty->fetch($this->getTemplatePath('order.pdf'));
        return $content;
    }

    public function getFilename()
    {
        return 'order_' . $this->id_order . '.pdf';
    }
    
    public function getHeader()
    {
        $ps_logo = ConfigurationCore::get('PS_LOGO');
        $shop = new ShopCore(Context::getContext()->shop->id);
        $shop_logo = $shop->getBaseURL(true).'img/'.$ps_logo;
        $this->smarty->assign(
            array(
                'logo_path' => $shop_logo,
                'order' => $this->order,
                'payments' => $this->getPayments(),
            )
        );
        $content = $this->smarty->fetch($this->getTemplatePath('order.header.pdf'));
        return $content;
    }
    
    public function getFooter()
    {
        $footer = $this->smarty->fetch($this->getTemplatePath('order.footer.pdf'));
        return $footer;
    }
    
    private function getAddressDelivery()
    {
        $id_address = $this->order->id_address_delivery;
        $address = new Address($id_address);
        $state = new StateCore($address->id_state);
        
        $output = '';
        if ($address->company) {
            $output .= '<strong>' . $address->company . '</strong><br>';
        } else {
            $output .= '<strong>' . $address->firstname . ' ' . $address->lastname . '</strong><br>';
        }
        if ($address->address1) {
            $output .= $address->address1 . '<br>';
        }
        if ($address->address2) {
            $output .= $address->address2 . '<br>';
        }
        $output .= $address->postcode . ' - ' . $address->city . '<br>';
        if ($state->name) {
            $output .= $state->name . ' (<strong>' . $state->iso_code . '</strong>)<br>';
        }
        $output .= Tools::strtoupper($address->country) . '<br>';
        if ($address->phone_mobile && $address->phone) {
            $output .= $this->l('Phone') . ': ' . $address->phone_mobile;
        } elseif ($address->phone_mobile) {
            $output .= $this->l('Phone') . ': ' . $address->phone_mobile;
        } elseif ($address->phone) {
            $output .= $this->l('Phone') . ': ' . $address->phone;
        }
        
        return $output;
    }
    
    private function getAddressInvoice()
    {
        $id_address = $this->order->id_address_invoice;
        $address = new Address($id_address);
        $state = new StateCore($address->id_state);
        
        $output = '';
        if ($address->company) {
            $output .= '<strong>' . $address->company . '</strong><br>';
        } else {
            $output .= '<strong>' . $address->firstname . ' ' . $address->lastname . '</strong><br>';
        }
        if ($address->address1) {
            $output .= $address->address1 . '<br>';
        }
        if ($address->address2) {
            $output .= $address->address2 . '<br>';
        }
        $output .= $address->postcode . ' - ' . $address->city . '<br>';
        if ($state->name) {
            $output .= $state->name . ' (<strong>' . $state->iso_code . '</strong>)<br>';
        }
        $output .= Tools::strtoupper($address->country) . '<br>';
        if ($address->vat_number) {
            $output .= $this->l('Vat number') . ': <strong>' . $address->vat_number . '</strong><br>';
        } else {
            $output .= $this->l('DNI') . ': <strong>' . $address->dni . '</strong><br>';
        }
        
        return $output;
    }
    
    private function getProducts()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql->select('product_id')
            ->select('product_attribute_id')
            ->select('product_quantity')
            ->select('product_quantity_in_stock')
            ->select('product_ean13')
            ->select('product_reference')
            ->select('total_price_tax_incl')
            ->select('total_price_tax_excl')
            ->select('unit_price_tax_incl')
            ->select('unit_price_tax_excl')
            ->select('original_product_price')
            ->select('original_wholesale_price')
            ->from('order_detail')
            ->where('id_order=' . (int)$this->order->id)
            ->orderBy('id_order_detail');
        //PrestaShopLoggerCore::addLog('getProducts: ' . $sql->__ToString());
        $details = $db->executeS($sql);
        if ($details) {
            $output = array();
            foreach ($details as $detail) {
                $id_product = (int)$detail['product_id'];
                $id_product_attribute = (int)$detail['product_attribute_id'];
                $tax_rate = $this->getTaxRate($id_product);
                $original_price = $detail['original_product_price'] * (100+$tax_rate) / 100;
                $unit_price_tax_incl = $detail['unit_price_tax_incl'];
                $discount = (($original_price - $unit_price_tax_incl) / $original_price) * 100;
                $output[] = array(
                    'product_id' => $id_product,
                    'product_id_attribute' => $id_product_attribute,
                    'product_name' => $this->getProductName($id_product_attribute, $id_product),
                    'product_quantity' => $detail['product_quantity'],
                    'product_stock' => $detail['product_quantity_in_stock'],
                    'product_price' => $original_price,
                    'product_price_discount' => $discount,
                    'product_ean13' => $detail['product_ean13'],
                    'product_reference' => $detail['product_reference'],
                    'unit_price_tax_incl' => $unit_price_tax_incl,
                    'total_price_tax_incl' => $detail['total_price_tax_incl'],
                    'image_url' => $this->getImageProduct($detail['product_id']),
                    'customization' => $this->getCustomization($id_product, $id_product_attribute),
                );
            }
            return $output;
        } else {
            return array();
        }
    }
    
    public function getTaxRate($id_product)
    {
        $product = new ProductCore($id_product);
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('t.rate')
                ->from('tax', 't')
                ->innerJoin('tax_rule', 'tr', 'tr.id_tax=t.id_tax')
                ->where('tr.id_tax_rules_group = ' . (int)$product->id_tax_rules_group);
        
        $tax_rate = (float)$db->getValue($sql);
        return $tax_rate;
    }
    
    public function getProductName($id_product_attribute, $id_product)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $id_lang = Context::getContext()->language->id;
        $sql->select('id_attribute')
            ->from('product_attribute_combination')
            ->where('id_product_attribute = ' . (int)$id_product_attribute);
        $product = new ProductCore((int)$id_product);
        $name = $product->name[(int)$id_lang];
        $attributes = $db->executeS($sql);
        foreach ($attributes as $attribute) {
            $attr = new AttributeCore($attribute['id_attribute']);
            $name .= ' ' . $attr->name[(int)$id_lang];
        }
        
        return $name;
    }
    
    private function getCustomization($id_product, $id_product_attribute)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $output = array();
        $sql->select('id_customization')
            ->from('customization')
            ->where('id_cart='.(int)$this->order->id_cart)
            ->where('id_product='.(int)$id_product)
            ->where('id_product_attribute='.(int)$id_product_attribute);
        $result = $db->executeS($sql);
        
        if ($result) {
            foreach($result as $id) {
                $sql = new DbQueryCore();
                $sql->select('*')
                    ->from('customized_data')
                    ->where('id_customization='.(int)$id['id_customization']);
                $custom = $db->executeS($sql);
                if ($custom) {
                    foreach($custom as $custom_data) {
                        $row = array(
                            'id_customization' => $custom_data['id_customization'],
                            'type' => $custom_data['type'],
                            'index' => $custom_data['index'],
                            'title' => $this->getCustomizationTitle($custom_data['index']),
                            'value' => $this->getCustomizationValue($custom_data['value'], $custom_data['type']),
                        );
                        $output[] = $row;
                    }
                }
            }
            return $output;
        }
        return array();
    }
    
    public function getCustomizationTitle($index)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql->select('name')
            ->from('customization_field_lang')
            ->where('id_customization_field='.(int)$index)
            ->where('id_lang='.(int)$this->id_lang);
        $value = $db->getValue($sql);
        return $value;
    }
    
    public function getCustomizationValue($value, $type)
    {
        
        if ($type == 0) {
            $value = $this->getImageCustomization($value);
        } else {
            $value =  $value;
        }
        return $value;
    }
    
    private function getMessages()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('n.*')
            ->select('concat(e.firstname,\' \', e.lastname) as employee')
            ->from('mp_customer_order_notes','n')
            ->leftJoin('employee', 'e', 'e.id_employee=n.id_employee')
            ->where('n.id_order = ' . (int)$this->order->id)
            ->where('n.deleted = 0')
            ->where('n.printable = 1')
            ->where('n.id_lang = '.(int)$this->id_lang)
            ->where('n.id_shop = '.(int)$this->id_shop)
            ->orderBy('n.date_add DESC');
        $result = $db->executeS($sql);
        if ($result) {
            $output = array();
            foreach ($result as $row) {
                $output[] = array(
                    'date' => $row['date_add'],
                    'employee' => $row['employee'],
                    'content' => $row['content'],
                );
            }
            return $output;
        } else {
            return array();
        }
    }
    
    private function getPayments()
    {
        $carrier = new CarrierCore((int)$this->order->id_carrier);
        return array(
            'payment_method' => $this->order->payment,
            'carrier' => $carrier->name,
            'total_order' => $this->order->total_paid_tax_incl,
        );
    }
    
    private function getTemplatePath($template)
    {
        return $this->pdf_folder . $template . '.tpl';
    }

    private function getImageProduct($id_product)
    {
        $shop = new ShopCore(Context::getContext()->shop->id);
        $product = new ProductCore((int)$id_product);
        $images = $product->getImages(Context::getContext()->language->id);

        foreach ($images as $obj_image) {
            $image = new ImageCore((int)$obj_image['id_image']);
            if ($image->cover) {
                return $shop->getBaseURL(true) . 'img/p/'. $image->getExistingImgPath() . '.jpg';
            }
        }
        return '';
    }
    
    private function getImageCustomization($value)
    {
        $shop = new ShopCore(Context::getContext()->shop->id);
        return $shop->getBaseURL(true) . 'upload/'. $value . '_small';
    }

    public function test()
    {
            return "<h1>HELLO WORLD<h1>";
    }
}
