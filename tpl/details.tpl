{include file="header.tpl" title=Details}

<div id="breadcrumbs">
    <a href='/'>Projektübersicht</a> &#187;
    <a href='/?project={$project|urlencode}'>{$project}</a> &#187;
    {$aTest.title}
</div>

{function showDifferences}
    <div style='background:{$color}'><span class='label'>   {$label}:</span>
        {if $aTest.ext=='png' || $aTest.ext=='bmp'}
        <img src="{$file}?{$sTime|urlencode}" title="{$label} {$time}">
        {else}
            {if $aTest.ext=='txt'}
                {if file_exists($file)}<div class='iframe_container'><textarea rows=21 cols=75 readonly="readonly">{fetch file=$file}</textarea></div>{/if}
            {else}
            <div class='iframe_container'><iframe src="{$file}?{$sTime|urlencode}" title={$label}></iframe></div>
            {/if}
        {/if}
    </div>
{/function}

<b style='color:red'>{$aTest.title}: {$aTest.desc}</b>
{showDifferences color=red file=$aTest.fileIst label=Ist time=$aTest.istTime}

{literal}
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script>
        $(function() {
            $( ".iframe_container" ).resizable({
              helper: "ui-resizable-helper"
            });
        });
    </script>
{/literal}
{if file_exists($aTest.fileSoll)}
  {showDifferences color=green file=$aTest.fileSoll label=Soll time=$aTest.sollTime}
  {if $aTest.ext=='png' || $aTest.ext=='bmp'}
    <span class='label'>Unterschiede: </span>
    <div style='position:relative;display:inline-block;background-image:url({$aTest.fileIst}?{$sTime|urlencode})'>
      <img src='compare.php?sTestName={$aTest.name|urlencode}'  style='opacity:0.95' title=Unterschiede >
    </div>
  {/if}
  {if $aTest.ext=='txt' || $aTest.ext=='rtf' || $aTest.ext=='ini' || $aTest.ext=='lmo'}
    <span class='label'>Unterschiede: </span>
    {php}
        global $aTest;
        include_once('../include/finediff.inc.php');
        $sIst = join('', file($aTest['fileIst']));
        $sSoll = join('', file($aTest['fileSoll']));
        $diff = new FineDiff($sSoll, $sIst);
        echo "<pre>" . $diff->renderDiffToHTML() . "</pre>";
    {/php}
  {/if}
{else}
   Soll-Datei existiert noch nicht.
{/if}
<br>

<div class='buttons' style='z-index:22'>
  <button id="done-button" onclick="location.href = 'done.php?done={$aTest.name|urlencode}&project={$project|urlencode}';">
      Ist-Zustand als neuen Sollwert abspeichern
  </button>
  <button id="discard-button" onclick="if (confirm('M&ouml;chten Sie dieses Testergebnis (Ist-Zustand) wirklich l&ouml;schen?')) location.href = 'discard.php?discard={$aTest.name|urlencode}&project={$project|urlencode}';" style='opacity:0.9'>
      Ist-Zustand verwerfen
  </button>
</div>

{include file="footer.tpl"}
