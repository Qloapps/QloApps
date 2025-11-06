{capture name='_smarty_debug' assign=debug_output}
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Smarty Debug Console</title>
        <style>
            {literal}
            body, h1, h2, h3, td, th, p {
                font-family: sans-serif;
                font-weight: normal;
                font-size: 0.9em;
                margin: 1px;
                padding: 0;
            }

            h1 {
                margin: 0;
                text-align: left;
                padding: 2px;
                background-color: #f0c040;
                color: black;
                font-weight: bold;
                font-size: 1.2em;
            }

            h2 {
                background-color: #9B410E;
                color: white;
                text-align: left;
                font-weight: bold;
                padding: 2px;
                border-top: 1px solid black;
            }

            h3 {
                text-align: left;
                font-weight: bold;
                color: black;
                font-size: 0.7em;
                padding: 2px;
            }

            body {
                background: black;
            }

            p, table, div {
                background: #f0ead8;
            }

            p {
                margin: 0;
                font-style: italic;
                text-align: center;
            }

            table {
                width: 100%;
            }

            th, td {
                font-family: monospace;
                vertical-align: top;
                text-align: left;
            }

            td {
                color: green;
            }

            tr:nth-child(odd) {
                background-color: #eeeeee;
            }

            tr:nth-child(even) {
                background-color: #fafafa;
            }

            .exectime {
                font-size: 0.8em;
                font-style: italic;
            }

            #bold div {
                color: black;
                font-weight: bold;
            }

            #blue h3 {
                color: blue;
            }

            #normal div {
                color: black;
                font-weight: normal;
            }

            #table_assigned_vars th {
                color: blue;
                font-weight: bold;
            }

            #table_config_vars th {
                color: maroon;
            }
            {/literal}
        </style>
    </head>
    <body>

    <h1>Smarty {Smarty::SMARTY_VERSION} Debug Console
        -  {if isset($template_name)}{$template_name|debug_print_var nofilter} {/if}{if !empty($template_data)}Total Time {$execution_time|string_format:"%.5f"}{/if}</h1>

    {if !empty($template_data)}
        <h2>included templates &amp; config files (load time in seconds)</h2>
        <div>
            {foreach $template_data as $template}
                <span style="color: brown;">{$template.name}</span>
                <br>&nbsp;&nbsp;<span class="exectime">
                (compile {$template['compile_time']|string_format:"%.5f"}) (render {$template['render_time']|string_format:"%.5f"}) (cache {$template['cache_time']|string_format:"%.5f"})
                 </span>
                <br>
            {/foreach}
        </div>
    {/if}

    <h2>assigned template variables</h2>

    <table id="table_assigned_vars">
        {foreach $assigned_vars as $vars}
            <tr>
                <td>
                    <h3 style="color: blue;">${$vars@key}</h3>
                    {if isset($vars['nocache'])}<strong>Nocache</strong><br>{/if}
                    {if isset($vars['scope'])}<strong>Origin:</strong> {$vars['scope']|debug_print_var nofilter}{/if}
                </td>
                <td>
                    <h3>Value</h3>
                    {$vars['value']|debug_print_var:10:80 nofilter}
                </td>
                <td>
                    {if isset($vars['attributes'])}
                        <h3>Attributes</h3>
                        {$vars['attributes']|debug_print_var nofilter}
                    {/if}
                </td>
         {/foreach}
    </table>

    <h2>assigned config file variables</h2>

    <table id="table_config_vars">
        {foreach $config_vars as $vars}
            <tr>
                <td>
                    <h3 style="color: blue;">#{$vars@key}#</h3>
                    {if isset($vars['scope'])}<strong>Origin:</strong> {$vars['scope']|debug_print_var nofilter}{/if}
                </td>
                <td>
                    {$vars['value']|debug_print_var:10:80 nofilter}
                </td>
            </tr>
        {/foreach}

    </table>
    </body>
    </html>
{/capture}
<script type="text/javascript">
    _smarty_console = window.open("", "console{$targetWindow}", "width=1024,height=600,left={$offset},top={$offset},resizable,scrollbars=yes");
    _smarty_console.document.write("{$debug_output|escape:'javascript' nofilter}");
    _smarty_console.document.close();
</script>
