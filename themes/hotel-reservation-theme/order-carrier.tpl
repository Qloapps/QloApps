<div class="box" id="tc_cont">
    <p class="checkbox">
        <input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
        <label for="cgv" id="tc_txt">{l s='I agree to the terms of service and will adhere to them unconditionally.'}</label>
        <a id="tc_link" href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow" >{l s='(Read the Terms of Service)'}</a>
    </p>
</div>