{if $smarty.get.ini}
    {include file="status.tpl"}
{else}
    {strip}
        {include file="header.tpl" title=Start}

        {$iStatusSum} / {$aTests|count} erfolgreich.

        <form method="post" action="#">
            <table>
                {foreach $aTests as $i => $aTest}
                    {if $smarty.get.show_all || $aTest.status==0}
                        <tr>
                            <td>
                                {if $aTest.status==0}
                                    <input id="cb{$i}" type=checkbox name="check[]" value="{$aTest.name|escape}" />
                                {/if}
                            </td>
                            <td>
                                <label for="cb{$i}">{$aTest.title}</label>
                            </td>
                            <td class="status{$aTest.status}">
                                <a href="details.php?sTestName={$aTest.name|urlencode}">
                                    {$aTest.desc}
                                </a>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </table>
            <a id='check_all' href="javascript:check_all(true)">alle markieren</a> <input type=submit value="als Okay markieren" />
        </form>
        <script>
            {literal}
                function check_all(bValue) {
                    $('input[type=checkbox]').attr('checked', bValue);
                    $('input[type=submit]').show();
                }
                function showhide_submit() {
                    $('input[type=submit]').hide();
                    $('input[type=checkbox]').each(function() {
                        if ($(this).is(':checked')) {
                            $('input[type=submit]').show();
                        }
                    });
                }
                var iFailedTests = $('input[type=checkbox]').change(showhide_submit).length;
                if (!iFailedTests)
                    $('#check_all').hide();
            {/literal}
                showhide_submit();
        </script>
        {if !$smarty.get.show_all}<a href="?show_all=1">auch erfolgreiche Tests zeigen</a>{/if}

        {include file="footer.tpl"}
    {/strip}
{/if}