{* wird von index.php aufgerufen, zeigt Übersicht über alle Projekte *}
{if $Sprache=='de'}
    {$Erf="erfolgreich"}
    {$ShowProcces="Laufende Tests anzeigen"}
    {$ShowAll="auch 100 % erfolgreiche Projekte zeigen"}
{else}
    {$Erf="successful"}
    {$ShowProcces="Show running procces"}
    {$ShowAll="show 100 % successful projects as well"}
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
                        <td>
                            {include file="run_project.tpl" aProject=$aProject}
                        </td>
                    </tr>
                {else}
                    {assign var="bHasHiddenProjects" value="1"}
                {/if}
            {/foreach}
        </table>
        {if !$show_all && $bHasHiddenProjects}<a href="?show_all=1">{$ShowAll}</a><br><br>{/if}
        <a href="show_running_procces.php">{$ShowProcces}</a>
        {include file="footer.tpl"}
    {/strip}
{/if}