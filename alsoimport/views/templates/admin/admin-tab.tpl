    <div class="form-group">
         <input type="hidden" name="submitted_tabs[]" value="Informations" />
    <h3 class="tab"> <i class="icon-info"></i> {l s='Skip update row on run cron ALSO import'}</h3>

    {if isset($display_common_field) && $display_common_field}
    <div class="alert alert-warning" style="display: block">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
    {/if}
 
        <div class="col-12">
            <div class="row" style="margin-top: 2rem">
                <div class="col-lg-9">
				<div class="checkbox">
                        <label for="skipname">
                            <input type="checkbox" name="skipname" id="skipname" value="1" {if $also_field['skipname']}checked="checked"{/if} >
                            {l s='Skip update name'}</label>
                    </div>
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
