{*
* 2007-2015 PrestaShop
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
* Do not edit or add to this file if you wish to upgrade PrestaShop to newersend_friend_form_content
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="idTab5" class="tab-pane">
	<div id="product_comments_block_tab">
		{if $comments}
			{if (!$too_early AND ($is_logged OR $allow_guests))}
				<p class="align_center open-comment-block">
					<a id="new_comment_tab_btn" class="btn btn-default open-comment-form" href="#new_comment_form" style="background-color:#bf9958">
						<span>{l s='Make a review' mod='productcomments'}</span>
					</a>
				</p>

				<div class="new_comment_form_outer" style="display: none;">
					<div id="new_comment_form">
						<form id="id_new_comment_form" action="#">
							<!-- <h2 class="page-subheading">
								{l s='Write a review' mod='productcomments'}
							</h2> -->
							<div class="row">
								<!-- {if isset($product) && $product}
									<div class="product clearfix  col-xs-12 col-sm-6">
										<img src="{$productcomment_cover_image}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product->name|escape:'html':'UTF-8'}" />
										<div class="product_desc">
											<p class="product_name">
												<strong>{$product->name}</strong>
											</p>
											{$product->description_short}
										</div>
									</div>
								{/if} -->
								<div class="new_comment_form_content col-xs-12 col-sm-12">
									<div id="new_comment_form_error" class="error" style="display: none;background-color: #cd5d5d;color: #ffffff;font-size: 13px;padding-left: 10px;font-family: 'PT Serif', serif;font-weight:400;">
										<ul></ul>
									</div>
									<label for="comment_title">
										{l s='Title:' mod='productcomments'} <sup class="required">*</sup>
									</label>
									<input id="comment_title" name="title" type="text" value=""/>
									{if $criterions|@count > 0}
										<ul id="criterions_list">
										{foreach from=$criterions item='criterion'}
											<li>
												<label>{$criterion.name|escape:'html':'UTF-8'}:</label>
												<div class="star_content">
													<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="1" />
													<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="2" />
													<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="3" />
													<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="4" checked="checked" />
													<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="5" />
												</div>
												<div class="clearfix"></div>
											</li>
										{/foreach}
										</ul>
									{/if}
									<label for="content">
										{l s='Description:' mod='productcomments'} <sup class="required">*</sup>
									</label>
									<textarea id="content" name="content"></textarea>
									{if $allow_guests == true && !$is_logged}
										<label>
											{l s='Your name:' mod='productcomments'} <sup class="required">*</sup>
										</label>
										<input id="commentCustomerName" name="customer_name" type="text" value=""/>
									{/if}
									<div id="new_comment_form_footer">
										<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}' />
										<!-- <p class="fl required"><sup>*</sup> {l s='Required fields' mod='productcomments'}</p> -->
										<p class="fr review_submit_div">
											<button id="submitNewMessage" name="submitMessage" type="submit" class="btn btn-default">
												<span>{l s='Make Review' mod='productcomments'}</span>
											</button>&nbsp;
											<!-- {l s='or' mod='productcomments'}&nbsp; -->
											<button id="cancelreview" name="cancelreview" type="submit" class="btn btn-default">
												<span>{l s='Cancel' mod='productcomments'}</span>
											</button>
										</p>
										<div class="clearfix"></div>
									</div> <!-- #new_comment_form_footer -->
								</div>
							</div>
						</form><!-- /end new_comment_form_content -->
					</div>
				</div>


			{/if}
			<hr>
			{foreach from=$comments item=comment}
				{if $comment.content}
					<div class="reviews_blogs">
						<div class="name_person">
							<span>{$comment.customer_name|escape:'html':'UTF-8'}</span>
							<span class="comment_date"><em>{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em></span>
						</div>
						<div class="review_container">
							<div class="review_title">
								<p>{$comment.title}</p>
							</div>
							<div class="star_content clearfix"  itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
								{section name="i" start=0 loop=5 step=1}
									{if $comment.grade le $smarty.section.i.index}
										<div class="star"></div>
									{else}
										<div class="star star_on"></div>
									{/if}
								{/section}
		        				<meta itemprop="worstRating" content = "0" />
								<meta itemprop="ratingValue" content = "{$comment.grade|escape:'html':'UTF-8'}" />
		        				<meta itemprop="bestRating" content = "5" />
							</div>
							<div class="review_content">
								<p itemprop="reviewBody">{$comment.content|escape:'html':'UTF-8'|nl2br}</p>
									<ul>
										{if $comment.total_advice > 0}
											<li>
												{l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='productcomments'}
											</li>
										{/if}
										{if $is_logged}
											{if !$comment.customer_advice}
											<li>
												{l s='Was this comment useful to you?' mod='productcomments'}
												<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="1" data-id-product-comment="{$comment.id_product_comment}">
													<span>{l s='Yes' mod='productcomments'}</span>
												</button>
												<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="0" data-id-product-comment="{$comment.id_product_comment}">
													<span>{l s='No' mod='productcomments'}</span>
												</button>
											</li>
											{/if}
											{if !$comment.customer_report}
											<li>
												<span class="report_btn" data-id-product-comment="{$comment.id_product_comment}">
													{l s='Report abuse' mod='productcomments'}
												</span>
											</li>
											{/if}
										{/if}
									</ul>
								</p><!-- .comment_details -->
							</div>
						</div>
					</div>
				{/if}
			{/foreach}
		{else}
			{if (!$too_early AND ($is_logged OR $allow_guests))}
			<p class="align_center">
				<a id="new_comment_tab_btn" class="btn btn-default open-comment-form" href="#new_comment_form">
					<span>{l s='Be the first to write your review!' mod='productcomments'}</span>
				</a>
			</p>
			<div class="new_comment_form_outer" style="display: none;">
				<div id="new_comment_form">
					<form id="id_new_comment_form" action="#">
						<!-- <h2 class="page-subheading">
							{l s='Write a review' mod='productcomments'}
						</h2> -->
						<div class="row">
							<!-- {if isset($product) && $product}
								<div class="product clearfix  col-xs-12 col-sm-6">
									<img src="{$productcomment_cover_image}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product->name|escape:'html':'UTF-8'}" />
									<div class="product_desc">
										<p class="product_name">
											<strong>{$product->name}</strong>
										</p>
										{$product->description_short}
									</div>
								</div>
							{/if} -->
							<div class="new_comment_form_content col-xs-12 col-sm-12">
								<div id="new_comment_form_error" class="error" style="display: none;background-color: #cd5d5d;color: #ffffff;font-size: 13px;padding-left: 10px;font-family: 'PT Serif', serif;font-weight:400;">
									<ul></ul>
								</div>
								<label for="comment_title">
									{l s='Title:' mod='productcomments'} <sup class="required">*</sup>
								</label>
								<input id="comment_title" name="title" type="text" value=""/>
								{if $criterions|@count > 0}
									<ul id="criterions_list">
									{foreach from=$criterions item='criterion'}
										<li>
											<label>{$criterion.name|escape:'html':'UTF-8'}:</label>
											<div class="star_content">
												<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="1" />
												<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="2" />
												<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="3" />
												<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="4" checked="checked" />
												<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="5" />
											</div>
											<div class="clearfix"></div>
										</li>
									{/foreach}
									</ul>
								{/if}
								<label for="content">
									{l s='Description:' mod='productcomments'} <sup class="required">*</sup>
								</label>
								<textarea id="content" name="content"></textarea>
								{if $allow_guests == true && !$is_logged}
									<label>
										{l s='Your name:' mod='productcomments'} <sup class="required">*</sup>
									</label>
									<input id="commentCustomerName" name="customer_name" type="text" value=""/>
								{/if}
								<div id="new_comment_form_footer">
									<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}' />
									<!-- <p class="fl required"><sup>*</sup> {l s='Required fields' mod='productcomments'}</p> -->
									<p class="fr review_submit_div">
										<button id="submitNewMessage" name="submitMessage" type="submit" class="btn btn-default">
											<span>{l s='Make Review' mod='productcomments'}</span>
										</button>&nbsp;
										<!-- {l s='or' mod='productcomments'}&nbsp; -->
										<button id="cancelreview" name="cancelreview" type="submit" class="btn btn-default">
											<span>{l s='Cancel' mod='productcomments'}</span>
										</button>
									</p>
									<div class="clearfix"></div>
								</div> <!-- #new_comment_form_footer -->
							</div>
						</div>
					</form><!-- /end new_comment_form_content -->
				</div>
			</div>
			{else}
			<p class="align_center no_reviews_cond_block">{l s='No customer reviews for the moment.' mod='productcomments'}</p>
			{/if}
		{/if}
	</div> <!-- #product_comments_block_tab -->
</div>
{strip}
{addJsDef productcomments_controller_url=$productcomments_controller_url|@addcslashes:'\''}
{addJsDef moderation_active=$moderation_active|boolval}
{addJsDef productcomments_url_rewrite=$productcomments_url_rewriting_activated|boolval}
{addJsDef secure_key=$secure_key}

{addJsDefL name=confirm_report_message}{l s='Are you sure that you want to report this comment?' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_added}{l s='Your comment has been added!' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_added_moderation}{l s='Your comment has been added and will be available once approved by a moderator.' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_title}{l s='New comment' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_ok}{l s='OK' mod='productcomments' js=1}{/addJsDefL}
{/strip}
<style type="text/css">
	.open-comment-form
	{
		background-color:#BF9958;
		color:#ffffff!important;
		font-size:18px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		padding-top: 10px;
		padding-right: 20px;
		padding-bottom: 10px;
		padding-left: 20px;
	}
	.no_reviews_cond_block
	{
		olor: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
	}
	.name_person
	{
		padding: 10px;
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:700;
	}
	.review_title
	{
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:700;
	}
	.review_content
	{
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		line-height: 23px;
		padding-top: 10px;
	}
	.review_container
	{
		padding:20px;
		border: 1px solid #cccccc;
	}
	.reviews_blogs
	{
		margin-bottom: 25px;
	}
	.comment_date
	{
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		color: #9A9A9A;
		margin-left: 10px;
	}
	.review_submit_div
	{
		float: left!important;
	}
	.cancel, .star
	{
		width: 25px;
		font-size: 21px;
		height: 21px;

	}

	.review_container
	{
		border: 1px solid #cccccc;
	    padding: 20px;
	    position: relative;
	}
	.review_container:after, .review_container:before
	{
		border: medium solid transparent;
	    bottom: 100%;
	    content: " ";
	    height: 0;
	    left: 7%;
	    pointer-events: none;
	    position: absolute;
	    width: 0;
	}

	.review_container:after
	{
		border-color: rgba(136, 183, 213, 0) rgba(136, 183, 213, 0) #ffffff;
	    border-width: 9px;
	    margin-left: -22px;
	}
	.review_container:before
	{
		border-color: rgba(245, 233, 171, 0) rgba(245, 233, 171, 0) #cccccc;
	    border-width: 11px;
	    margin-left: -24px;
	}

</style>
