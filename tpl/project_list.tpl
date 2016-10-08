{* wird von index.php aufgerufen, zeigt Übersicht über alle Projekte *}
{if $Sprache=='de'}
    {$Erf="erfolgreich"}
    {$ShowProcess="Laufende Tests anzeigen"}
    {$ShowAll="auch 100 % erfolgreiche Projekte zeigen"}
    {$sAddSubscriber = "Abonnent hinzu"}
{else}
    {$Erf="successful"}
    {$ShowProcess="Show running process"}
    {$ShowAll="show 100 % successful projects as well"}
    {$sAddSubscriber = "Add subscriber"}
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
                        <td>
                            {include file="run_project.tpl" sFormTarget="form_target_$i"}
                        </td>
                        <td class='subscribers status{$aProject.status}'>

                            <a href="/phpmyadmin/sql.php?db=delphiscreenshottestsuite&table=subscribers"
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
        </table>
        {if !$show_all && $bHasHiddenProjects}<a href="?show_all=1">{$ShowAll}</a><br><br>{/if}
        <iframe src='show_running_process.php' style='overflow: hidden; height:1.5em;width:100%;border:none'></iframe>
        {include file="footer.tpl"}
    {/strip}
{/if}
