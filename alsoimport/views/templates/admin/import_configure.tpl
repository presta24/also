{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="product-informations" class="panel product-tab">
    <input type="hidden" name="submitted_tabs[]" value="Informations" />
    <h3 class="tab"> <i class="icon-info"></i> {l s='Skip update row on run cron ALSO import'}</h3>

    {if isset($display_common_field) && $display_common_field}
    <div class="alert alert-warning" style="display: block">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
    {/if}


    {* status informations *}


    <div id="product_options" class="form-group">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-1">
                    <span class="pull-right">
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipstatus" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipdescr" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipimage" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipcat" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipstock" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipprice" type="default"}
                    {include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="skipseo" type="default"}
                    </span>
                </div>

                <div class="col-lg-9">
                    <div class="checkbox">
                        <label for="skipstatus">
                            <input type="checkbox" name="skipstatus" id="skipstatus" value="1" {if $also_field['skipstatus']}checked="checked"{/if} >
                            {l s='Skip update status'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipdescr">
                            <input type="checkbox" name="skipdescr" id="skipdescr" value="1" {if $also_field['skipdescr']}checked="checked"{/if} >
                            {l s='Skip update description'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipimage">
                            <input type="checkbox" name="skipimage" id="skipimage" value="1" {if $also_field['skipimage']}checked="checked"{/if} >
                            {l s='Skip update image'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipcat">
                            <input type="checkbox" name="skipcat" id="skipcat" value="1" {if $also_field['skipcat']}checked="checked"{/if} >
                            {l s='Skip update default category'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipstock">
                            <input type="checkbox" name="skipstock" id="skipstock" value="1" {if $also_field['skipstock']}checked="checked"{/if} >
                            {l s='Skip update stock'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipprice">
                            <input type="checkbox" name="skipprice" id="skipprice" value="1" {if $also_field['skipprice']}checked="checked"{/if} >
                            {l s='Skip update price'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="skipseo">
                            <input type="checkbox" name="skipseo" id="skipseo" value="1" {if $also_field['skipseo']}checked="checked"{/if} >
                            {l s='Skip update seo'}</label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel-footer">
        <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
        <button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
        <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
    </div>
</div>
    </div>
