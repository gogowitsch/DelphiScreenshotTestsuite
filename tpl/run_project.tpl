{if $Sprache=='de'}
    {$ScreN="Screenshots neu erstellen"}
{else}
    {$ScreN="Create new screenshots"}
{/if}




{if $aProject.cmd}
  {* assign "sFrage" "Möchtest du wirklich alle Ist-Zustäde  verwerfen und neu erstellen?" *}
  {assign "sFrage" ""}
  &nbsp;
  <a
    onclick="return confirm('Möchtest du wirklich alle Ist-Zustäde von {$aProject.title} verwerfen und neu erstellen?')"
    href="run_project.php?project={$aProject.title|urlencode}&run=1"

    title="Startet {$aProject.cmd|escape}">{$ScreN}</a>

{/if}
