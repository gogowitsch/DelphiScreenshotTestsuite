{* wird von index.php aufgerufen, zeigt Übersicht über alle Projekte *}
{if $Sprache=='de'}
    {$Erf="erfolgreich"}
    {$ShowProcess="Laufende Tests anzeigen"}
    {$ShowAll="auch 100 % erfolgreiche Projekte zeigen"}
    {$sAddSubscriber = "Abonnent hinzu"}
    {$sTestUpToDate = "Die Testergebnisse beziehen sich auf das neuste Release"}
    {$sTestOutdated = "Es gibt ein neues Release, das noch nicht getestet wurde - der Test ist veraltet"}
{else}
    {$Erf="successful"}
    {$ShowProcess="Show running process"}
    {$ShowAll="show 100 % successful projects as well"}
    {$sAddSubscriber = "Add subscriber"}
    {$sTestUpToDate = "Test results cover the most recent release - they are up-to-date"}
    {$sTestOutdated = "Test finished before most recent than release and is thus outdated"}
{/if}

{if $ini}
    {include file="status.tpl"}
{else}
    {strip}
        {include file="header.tpl" title=Start}

        Tests: {$iStatusSum} / {$aTests|count} {$Erf}.
        {if isset($iframeFurtherImageConversions)}
            <iframe src='convert_images.php' style='overflow: hidden; height:1em;width:300px;border:none'></iframe>
        {/if}

        <table>
            <thead>
              <th>Title</th>
              <th>Ratio</th>
              <th>Duration</th>
              <th>Last run</th>
              <th></th>
              <th>Subscribers</th>
            </thead>
            <tbody>
            {foreach $aProjects as $i => $aProject}
                {if $show_all || $aProject.status==0}
                    <tr>
                        <td class="status{$aProject.status}">
                            <a href="index.php?project={$aProject.title|urlencode}">{$aProject.title}</a>
                        </td>
                        <td class="status{$aProject.status} ratio">
                            {$aProject.ratio} erfolgreich
                        </td>
                        <td class="status{$aProject.status}">
                            {$aProject.duration}
                        </td>
                        {if $aProject.last_run}
                            <td class="test-alter{$aProject.last_run > $aProject.exe_time}" 
                                data-exe_path="{$aProject.exe_path}" 
                                data-exe_time="{$aProject.exe_time|date_format:'%Y-%m-%d %T'}">
                                {$aProject.last_run|date_format:"%Y-%m-%d %T"}
                            </td>
                        {else}
                            <td></td>
                        {/if}
                        <td>
                            {include file="run_project.tpl" sFormTarget="form_target_$i"}
                        </td>
                        <td class='subscribers status{$aProject.status}'>

                            <a href="/phpmyadmin/sql.php?db=delphiscreenshottestsuite&amp;table=subscribers"
                               title="Abonenntenliste in phpMyAdmin bearbeiten.">
                                {foreach $aProject.subscribers as $key => $subscriber}
                                    {if $key > 0}, {/if}
                                    {$subscriber.email|stripDomainIfQuoData}
                                {foreachelse}
                                    + {$sAddSubscriber}
                                {/foreach}
                            </a>
                        </td>
                    </tr>
                {else}
                    {assign var="bHasHiddenProjects" value="1"}
                {/if}
            {/foreach}
            </tbody>
        </table>
        <script>
            function setLastRunTitle(sSelector, sTemplate) {
                var el = $(sSelector);
                el.attr('title', sTemplate + '\nRelease: ' + el.data('exe_time') + ', ' + el.data('exe_path'));
            }
            setLastRunTitle('.test-alter1', '{$sTestUpToDate}');
            setLastRunTitle('.test-alter', '{$sTestOutdated}');
        </script>
        {if !$show_all && $bHasHiddenProjects}<a href="?show_all=1">{$ShowAll}</a><br><br>{/if}
        <iframe src='show_running_process.php' style='overflow: hidden; height:1.5em;width:100%;border:none'></iframe>
        {include file="footer.tpl"}
    {/strip}
{/if}
