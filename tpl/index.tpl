{if $ini}
    {include file="status.tpl"}
{else}
    {strip}
        {include file="header.tpl" title=Start}
        <div id="breadcrumbs">
            <a href='/'>
                {if $Sprache=='de'}
                    Projektübersicht
                {else}
                    Project Overview
                {/if}
            </a> &#187;&nbsp;
            {$project}
		</div>

            {if $Sprache=='de'}
                {$iStatusSum} / {$aTests|count} erfolgreich.
            {else}
                {$iStatusSum} / {$aTests|count} successful.
            {/if}
        {include file="run_project.tpl" aProject=$aProjects.0}

        <form method="post" action="#">
            <table>
                {foreach $aTests as $i => $aTest}
                    {if $show_all || $aTest.status==0}
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
                                <a href="details.php?project={$project|urlencode}&sTestName={$aTest.name|urlencode}">
                                    {$aTest.desc}
                                </a>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </table>
                {if $Sprache=='de'}
                    <a id='check_all' href="javascript:check_all(true)">alle markieren</a><br>
                {else}
                    <a id='check_all' href="javascript:check_all(true)">Select all</a><br>
                {/if}

                {if $Sprache=='de'}
                    <input type=submit name=done title="als Okay markieren" value="Ist-Zustand als neuen Sollwert abspeichern" />
                {else}
                    <input type=submit name=done title="als Okay markieren" value="Save actual state as new target state" />
                {/if}

                {if $Sprache=='de'}
                    <input type=submit name=discard value="Ist-Zustand verwerfen"  onclick="return confirm('M&ouml;chten Sie die Testergebnisse (Ist-Zustand) wirklich l&ouml;schen?\n\nDas ist sinnvoll, wenn es verwaist ist, also nicht mehr automatisch generiert wird.')"  />
                {else}
                    <input type=submit name=discard value="Discard actual state"  onclick="return confirm('Do you really want to remove the selected test results (Actual state)?\n\nThat is useful when the test results don\'t get automatically generated anymore.')"  />
                {/if}
        </form>
        <script>
            {literal}
                function check_all(bValue) {
                    $('input[type=checkbox]').attr('checked', bValue);
                    $('input[type=submit]').show();
                }
                var $chkboxes = null;
                var lastChecked = null;
                function checkbox_click(e) {
                    if(!lastChecked) {
                        lastChecked = this;
                        return;
                    }

                    if (e.shiftKey) {
                        var start = $chkboxes.index(this);
                        var end = $chkboxes.index(lastChecked);
                        $chkboxes.slice(Math.min(start,end), Math.max(start,end) + 1).attr('checked', lastChecked.checked);

                    }

                    lastChecked = this;
                }
                function showhide_submit() {
                    $('input[type=submit]').hide();
                    $('input[type=checkbox]').each(function() {
                        if ($(this).is(':checked')) {
                            $('input[type=submit]').show();
                        }
                    });
                }
                $(function () {
                    $chkboxes = $('input[type=checkbox]');
                    $chkboxes.click(checkbox_click).change(showhide_submit);
                    var iFailedTests = $chkboxes.length;
                    if (!iFailedTests)
                        $('#check_all').hide();
                });
            {/literal}
                showhide_submit();
        </script>
        {if !$show_all}<a href="?project={$project|urlencode}&show_all=1">auch erfolgreiche Tests zeigen</a>{/if}

        {include file="footer.tpl"}
    {/strip}
{/if}