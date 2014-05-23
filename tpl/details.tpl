{include file="header.tpl" title=Details}

<div id="breadcrumbs">
    <a href='/'>Projektübersicht</a> &#187;
    <a href='/?project={$project|urlencode}'>{$project}</a> &#187;
    {$aTest.title}
</div>


<b style='color:red'>Es gibt Unterschiede in {$aTest.title}</b>
<div style='background:red'><span class='label'>   Ist:</span>
    {if $aTest.ext=='png'}
    <img src='{$aTest.fileIst}?{$sTime|urlencode}' title=Ist>
    {else}

    <div class='iframe_container'><iframe src='{$aTest.fileIst}?{$sTime|urlencode}' title=Ist></iframe></div>
    {/if}
</div>
<div style='background:green'><span class='label'>   Soll:</span>
    {if $aTest.ext=='png'}
    <img src='{$aTest.fileSoll}?{$sTime|urlencode}' title=Soll>
    {else}
    <div class='iframe_container'><iframe src='{$aTest.fileSoll}?{$sTime|urlencode}' title=Soll></iframe></div>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script>
    $(function() {
        $( ".iframe_container" ).resizable({
          helper: "ui-resizable-helper"
        });
    });
    </script>
    {/if}
</div>
{if $aTest.ext=='png'}
    <span class='label'>Unterschiede: </span>
    <img src='compare.php?sTestName={$aTest.name|urlencode}' title=Unterschiede>
{/if}
<br>

<button onclick="location.href = 'index.php?done={$aTest.name|urlencode}';">
    Ist-Zustand als neuen Sollwert abspeichern
</button>
<button onclick="if (confirm('M&ouml;chten Sie dieses Testergebnis (Ist-Zustand) wirklich l&ouml;schen?')) location.href = 'index.php?discard={$aTest.name|urlencode}';" style='opacity:0.9'>
    Ist-Zustand verwerfen
</button>

{include file="footer.tpl"}
