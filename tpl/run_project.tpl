{if $Sprache=='de'}
    {$ScreN="Screenshots neu erstellen"}
{else}
    {$ScreN="Create new screenshots"}
{/if}

{if $aProject.cmd}
    <form method="post"
          {*action="run_project.php?project={$aProject.title|urlencode}&run=1"*}>
        <label for="email">Please enter your email address (optional):</label>
        <input type="text" name="email" id="email" {*value="{$sEmail}"*}>

        <button type="submit"
                name="action"
                title="Startet {$aProject.cmd|escape}">
            Create new screenshots</button>
    </form>

    {if $bEmail}
        <script>
            $(document).ready(function () {
                window.location='run_project.php?project={$aProject.title|urlencode}&run=1';
            });
        </script>
    {/if}
{/if}
