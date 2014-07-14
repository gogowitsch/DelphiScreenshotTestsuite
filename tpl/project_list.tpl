{* wird von index.php aufgerufen *}
{if $ini}
    {include file="status.tpl"}
{else}
    {strip}
        {include file="header.tpl" title=Start}

        Tests: {$iStatusSum} / {$aTests|count} erfolgreich.

        <table>
            {foreach $aProjects as $i => $aProject}
                {if $show_all || $aProject.status==0}
                    <tr>
                        <td class="status{$aProject.status}">
                            <a href="index.php?project={$aProject.title|urlencode}">{$aProject.title}</a>
                        </td>
                        <td class="status{$aProject.status} ratio">
                            {$aProject.ratio} erfolgreich
                            {include file="run_project.tpl" aProject=$aProject}
                        </td>
                    </tr>
                {else}
                    {assign var="bHasHiddenProjects" value="1"}
                {/if}
            {/foreach}
        </table>
        {if !$show_all && $bHasHiddenProjects}<a href="?show_all=1">auch 100 % erfolgreiche Projekte zeigen</a>{/if}
        {include file="footer.tpl"}
    {/strip}
{/if}