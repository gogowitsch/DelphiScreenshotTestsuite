<form method="post" action="index.php">
    {if $bProcessRunning}
        <div style="background-color: #99ff99" id="show_running_pocces">
            <p>Running process: {$sCurrentProcess} {$iFileTime}</p>
            <input name="killJobs" type="submit" value="Stop running process"></input>
        </div><br>
        <a href = 'index.php'>Go back to Project-Overview</a>
            <a href="/phpmyadmin/sql.php?server=1&db=delphiscreenshottestsuite&table=job_warteschlange">
                View Job Queue in PhpMyAdmin </a>
    {else}
        <div style="background-color: #ff9999" id="no_running_pocces">
            <p>There is no running process.</p>
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
