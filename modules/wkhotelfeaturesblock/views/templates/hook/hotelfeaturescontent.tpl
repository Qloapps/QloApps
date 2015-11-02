<hr style="height:1px;background-color:#999">
<div id="features_block" class="row">
  <p class="hotel_feature_heading">
    {if isset($main_blog_data.blog_heading)}{$main_blog_data.blog_heading}{/if}
  </p>
  <p class="hotel_feature_content">
    {if isset($main_blog_data.blog_description)}{$main_blog_data.blog_description}{/if}
  </p>
  <div class="features_container">
    {if isset($features_data) && $features_data} 
      {foreach $features_data as $data}
        <div class="col-sm-4 single_feature_container">
          <div>
            {if isset($data.feature_image) && $data.feature_image}
              <img src="{$module_dir}views/img/{$data.feature_image}">
            {else}
              <img src="{$module_dir}views/img/default.jpg">
            {/if}
          </div>
          <div class="feature_head">{$data.feature_title}</div>
          <div class="feature_content">{$data.feature_description}</div>
        </div>
      {/foreach}
    {/if}  
  </div>
</div>