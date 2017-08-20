{if $Sprache=='de'}
    {if empty($started)}
        {$ScreN="Screenshots neu erstellen"}
    {else}
        {$ScreN="Weiteren E-Mail-Empfänger hinzufügen"}
    {/if}
{else}
    {if empty($started)}
        {$ScreN="Create new screenshots"}
    {else}
        {$ScreN="Add subscriber"}
    {/if}
{/if}

{if empty($started)}
    {$verb="run"}
{else}
    {$verb="add_subscriber"}
{/if}

{if $aProject.cmd}
    <form method="post"
    {if !empty($sFormTarget)}
          target="{$sFormTarget}"
    {/if}
          action="run_project.php?project={$aProject.title|urlencode}&amp;run=1"
          class="{$verb}_project">

        <button type="submit"
                title="Startet {$aProject.cmd|escape}">
            {$ScreN}
        </button>
        <label for="email">wenn fertig, E-Mail an:</label>
        <input type="text" name="email" id="email" value="{$sEmail}" placeholder="(optional)">
    {if !empty($sFormTarget)}
        <iframe name="{$sFormTarget}" style="display:none"></iframe>
    {/if}
    </form>
{/if}
