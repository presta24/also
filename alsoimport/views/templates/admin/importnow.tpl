{*
* 2007-2018 PrestaShop
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
*}

<div class="panel">
    <h3><i class="icon icon-credit-card"></i> {l s='Import Now' mod='alsoimport'} - {l s='If need update product list without cron!' mod='alsoimport'}</h3>

    <div id="importNowFields" class="row" style="text-align:center;">
      <a href="{$baseURI|escape:'htmlall':'UTF-8'}modules/alsoimport/AlsoImportUpdate.php?token=PUT_YOUR_TOKEN_HERE&updateId={$obj->id_alsoimport}" target="_blank" class="btn btn-primary btn-lg">{l s='IMPORT NOW' mod='alsoimport'}</a>

    </div>
  <div class="modal fade" id="importNow" tabindex="-1" role="dialog" aria-labelledby="importNowLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="importNowLabel">{l s='IMPORT NOW' mod='alsoimport'}</h4>
      </div>
      <div class="modal-body">

      <progress class="progress progress-primary" value="50" max="100">50%</progress>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-tertiary-outline btn-lg" data-dismiss="modal">{l s='Close' mod='alsoimport'}</button>
      </div>
    </div>
  </div>
</div>
</div>

