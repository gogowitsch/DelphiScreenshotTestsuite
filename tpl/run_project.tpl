{if $Sprache=='de'}
    {$ScreN="Screenshots neu erstellen"}
{else}
    {$ScreN="Create new screenshots"}
{/if}

{if $aProject.cmd}
    <form method="post"
          action="run_project.php?project={$aProject.title|urlencode}&run=1" class="run_project">

        <button type="submit"
                title="Startet {$aProject.cmd|escape}">
            Create new screenshots
        </button>
        <label for="email">wenn fertig, E-Mail an:</label>
        <input type="text" name="email" id="email" value="{$sEmail}" placeholder="(optional)">
    </form>
{/if}
