{include file="header.tpl" title=Details}

<div id="breadcrumbs">
    <a href='/'>Projektübersicht</a> &#187;
    <a href='/?project={$project|urlencode}'>{$project}</a> &#187;
    {$aTest.title}
</div>


<b style='color:red'>Es gibt Unterschiede in {$aTest.title}</b>
<div style='background:red'><span class='label'>   Ist:</span>
    {if $aTest.ext=='png' || $aTest.ext=='bmp'}
    <img src="{$aTest.fileIst}?{$sTime|urlencode}" title=Ist>
    {else}
    {if $aTest.ext=='txt'}
    {if file_exists($aTest.fileIst)}<div class='iframe_container'><textarea rows=21 cols=75 readonly="readonly">{fetch file=$aTest.fileIst}</textarea></div>{/if}
    {else}

    <div class='iframe_container'><iframe src="{$aTest.fileIst}?{$sTime|urlencode}" title=Ist></iframe></div>
    {/if}
	{/if}
</div>
<div style='background:green'><span class='label'>   Soll:</span>
    {if $aTest.ext=='png' || $aTest.ext=='bmp'}
    <img src="{$aTest.fileSoll}?{$sTime|urlencode}" title=Soll>
    {else}
    {if $aTest.ext=='txt'}
    {if file_exists($aTest.fileSoll)}<div class='iframe_container'><textarea rows=21 cols=75 readonly="readonly">{fetch file=$aTest.fileSoll}</textarea></div>{/if}
    {else}
      <div class='iframe_container'><iframe src="{$aTest.fileSoll}?{$sTime|urlencode}" title=Soll></iframe></div>
      <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
      <script>
      $(function() {
          $( ".iframe_container" ).resizable({
            helper: "ui-resizable-helper"
          });
      });
      </script>
    {/if}
    {/if}
</div>
{if $aTest.ext=='png' || $aTest.ext=='bmp'}
    <span class='label'>Unterschiede: </span>
    <img src='compare.php?sTestName={$aTest.name|urlencode}' title=Unterschiede>
{/if}
<br>

<button id="done-button" onclick="location.href = 'done.php?done={$aTest.name|urlencode}&project={$project|urlencode}';">
    Ist-Zustand als neuen Sollwert abspeichern
</button>
<button id="discard-button" onclick="if (confirm('M&ouml;chten Sie dieses Testergebnis (Ist-Zustand) wirklich l&ouml;schen?')) location.href = 'discard.php?discard={$aTest.name|urlencode}&project={$project|urlencode}';" style='opacity:0.9'>
    Ist-Zustand verwerfen
</button>

{include file="footer.tpl"}
