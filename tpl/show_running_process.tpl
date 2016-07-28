<form method="post" action="index.php">
    {if $bProcessRunning}
        <div style="background-color: #99ff99" id="show_running_pocces">
            Running process: {$sCurrentProcess} {$iFileTime}
            <input name="killJobs" type="submit" value="Stop"></input> |
            <a target=phpMyAdmin href="/phpmyadmin/sql.php?server=1&db=delphiscreenshottestsuite&table=job_warteschlange">
                View Job Queue in phpMyAdmin </a>
        </div>
    {else}
        <div style="background-color: #ff9999" id="no_running_pocces">
            There is no running process.
        </div>
    {/if}
</form>

<script>
    setTimeout(function () {
        window.location.reload(1);
    }, 3000);
</script>

<style>
form, body {
  margin:0;
}
</style>
