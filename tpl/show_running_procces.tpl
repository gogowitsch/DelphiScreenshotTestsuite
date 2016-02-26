<form method="post" action="index.php">
    {if $bProccesRunning}
        <div style="background-color: #99ff99" id="show_running_pocces">
            <p>Running procces: {$sCurrentProcces} {$iFileTime}</p>
            <input name="killJobs" type="submit" value="Stop running procces"></input>
        </div><br>
        <a href = 'index.php'>Go back to Project-Overview</a>
    {else}
        <div style="background-color: #ff9999" id="no_running_pocces">
            <p>There is no running procces.</p>
            <a href='index.php'>Go back to Project-Overview</a>
        </div>
    {/if}
</form>

{include file="footer.tpl"}

<script>
    setTimeout(function () {
        window.location.reload(1);
    }, 3000);
</script>