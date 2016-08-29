{* zeigt Ergebnisse eines einzigen Projektes *}

{if $Sprache=='de'}
    {$Proj="Projektübersicht"}
    {$Running="Test läuft noch, gestartet am"}
    {$Finished="Test abgeschlossen"}
    {$Erf="erfolgreich"}
    {$AllM="Alle markieren"}
    {$IstZu="Ist-Zustand als neuen Sollwert abspeichern"}
    {$ZurVer="Ist-Zustand verwerfen"}
    {$MoeSi="M&ouml;chten Sie die Testergebnisse (Ist-Zustand) wirklich l&ouml;schen?"}
    {$DaIsSi="Das ist sinnvoll, wenn es verwaist ist, also nicht mehr generiert wird."}
    {$ShowProcess="Laufende Tests anzeigen"}
    {$ShowAll="auch 100 % erfolgreiche Projekte zeigen"}
    {$InQueue="In der Warteschlange"}
    {$green="grün"}
    {$yellow="gelb"}
{else}
    {$Proj="Project Overview"}
    {$Running="Test in progress, initiated on"}
    {$Finished="Test finished"}
    {$Erf="successful"}
    {$AllM="Select all"}
    {$IstZu="Save actual state as new target state"}
    {$ZurVer="Discard actual state"}
    {$MoeSi="Do you really want to remove the selected test results (Actual state)?"}
    {$DaIsSi="That is useful when the test results don\'t get generated anymore."}
    {$ShowProcess="Show running process"}
    {$ShowAll="show 100 % successful projects as well"}
    {$InQueue="Queued"}
    {$green="green"}
    {$yellow="yellow"}
{/if}



{if $ini}
    {include file="status.tpl"}
{else}
    {strip}
    {if empty($project)}{$project = "Start"}{/if}
    {include file="header.tpl" title=$project}

    <div id="breadcrumbs">
        <a href='.'>
            {$Proj}
        </a> &#187;&nbsp;
        {$project}
    </div>

    {if !empty($started)}
        <span> {$Running} {$started} </span>
    {elseif !empty($queued)}
        <span> {$InQueue} </span>
    {else}
        <span> {$Finished} </span>
    {/if}

    <span style='background-color: #99ff99'>{$iStatusSum} ({$iPercentage} %) {$Erf}</span><br>

    {include file="run_project.tpl" aProject=$aProjects.0}

    <form method="post" action="#">
        <table>
            {$nSuccess=0}
            {$nWouldBeSuccess=0}
            {foreach $aTests as $i => $aTest}
                {if $aTest.ext == 'bmp' || $aTest.ext == 'pdf'}
                    {continue}
                {/if}
                {if !$show_all && $aTest.status == 1}
                    {$nSuccess++}
                    {continue}
                {/if}
                {if !$show_all && $aTest.iWouldBeStatus == 1}
                    {$nWouldBeSuccess++}
                    {continue}
                {/if}
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
            {/foreach}
        </table>
        <a id='check_all' href="javascript:check_all(true)">{$AllM}</a><br>

        <div id="actions">
        <input type=submit name=done title="als Okay markieren" id='done-button' value="A: {$IstZu}" />
        <input type=submit name=discard value="C: {$ZurVer}"  id='discard-button' onclick="return confirm('{$MoeSi}\n\n{$DaIsSi}')"  />
    </form>
    <!-- NEUE GITLAB-URLS BEI include/smarty.inc.php ANLEGEN!!! --!>
    {if isset($newGitLabIssueURL)}
    <form id="new-issue" data-url="{$newGitLabIssueURL}">
        <br>
        <fieldset>
            <legend>GitLab</legend>
            <input id="issue-title" placeholder="Titel">
            <input type="submit" value="Issue anlegen">
        </fieldset>
    </form>
    {/if}
        </div>


    <script>
        {include file="key_binding.inc.tpl"}

        function check_all(bValue) {
            $('input[type=checkbox]').attr('checked', bValue);
            $('#actions').show();
        }
        var $chkboxes = null;
        var lastChecked = null;
        function checkbox_click(e) {
            if (!lastChecked) {
                lastChecked = this;
                return;
            }

            if (e.shiftKey) {
                var start = $chkboxes.index(this);
                var end = $chkboxes.index(lastChecked);
                $chkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).attr('checked', lastChecked.checked);

            }

            lastChecked = this;
        }
        function showhide_submit() {
            $('#actions').hide();
            $('input[type=checkbox]').each(function () {
                if ($(this).is(':checked')) {
                    $('#actions').show();
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
        $("#new-issue").submit(function(e) {
            e.preventDefault();

            var title = $('#issue-title').val();

            var description = $("tr").map(function() {
                if ($(this).find(':checked').length)
                  return '- ' + $(this).find('a').prop('href');
            }).get().join('\n');

            window.open($(this).data('url')
              + '?issue[title]=' + encodeURIComponent(title)
              + '&issue[description]=' + encodeURIComponent(description) )
        });
    </script>
    {if !$show_all}
        <a href="?project={$project|urlencode}&show_all=1">
            {$ShowAll} ({$nSuccess} {$green}, {$nWouldBeSuccess} {$yellow})</a>
    {/if}

    {include file="footer.tpl"}
{/strip}
{/if}
