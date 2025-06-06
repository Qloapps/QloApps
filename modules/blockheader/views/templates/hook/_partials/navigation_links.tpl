{* Partial for rendering navigation links *}
{if isset($custom_navigation_links) && $custom_navigation_links|@count > 0}
    <ul class="bh-main-nav-ul {if isset($mobile_nav_class) && $mobile_nav_class}bh-mobile-nav-list{/if}">
        {foreach $custom_navigation_links as $nav_link}
            {if $nav_link.active && !empty($nav_link.text) && !empty($nav_link.url)}
                <li>
                    <a href="{$nav_link.url|escape:'html':'UTF-8'}" class="bh-nav-item {if isset($nav_link.current) && $nav_link.current}active{/if}">
                        {$nav_link.text|escape:'html':'UTF-8'}
                    </a>
                </li>
            {/if}
        {/foreach}
    </ul>
{/if}
