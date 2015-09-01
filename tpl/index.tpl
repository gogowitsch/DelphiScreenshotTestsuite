{* zeigt Ergebnisse eines einzigen Projektes *}

{if $Sprache=='de'}
    {$Proj="Projektübersicht"}
    {$Erf="erfolgreich."}
    {$AllM="Alle markieren"}
    {$IstZu="Ist-Zustand als neuen Sollwert abspeichern"}
    {$ZurVer="Ist-Zustand verwerfen"}
    {$MoeSi="M&ouml;chten Sie die Testergebnisse (Ist-Zustand) wirklich l&ouml;schen?"}
    {$DaIsSi="Das ist sinnvoll, wenn es verwaist ist, also nicht mehr generiert wird."}
{else}
    {$Proj="Project Overview"}
    {$Erf="successful."}
    {$AllM="Select all"}
    {$IstZu="Save actual state as new target state"}
    {$ZurVer="Discard actual state"}
    {$MoeSi="Do you really want to remove the selected test results (Actual state)?"}
    {$DaIsSi="That is useful when the test results don\'t get generated anymore."}
{/if}



﻿{if $ini}
    {include file="status.tpl"}
{else}
    {strip}
        {include file="header.tpl" title=Start}
        <div id="breadcrumbs">
            <a href='.'>
                {$Proj}
            </a> &#187;&nbsp;
            {$project}
        </div>
        {$iStatusSum} / {$aTests|count} {$Erf}

        {include file="run_project.tpl" aProject=$aProjects.0}

        <form method="post" action="#">
            <table>
                {foreach $aTests as $i => $aTest}
                    {if ($show_all || $aTest.status==0) && $aTest.ext != 'bmp' && $aTest.ext != 'pdf'}
                        <tr>
                            <td>
                                {if $aTest.status==0}
                                    {* die folgende Checkbox wird von screenshot.inc.php in handleActions() ausgewertet *}
                                    <input id="cb{$i}" type=checkbox name="check[]" value="{$aTest.name|urlencode|htmlentities}" />
                                {/if}
                            </td>
                            <td>
                                <label for="cb{$i}">{$aTest.title|utf8_encode}</label>
                            </td>
                            <td class="status{$aTest.status} wouldbe{$aTest.iWouldBeStatus}">
                                <a href="details.php?project={$project|urlencode}&sTestName={$aTest.name|urlencode}">
                                    {$aTest.desc}
                                </a>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </table>
            <a id='check_all' href="javascript:check_all(true)">{$AllM}</a><br>

            <input type=submit name=done title="als Okay markieren" id='done-button' value="A: {$IstZu}" />
            <input type=submit name=discard value="C: {$ZurVer}"  id='discard-button' onclick="return confirm('{$MoeSi}\n\n{$DaIsSi}')"  />

        </form>
        <script>
            {include file="key_binding.inc.tpl"}

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
            showhide_submit();
        </script>
        {if !$show_all}<a href="?project={$project|urlencode}&show_all=1">auch erfolgreiche Tests zeigen</a>{/if}

        {include file="footer.tpl"}
    {/strip}
{/if}
