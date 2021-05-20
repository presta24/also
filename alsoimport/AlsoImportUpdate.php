<?php
include_once dirname(__FILE__) . "/" . '../../config/config.inc.php';
include_once dirname(__FILE__) . "/" . '../../config/defines.inc.php';
include_once dirname(__FILE__) . "/" . '../../init.php';
include_once dirname(__FILE__) . '/classes/ALSO.php';
include_once dirname(__FILE__) . '/classes/also_dom.php';
include_once dirname(__FILE__) . '/classes/also-function.php';
require_once dirname(__FILE__) . '/controllers/admin/AlsoAdminImport.php';

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
ini_set('memory_limit', '1G');
ini_set("gd.jpeg_ignore_warning", 1);

define('ALSOIMPORT_CLIENTID', Configuration::get('ALSOIMPORT_CLIENTID'));
define('ALSOIMPORT_USERNAME', Configuration::get('ALSOIMPORT_USERNAME'));
define('ALSOIMPORT_PASSWORD', Configuration::get('ALSOIMPORT_PASSWORD'));
define('ALSOIMPORT_SCI', Configuration::get('ALSOIMPORT_SCI'));
define('ALSOIMPORT_URL', 'https://b2b.alsolatvia.lv/DirectXML.svc/2/scripts/XML_Interface.dll?');
define('ALSOIMPORT_URL_SIMPLE', 'https://b2b.actebis.com/invoke/ActDelivery_HTTP.Inbound/receiveXML_ALSO?');

$linktoken = "YOUR_TOKEN_GOES_HERE";
if ($linktoken != Tools::getValue('token')) {
 echo "Token error...";
 die();
}

//GetAlso filter information from parameter filter ID $id
function getAlsoFilter($id)
{
  //Also filter in prestashop
  $sql = '
        SELECT *
        FROM `' . _DB_PREFIX_ . 'alsoimport`
        WHERE `id_alsoimport` = "' . $id . '"
        ORDER BY `id_alsoimport` ASC';

  return $_also_filters = Db::getInstance()->executeS($sql);
}

//Get Also products list with filter parameter filter ID $id
function getAlsoFilteredProducts()
{
  // return $data;
  $xml = '<?xml version="1.0" encoding="windows-1257"?>
  <CatalogRequest>
  <Date>' . date("Y-m-d h:i") . '</Date>
  <Route>
  <From>
  <ClientID>' . ALSOIMPORT_CLIENTID . '</ClientID>
  </From>
  <To>
  <ClientID>2</ClientID>
  </To>
  </Route>
  <Filters>';
  foreach (getAlsoFilter(Tools::getValue('updateId')) as $filterElement) {
    //ClassID
    $ClassIds = explode(",", $filterElement['also_cat']);
    foreach ($ClassIds as $classid) {
      $xml .= '<Filter FilterID="ClassID" Value="' . $classid . '"/>';
    }
    //VendorID
    if (!empty($filterElement['brand'])) {
      $VendorIDs = explode(",", $filterElement['brand']);
      foreach ($VendorIDs as $vendorid) {
        $xml .= '<Filter FilterID="VendorID" Value="' . $vendorid . '"/>';
      }
    }
    $xml .= '<Filter FilterID="StockLevel" Value="' . $filterElement['stock'] . '"/>';
  }
  $xml .= '<Filter FilterID="WarehouseID" Value="1"/>
  </Filters>
  </CatalogRequest>';

  $_url = ALSOIMPORT_URL . 'USERNAME=' . ALSOIMPORT_USERNAME . '&PASSWORD=' . ALSOIMPORT_PASSWORD . '&XML=' . urlencode($xml);

  //Get product data from Also;
  $curl = curl_init($_url);
  curl_setopt($curl, CURLOPT_URL, $_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array(
    "content-type: text/xml",
  );
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

  $resp = curl_exec($curl);
  curl_close($curl);

  $_alsoProducts = simplexml_load_string($resp);
  return $_alsoProducts;
}

//Get Also product list with filter parameter filter ID $id
function getAlsoFilteredProduct($val)
{
  // return $data;
  $xml = '<?xml version="1.0" encoding="windows-1257"?>
  <ProductSpecRequest>
  <Date>' . date("Y-m-d h:i") . '</Date>
  <Route>
  <From>
  <ClientID>' . ALSOIMPORT_CLIENTID . '</ClientID>
  </From>
  <To>
  <ClientID>2</ClientID>
  </To>
  </Route>
   <Language>ENG</Language>
   <PartNumber>' . $val . '</PartNumber>
  </ProductSpecRequest>';

  $_urls = ALSOIMPORT_URL_SIMPLE . 'USERNAME=' . ALSOIMPORT_USERNAME . '&PASSWORD=' . ALSOIMPORT_PASSWORD . '&XML=' . urlencode($xml);

  //Get product data from Also;
  $curl = curl_init($_urls);
  curl_setopt($curl, CURLOPT_URL, $_urls);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array(
    "content-type: text/xml",
  );
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

  $resps = curl_exec($curl);
  curl_close($curl);

  $_alsoProduct = simplexml_load_string($resps);
  return $_alsoProduct;
}

//All data
Shop::setContext(Shop::CONTEXT_ALL);
$languages = Language::getLanguages();
//Also filter data
$defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
$shop_ids = Shop::getCompleteListOfShopsID();
$shop_is_feature_active = Shop::isFeatureActive();

//Add strart cron
getCronEnd(0, Tools::getValue('updateId'));
$_alsofiltre = getAlsoFilter(Tools::getValue('updateId'));

//Get products data
foreach (getAlsoFilteredProducts()->ListofCatalogDetails->CatalogItem as $key => $product) {

  $replaceFrom = "[^a-zA-Z0-9`\-\[\]',./~!@\$%\\^&*()_+|:\"?ąčęėįšųūžĄČĘĖĮŠŲŪŽ" . chr("ą") . chr("č") . chr("ę") . chr("ė") . chr("į") . chr("š") . chr("ų") . chr("ū") . chr("ž") . chr("Ą") . chr("Č") . chr("Ę") . chr("Ė") . chr("Į") . chr("Š") . chr("Ų") . chr("Ū") . chr("Ž") . "]";
  $replaceTo = " ";

  //Add Also supplier if not exist
  $supplier = Supplier::getIdByName('Also');
  if (empty($supplier)) {
    $supplier = new Supplier();
    $supplier->name = 'Also';
    $supplier->active = 1;
    $supplier->save();
    $supplier = $supplier->id_supplier;
  }

  //Global value
  $reference = $product->Product->PartNumber;
  
  //Qty
  $_stock = $product->Qty->QtyAvailable;

  //Create product
  $_AlsoItemStatus = getAlsoFilterInProduct($product->Product->PartNumber);

  //Get brand from Also
  foreach ($product->Product->Grouping->GroupBy as $keys => $brand) {
    if ($brand->attributes()->GroupID == 'ClassID') {
      continue;
    }

    $_brands = Db::getInstance()->getValue('SELECT `also_cat` FROM `' . _DB_PREFIX_ . 'alsoimport_tree` WHERE `also_cat_id` = "' . pSQL((int) $brand->attributes()->Value) . '"');
  }

  

  //Price
  $productRRP = $product->Price->UnitPrice;

  //Antkainis + RRP
  if ($productRRP < 10) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin1'] / 100);
    } elseif ($productRRP < 20) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin2'] / 100);
    } elseif ($productRRP < 30) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin3'] / 100);
    } elseif ($productRRP < 40) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin4'] / 100);
    } elseif ($productRRP < 50) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin5'] / 100);
    } elseif ($productRRP < 60) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin6'] / 100);    
    } elseif ($productRRP < 70) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin7'] / 100);    
    } elseif ($productRRP < 80) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin8'] / 100);    
    } elseif ($productRRP < 90) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin9'] / 100);    
    } elseif ($productRRP < 100) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin10'] / 100);    
    } elseif ($productRRP < 200) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin11'] / 100);   
    } elseif ($productRRP < 300) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin12'] / 100);    
    } elseif ($productRRP < 400) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin13'] / 100);    
    } elseif ($productRRP < 500) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin14'] / 100);    
    } elseif ($productRRP > 500) {
        $surchargeRRP = 1 + ($_alsofiltre[0]['margin15'] / 100);
    }

  //URL
  $product_url = Tools::link_rewrite(mb_substr(url_slug($_name . "-" . $product->Product->PartNumber), 0, 128));

  //Цены и скидки Also
  $price = number_format($productRRP * $surchargeRRP, 2, '.', '');
  $specialprice = number_format(($productRRP - $product['discountPrice']) * $surchargeRRP, 2, '.', '');

  $product_exists = Db::getInstance()->getValue('SELECT p.`id_product` FROM `' . _DB_PREFIX_ . 'product` p WHERE p.`reference` = "' . pSQL($reference) . '" AND p.id_supplier = "'.Supplier::getIdByName('Also').'"');

  if (empty($product_exists)) {
        $action = 'insert';
    } else {
        $action = 'update';
        $product_id = Db::getInstance()->getValue("SELECT id_product FROM " . _DB_PREFIX_ . "product p WHERE p.reference = '" . pSQL($reference) . "' AND p.id_supplier = '".Supplier::getIdByName('Also')."'");
    }


  if ($action == 'insert') {
        $p = new Product();
        //Name
        $name = mb_substr(trim_strrpos($product->Product->Description), 0, 128);
        $p->name = [$defaultLanguageId => $name];
        $p->description_short = [$defaultLanguageId => str_replace('|', ', ', $product->Product->LongDesc)];

        $p->reference = $reference;
        $p->price = round($price,6);
        $p->wholesale_price = $productRRP;
        $p->quantity = $_stock;
        $p->link_rewrite = [$defaultLanguageId => $product_url];
        $p->supplier_reference = $reference;
        $p->id_supplier = $supplier;
        $p->ean13 = mb_substr($product->Product->EANCode, 0, 13);

        $default_cat = explode(",", $_alsofiltre[0]['presta_cat']);
        $p->id_category_default = $default_cat[0];
        $p->id_category = $default_cat;
        $p->id_manufacturer = $manufacturer;
        $p->id_tax_rules_group = Configuration::get('ELKOIMPORT_TAXGROUP');
        $p->shop = getAlsoShop(Tools::getValue('updateId'));
        $p->id_shop_default = getAlsoShop(Tools::getValue('updateId'));

        // link product to shops
        $p->id_shop_list[] = $p->shop;

        if ($_stock != 0) {
            $p->active = 1;
            $p->available_for_order = 1;
            $p->visibility = 'both';
        } else {
            $p->active = 0;
            $p->available_for_order = 0;
            $p->visibility = 'none';
        }
        $p->indexed = 1;

        if($p->save()) {
            if ($supplier) {
                addSupplier($supplier, $p->id, $reference, round($price,6), Currency::getDefaultCurrency()->id);
            }
            $p->updateCategories($p->id_category);
            $p->addToCategories(explode(",", $_alsofiltre[0]['presta_cat']));
            //StockAvailable::setQuantity((int)$p->id, 0, $p->quantity, Context::getContext()->shop->id);
            StockAvailable::setQuantity((int)$p->id, 0, $p->quantity, Context::getContext()->shop->id);
           // $tag_list[] = str_replace(' ',',',$product['name']);
           // Tag::addTags($defaultLanguageId, $p->id, $tag_list);
                }

  // Create DOM from URL or file
  $html = file_get_html('http://cby.alsoactebis.com/cop/product/' . $product->Product->ProductID . '/' . ALSOIMPORT_SCI . '/datasheet.do');

  //Get attributes
  if ($html) {
    foreach ($html->find('table.properties .mspec') as $article) {
      if (empty($article->find('td.name', 0)->plaintext) or $article->find('td.name', 0)->plaintext == "Product Description" or $article->find('td.name', 0)->plaintext == "Product Type") {
        continue;
      }

 /**
       * Add features
       * if description file exist - add features
       */
       $replaceFrom = "[^a-zA-Z0-9`\-\[\]',./~!@\$%\\^&*()_+|:\"?ąčęėįšųūžĄČĘĖĮŠŲŪŽ" . chr("ą") . chr("č") . chr("ę") . chr("ė") . chr("į") . chr("š") . chr("ų") . chr("ū") . chr("ž") . chr("Ą") . chr("Č") . chr("Ę") . chr("Ė") . chr("Į") . chr("Š") . chr("Ų") . chr("Ū") . chr("Ž") . "]";
$replaceTo = " ";
      $feature_name = mb_strtoupper($article->find('td.name', 0)->plaintext, 'UTF-8');
      $feature_value = trim(mb_strtoupper(mb_substr(preg_replace("/\s{2,}/", " ", mb_ereg_replace($replaceFrom, $replaceTo, (string)$article->find('td.value', 0)->plaintext)), 0, 255), 'UTF-8'));
      $feature_value_replace = getReplaceFrom($feature_value);
      $feature_name = getReplaceFrom($feature_name);
      $position = isset($k) ? $k : false;
      if (isset($feature_value_replace) && !empty($feature_value_replace) && isset($feature_name) && !empty($feature_name)) {
        $id_feature = Feature::addFeatureImport($feature_name, $position);
        $id_feature_value = FeatureValue::addFeatureValueImport($id_feature, $feature_value_replace, $p->id, $defaultLanguageId);
        Product::addFeatureProductImport($p->id, $id_feature, $id_feature_value);
        // clean feature positions to avoid conflict
      }
      Feature::cleanPositions();

    $existImage = Db::getInstance()->getValue('
     SELECT count(`id_product`)
     FROM `' . _DB_PREFIX_ . 'image`
     WHERE `id_product` = ' . (int) $p->id, false);

 //Process image import
    $j = 0;
    $shops = 1;

      $url = $html->find('td.image img', 0)->src;

      $id_product = $p->id;
      $image = new Image();
      $image->id_product = $p->id;
      $image->position = $j + 1;
      $image->cover = $j == 0 ? 1 : 0;
      if ($existImage == "0") {
        $image->add();
      }

      if (($image->validateFields(false, true)) === true &&
        ($image->validateFieldsLang(false, true)) === true) {
        $productThumb = true;

        $image->associateTo($shops);
        if (!copyImg($p->id, $image->id, $url, 'products', $productThumb)) {
          $image->delete();
        }
      }
      $j++;
    }
  }

    echo 'Inserted product id: '.$p->id.' - '.$p->quantity.'<br />';

    } // end insert

if ($action == 'update') {

$p = new Product($product_id);
        $p->price = round($price,6);
        $p->wholesale_price = $productRRP;
        $p->link_rewrite = [$defaultLanguageId => $product_url];
        $p->supplier_reference = $reference;
        $p->id_supplier = $supplier;
        $p->ean13 = mb_substr($product->Product->EANCode, 0, 13);
        $p->id_manufacturer = $manufacturer;
        
        $p->id_tax_rules_group = Configuration::get('ELKOIMPORT_TAXGROUP');
        $p->shop = getAlsoShop(Tools::getValue('updateId'));
        $p->id_shop_default = getAlsoShop(Tools::getValue('updateId'));

        // link product to shops
        $p->id_shop_list[] = $p->shop;

        if ($_stock != 0) {
            $p->active = 1;
            $p->available_for_order = 1;
            $p->visibility = 'both';
        } else {
            $p->active = 0;
            $p->available_for_order = 0;
            $p->visibility = 'none';
        }
        $p->quantity = $_stock;
        $p->indexed = 1;

        if($p->save()) {
            if ($supplier) {
                addSupplier($supplier, $p->id, $reference, round($price,6), Currency::getDefaultCurrency()->id);
            }
            $p->updateCategories($p->id_category);
            $p->addToCategories(explode(",", $_alsofiltre[0]['presta_cat']));
            StockAvailable::setQuantity((int)$p->id, 0, $p->quantity, Context::getContext()->shop->id);
           // $tag_list[] = str_replace(' ',',',$product['name']);
           // Tag::addTags($defaultLanguageId, $p->id, $tag_list);
                }

  // Create DOM from URL or file
  $html = file_get_html('http://cby.alsoactebis.com/cop/product/' . $product->Product->ProductID . '/' . ALSOIMPORT_SCI . '/datasheet.do');

  //Get attributes
  if ($html) {
  $existImage = Db::getInstance()->getValue('
     SELECT count(`id_product`)
     FROM `' . _DB_PREFIX_ . 'image`
     WHERE `id_product` = ' . (int) $p->id, false);

 //Process image import
    $j = 0;
    $shops = 1;

      $url = $html->find('td.image img', 0)->src;

      $id_product = $p->id;
      $image = new Image();
      $image->id_product = $p->id;
      $image->position = $j + 1;
      $image->cover = $j == 0 ? 1 : 0;
      if ($existImage == "0") {
        $image->add();
      }

      if (($image->validateFields(false, true)) === true &&
        ($image->validateFieldsLang(false, true)) === true) {
        $productThumb = true;

        $image->associateTo($shops);
        if (!copyImg($p->id, $image->id, $url, 'products', $productThumb)) {
          $image->delete();
        }
      }
      $j++;
      }
       echo 'Updated product id: '.$p->id.'<br />';

    } // end update
}

//Add end cron
getCronEnd(1, Tools::getValue('updateId'));
getCronEndTime(date("Y-m-d H:i:s"),Tools::getValue('updateId'));


// Cron coplete or not
function getCronEnd($val, $filtreId)
{
//Also filter in prestashop
  $sql = 'UPDATE `' . _DB_PREFIX_ . 'alsoimport`
        SET `complete` = "' . $val . '"
        WHERE `id_alsoimport` = "' . $filtreId . '"';

  return Db::getInstance()->execute($sql);
}

function getAlsoShop($id)
{
  //Also shop filter in prestashop
  $sql = '
        SELECT `id_shop`
        FROM `' . _DB_PREFIX_ . 'alsoimport_shop`
        WHERE `id_alsoimport` = "' . $id . '"
        ORDER BY `id_alsoimport` ASC';

  return $_shopId = Db::getInstance()->getValue($sql);
}

function trim_strrpos($string, $length = 40, $trimmarker = '')
{
  $len = strlen(trim($string));
  $newstring = ($len > $length) ? rtrim(substr($string, 0, strrpos(substr($string, 0, $length), ' '))) . $trimmarker : $string;
  return $newstring;
}

//Also filter data in prestashop
function getAlsoFilterInProduct($id)
{

  $sql = 'SELECT skipname,skipstatus,skipdescr,skipimage,skipcat,skipstock,skipprice,skipseo
        FROM `' . _DB_PREFIX_ . 'product`
        WHERE `reference` = "' . $id . '"
        ORDER BY `id_product` ASC';

  return Db::getInstance()->getRow($sql);
}

/* functions from the core */

function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
{
  $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
  $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

  switch ($entity) {
    default:
    case 'products':
      $image_obj = new Image($id_image);
      $path = $image_obj->getPathForCreation();
      break;
    case 'categories':
      $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;
      break;
    case 'manufacturers':
      $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;
      break;
    case 'suppliers':
      $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;
      break;
  }

  $url = urldecode(trim($url));
  $parced_url = parse_url($url);

  if (isset($parced_url['path'])) {
    $uri = ltrim($parced_url['path'], '/');
    $parts = explode('/', $uri);
    foreach ($parts as &$part) {
      $part = rawurlencode($part);
    }
    unset($part);
    $parced_url['path'] = '/' . implode('/', $parts);
  }

  if (isset($parced_url['query'])) {
    $query_parts = array();
    parse_str($parced_url['query'], $query_parts);
    $parced_url['query'] = http_build_query($query_parts);
  }

  if (!function_exists('http_build_url')) {
    require_once _PS_TOOL_DIR_ . 'http_build_url/http_build_url.php';
  }

  $url = http_build_url('', $parced_url);

  $orig_tmpfile = $tmpfile;

  if (Tools::copy($url, $tmpfile)) {
// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
    if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
      @unlink($tmpfile);
      return false;
    }

    $tgt_width = $tgt_height = 0;
    $src_width = $src_height = 0;
    $error = 0;
    ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5,
      $src_width, $src_height);
    $images_types = ImageType::getImagesTypes($entity, true);

    if ($regenerate) {
      $previous_path = null;
      $path_infos = array();
      $path_infos[] = array($tgt_width, $tgt_height, $path . '.jpg');
      foreach ($images_types as $image_type) {
        $tmpfile = get_best_path($image_type['width'], $image_type['height'], $path_infos);

        if (ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'],
          $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5,
          $src_width, $src_height)) {
// the last image should not be added in the candidate list if it's bigger than the original image
          if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
            $path_infos[] = array($tgt_width, $tgt_height, $path . '-' . stripslashes($image_type['name']) . '.jpg');
          }
          if ($entity == 'products') {
            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg')) {
              unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg');
            }
            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg')) {
              unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg');
            }
          }
        }
        if (in_array($image_type['id_image_type'], $watermark_types)) {
          Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
        }
      }
    }
  } else {
    @unlink($orig_tmpfile);
    return false;
  }
  unlink($orig_tmpfile);
  return true;
}

function get_best_path($tgt_width, $tgt_height, $path_infos)
{
  $path_infos = array_reverse($path_infos);
  $path = '';
  foreach ($path_infos as $path_info) {
    list($width, $height, $path) = $path_info;
    if ($width >= $tgt_width && $height >= $tgt_height) {
      return $path;
    }
  }
  return $path;
}

//addSupplier
function addSupplier($id_supplier, $id_product, $reference, $price_te, $id_currency)
{
  //  foreach ($id_supplier as $value_sup) {
  $supplier_id_currency = $id_currency;
  $supplier_price_te = $price_te;
  $supplier_reference = $reference;
  $data = array(
    "id_product" => $id_product,
    "id_product_attribute" => 0,
    "id_supplier" => $id_supplier,
    "product_supplier_reference" => pSQL($supplier_reference),
    "product_supplier_price_te" => $supplier_price_te,
    "id_currency" => $supplier_id_currency
  );
  Db::getInstance()->insert("product_supplier", $data, false, true, DB::REPLACE);
  //}
}

function getCronEndTime($time, $id)
{
 //Elko add end cron time
 $sql = 'UPDATE `' . _DB_PREFIX_ . 'alsoimport` SET  `cron_date` = "'.pSQL($time).'" WHERE `id_alsoimport` = "'.pSQL($id).'"';
 return Db::getInstance()->execute($sql);

}