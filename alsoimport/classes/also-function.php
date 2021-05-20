<?php

/* Get replace value other */
function getReplaceFrom($value)
{
  $sql = Db::getInstance()->getValue('SELECT `replace_to`
    FROM `' . _DB_PREFIX_ . 'import_replace`
    WHERE `replace_from` = "' . pSQL($value) . '" AND `active` = "1"
    ORDER BY `id_import_replace` ASC');

  if (empty($sql)) {
    return trim(mb_strtoupper($value, 'UTF-8'));
  } else {
    return trim(mb_strtoupper($sql, 'UTF-8'));
  }
}

/* Get replace Manufacturer value */
function getReplaceBrandValue($value)
{

  $sql = Db::getInstance()->getValue('SELECT `replace_to`
    FROM `' . _DB_PREFIX_ . 'import_replace`
    WHERE `replace_from` = "' . pSQL($value) . '" AND `active` = "1"
    ORDER BY `id_import_replace` ASC');
  $_replaceToNewManufacturer = trim(mb_strtoupper($sql, 'UTF-8'));

//Get ID old features group name LOGICAL ERROR
  $_selectOldManufacturerId = Db::getInstance()->getValue('SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` WHERE `name` = "' . pSQL($value) . '"');

  $update_Manufacturer = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'manufacturer` SET `name` = "' . pSQL($_replaceToNewManufacturer) . '" WHERE `id_manufacturer` = "' . pSQL($_selectOldManufacturerId) . '"');

  return $update_Manufacturer;
}

//Replace Features value
function getReplaceFeaturesValue($value)
{
  $_replaceToValue = Db::getInstance()->getValue('SELECT `replace_to`
    FROM `' . _DB_PREFIX_ . 'import_replace`
    WHERE `replace_from` = "' . pSQL($value) . '" AND `active` = "1"
    ORDER BY `id_import_replace` ASC');

  if ($_replaceToValue) {
//Get ID old features group name UPDATE `bandom_ps17`.`ps_feature_value_lang` SET `value` = '1920 X 1200' WHERE `id_feature_value` = 3581 AND `id_lang` = 1
    $_replaceOldFeaturesValuesIds = Db::getInstance()->executeS('SELECT `id_feature_value` FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE `value` = "' . pSQL($value) . '"');

    foreach ($_replaceOldFeaturesValuesIds as $featuresId) {
      $update_ValueLang = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'feature_value_lang` SET `value` = "' . pSQL(html_entity_decode($_replaceToValue)) . '" WHERE `id_feature_value` = "' . pSQL($featuresId['id_feature_value']) . '" AND `id_lang` = "' . Configuration::get('PS_LANG_DEFAULT') . '"');
    }

    return $_replaceToValue;
  } else {
    return getReplaceFrom($value);
  }
}

//Replace Features group name
function getReplaceFeaturesGroupName($value)
{
  $sql = Db::getInstance()->getValue('SELECT `replace_to`
    FROM `' . _DB_PREFIX_ . 'import_replace`
    WHERE `replace_from` = "' . pSQL($value) . '" AND `active` = "1"
    ORDER BY `id_import_replace` ASC');
  $_replaceTo = trim(mb_strtoupper($sql, 'UTF-8'));

//Get ID old features group name
  $_replaceOldFeaturesGruopId = Db::getInstance()->getValue('SELECT `id_feature`
    FROM `' . _DB_PREFIX_ . 'feature_lang`
    WHERE `name` = "' . pSQL($value) . '"');

  $update_lang = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'feature_lang` SET `name` = "' . pSQL($_replaceTo) . '" WHERE `id_feature` = "' . pSQL($_replaceOldFeaturesGruopId) . '" AND `name` = "' . pSQL($value) . '" AND `id_lang` = "1"');

if ($_replaceTo) {
    return $_replaceTo;
  } else {
    return $value;
  }
}

//Get features
function getAlsoFeatures($value, $id)
{

//Also description html file
  $also_html = file_get_html('http://cby.alsoactebis.com/cop/product/' . $value . '/' . $id . '/datasheet.do');

  foreach ($also_html->find('table.properties .mspec') as $item) {
    if (empty($item->find('td.name', 0)->plaintext) or $item->find('td.name', 0)->plaintext == "Product Description" or $item->find('td.name', 0)->plaintext == "Product Type" or $item->find('td.name', 0)->plaintext == "Weight") {
      continue;
    }

    $product['name'] = getReplaceFeaturesGroupName(trim($item->find('td.name', 0)->plaintext));
    $product['value'] = getReplaceFeaturesValue(trim($item->find('td.value', 0)->plaintext));
    $products[] = $product;
  }

//Memory leak!
  $also_html->clear();
  unset($also_html);

  return $products;
}

function getAlsoImage($value, $id)
{

//Also description html file
  $also_html = file_get_html('http://cby.alsoactebis.com/cop/product/' . $value . '/' . $id . '/datasheet.do');
  //Image
  $image = $also_html->find('td.image img', 0)->src;

  //Memory leak!
  $also_html->clear();
  unset($also_html);

  return $image;
}

function getAlsoDescription($value, $id)
{

//Also description html file
  $also_html = file_get_html('http://cby.alsoactebis.com/cop/product/' . $value . '/' . $id . '/datasheet.do');

  //Description
  $description = $also_html->find('tr.featuresText td', 0)->innertext;

  //Memory leak!
  $also_html->clear();
  unset($also_html);

  return $description;
}
