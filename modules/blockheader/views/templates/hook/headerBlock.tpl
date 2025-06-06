{* Block Header Template *}

<header id="custom_header_block" class="custom-header">
    {* Desktop Header *}
    <div class="bh-desktop-header">
        <div class="bh-top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 bh-top-left">
                        {if !empty($BH_DESKTOP_LINK1_TEXT) && !empty($BH_DESKTOP_LINK1_URL)}
                            <a href="{$BH_DESKTOP_LINK1_URL|escape:'html':'UTF-8'}" class="bh-top-link">{$BH_DESKTOP_LINK1_TEXT|escape:'html':'UTF-8'}</a>
                        {/if}
                        {if !empty($BH_DESKTOP_LINK1_TEXT) && !empty($BH_DESKTOP_LINK1_URL) && !empty($BH_DESKTOP_LINK2_TEXT) && !empty($BH_DESKTOP_LINK2_URL)}
                            <span class="bh-separator">|</span>
                        {/if}
                        {if !empty($BH_DESKTOP_LINK2_TEXT) && !empty($BH_DESKTOP_LINK2_URL)}
                            <a href="{$BH_DESKTOP_LINK2_URL|escape:'html':'UTF-8'}" class="bh-top-link">{$BH_DESKTOP_LINK2_TEXT|escape:'html':'UTF-8'}</a>
                        {/if}
                    </div>
                    <div class="col-md-4 bh-top-center">
                        {if !empty($BH_LOGO)}
                            <img src="{$module_dir|escape:'html':'UTF-8'}views/img/{$BH_LOGO|escape:'html':'UTF-8'}" alt="{$shop.name|escape:'html':'UTF-8'} Logo" class="bh-logo"/>
                        {/if}
                        {if !empty($BH_BRAND_TEXT1)}
                            <div class="bh-brand-text-1">{$BH_BRAND_TEXT1|escape:'html':'UTF-8'}</div>
                        {/if}
                        {if !empty($BH_BRAND_TEXT2)}
                            <div class="bh-brand-text-2">{$BH_BRAND_TEXT2|escape:'html':'UTF-8'}</div>
                        {/if}
                    </div>
                    <div class="col-md-4 bh-top-right">
                        {if !empty($BH_PHONE_NUMBER)}
                            <span class="bh-phone-number">{$BH_PHONE_NUMBER|escape:'html':'UTF-8'}</span>
                        {/if}
                        {* Language selector should be hooked here or handled by the theme *}
                        <div class="bh-language-selector-placeholder">
                            {hook h='displayNav2'} {* Common hook for language/currency in top bar *}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="bh-bottom-bar">
            <div class="container">
                {* Main navigation links - this could be a custom list or a hook for other modules like blocktopmenu *}
                <ul class="bh-main-nav">
                    <li><a href="#">Home</a></li> {* Example Link *}
                    <li><a href="#">Products</a></li> {* Example Link *}
                    <li><a href="#">About Us</a></li> {* Example Link *}
                    {* <!-- Main Navigation Placeholder --> *}
                    {hook h='displayBlockHeaderNavigation'} {* Custom hook for navigation links *}
                </ul>
            </div>
        </nav>

        {if !empty($BH_DESKTOP_CTA_TEXT) && !empty($BH_DESKTOP_CTA_URL)}
            <a href="{$BH_DESKTOP_CTA_URL|escape:'html':'UTF-8'}" class="bh-desktop-cta-button">
                {$BH_DESKTOP_CTA_TEXT|escape:'html':'UTF-8'}
            </a>
        {/if}
    </div>

    {* Mobile Header *}
    <div class="bh-mobile-header">
        <div class="bh-mobile-top-overlay">
            <div class="bh-mobile-branding">
                {if !empty($BH_LOGO)}
                    <img src="{$module_dir|escape:'html':'UTF-8'}views/img/{$BH_LOGO|escape:'html':'UTF-8'}" alt="{$shop.name|escape:'html':'UTF-8'} Logo" class="bh-logo-mobile"/>
                {/if}
                {if !empty($BH_BRAND_TEXT1)}
                    <div class="bh-brand-text-1-mobile">{$BH_BRAND_TEXT1|escape:'html':'UTF-8'}</div>
                {/if}
                {if !empty($BH_BRAND_TEXT2)}
                    <div class="bh-brand-text-2-mobile">{$BH_BRAND_TEXT2|escape:'html':'UTF-8'}</div>
                {/if}
            </div>
            <div class="bh-mobile-language-selector">
                {* Language selector placeholder for mobile *}
                {hook h='displayNav2'} {* Or a mobile specific hook if available/needed *}
            </div>
            <button class="bh-mobile-nav-toggle" aria-label="Toggle navigation">
                <span class="bh-icon-bar"></span>
                <span class="bh-icon-bar"></span>
                <span class="bh-icon-bar"></span>
            </button>
        </div>

        <div class="bh-mobile-nav-panel">
            {* Mobile navigation links - often a duplication of main nav or a simplified version *}
            <ul class="bh-mobile-nav-list">
                <li><a href="#">Home</a></li> {* Example Link *}
                <li><a href="#">Products</a></li> {* Example Link *}
                <li><a href="#">About Us</a></li> {* Example Link *}
                 {hook h='displayBlockHeaderMobileNavigation'} {* Custom hook for mobile navigation links *}
            </ul>
        </div>

        {if !empty($BH_MOBILE_BG_IMAGE)}
        <style>
            .bh-mobile-header {
                background-image: url('{$module_dir|escape:'html':'UTF-8'}views/img/{$BH_MOBILE_BG_IMAGE|escape:'html':'UTF-8'}');
                /* Add other background properties like size, position, repeat as needed in CSS file */
            }
        </style>
        {/if}

        <div class="bh-mobile-bottom-bar">
            {if !empty($BH_MOBILE_LINK1_TEXT) && !empty($BH_MOBILE_LINK1_URL)}
            <a href="{$BH_MOBILE_LINK1_URL|escape:'html':'UTF-8'}" class="bh-mobile-bottom-link">
                {if !empty($BH_MOBILE_LINK1_ICON)}<i class="{$BH_MOBILE_LINK1_ICON|escape:'html':'UTF-8'}"></i>{/if}
                <span>{$BH_MOBILE_LINK1_TEXT|escape:'html':'UTF-8'}</span>
            </a>
            {/if}

            {if !empty($BH_MOBILE_LINK2_TEXT) && !empty($BH_MOBILE_LINK2_URL)}
            <a href="{$BH_MOBILE_LINK2_URL|escape:'html':'UTF-8'}" class="bh-mobile-bottom-link">
                {if !empty($BH_MOBILE_LINK2_ICON)}<i class="{$BH_MOBILE_LINK2_ICON|escape:'html':'UTF-8'}"></i>{/if}
                <span>{$BH_MOBILE_LINK2_TEXT|escape:'html':'UTF-8'}</span>
            </a>
            {/if}

            {if !empty($BH_MOBILE_LINK3_TEXT) && !empty($BH_MOBILE_LINK3_URL)}
            <a href="{$BH_MOBILE_LINK3_URL|escape:'html':'UTF-8'}" class="bh-mobile-bottom-link bh-mobile-cta">
                {if !empty($BH_MOBILE_LINK3_ICON)}<i class="{$BH_MOBILE_LINK3_ICON|escape:'html':'UTF-8'}"></i>{/if}
                <span>{$BH_MOBILE_LINK3_TEXT|escape:'html':'UTF-8'}</span>
            </a>
            {/if}
        </div>
    </div>
</header>
