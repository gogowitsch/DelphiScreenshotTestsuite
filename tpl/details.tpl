{include file="header.tpl" title=Details}

<div id="breadcrumbs">
    {if $Sprache=='de'}
        <a href='/'>Projektübersicht</a> &#187;
    {else}
        <a href='/'>Project Overview</a> &#187;
    {/if}

    <a href='/?project={$project|urlencode}'>{$project}</a> &#187;
    {$aTest.title}
</div>

{function showDifferences}
    <div style='background:{$color}'><span class='label'>   {$label}:</span>
        {if $aTest.ext=='png' || $aTest.ext=='bmp'}
        <img src="{$file}?{$sTime|urlencode}" title="{$label} {$time}">
        {else}
            {if $aTest.ext=='txt' || $aTest.ext=='rtf'}
                {if file_exists($file)}
                    <div class='iframe_container'>
                    {$aTest.sRtfLink}
                      <textarea rows=21 cols=75 readonly="readonly">{fetch file=$file}</textarea>

                    </div>
                {/if}
            {else}
                <div class='iframe_container'><iframe src="{$file}?{$sTime|urlencode}" title={$label}></iframe></div>
            {/if}
        {/if}
    </div>
{/function}

<b style='color:red'>{$aTest.title}: {$aTest.desc}</b>
{if $Sprache=='de'}
    {showDifferences color=red file=$aTest.fileIst label=Ist time=$aTest.istTime}
{else}
    {showDifferences color=red file=$aTest.fileIst label=Actual time=$aTest.istTime}
{/if}

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
    {if $Sprache=='de'}
        {showDifferences color=green file=$aTest.fileSoll label=Soll time=$aTest.sollTime}
    {else}
        {showDifferences color=green file=$aTest.fileSoll label=Target time=$aTest.sollTime}
    {/if}
  {if $aTest.ext=='png' || $aTest.ext=='bmp'}
      {if $Sprache=='de'}
        <span class='label'>Unterschiede: </span>
    {else}
        <span class='label'>Differences: </span>
    {/if}
    <div style='position:relative;display:inline-block;background-image:url({$aTest.fileIst}?{$sTime|urlencode})'>
      <img src='compare.php?sTestName={$aTest.name|urlencode}'  style='opacity:0.95' title=Unterschiede >
    </div>
  {/if}
  {if $aTest.ext=='txt' || $aTest.ext=='rtf' || $aTest.ext=='ini' || $aTest.ext=='lmo'}
      {if $Sprache=='de'}
        <span class='label'>Unterschiede: </span>
    {else}
        <span class='label'>Differences: </span>
    {/if}
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
    {if $Sprache=='de'}
       Soll-Datei existiert noch nicht.
   {else}
       Currently no Actual state file
   {/if}
{/if}
<br>

<div class='buttons' style='z-index:22'>
    <button id="done-button" onclick="location.href = 'done.php?done={$aTest.name|urlencode}&project={$project|urlencode}';">
        {if $Sprache=='de'}
            Ist-Zustand als neuen Sollwert abspeichern
        {else}
            Save actual state as new target state
        {/if}
    </button>
    <button id="done-button" onclick="location.href = 'done.php?doneAll={$aTest.name|urlencode}&project={$project|urlencode}';">
        {if $Sprache=='de'}
            Ist-Zustand als neuen Sollwert für alle gleichen Unterschiede abspeichern
        {else}
            Save actual state as new target state for all equal differences
        {/if}
    </button>
    <button id="discard-button" onclick="if (confirm('M&ouml;chten Sie dieses Testergebnis (Ist-Zustand) wirklich l&ouml;schen?')) location.href = 'discard.php?discard={$aTest.name|urlencode}&project={$project|urlencode}';" style='opacity:0.9'>
        {if $Sprache=='de'}
            Ist-Zustand verwerfen
        {else}
            Discard actual state
        {/if}
  </button>
  {if file_exists($aTest.fileSoll)}
  <div id="soll_no_longer_needed-wrap">
    <button id="soll_no_longer_needed-button" onclick="if (confirm('M&ouml;chten Sie den Soll-Zustand wirklich l&ouml;schen? Das macht Sinn, wenn die neuste EXE keine Ist-Zust&auml;nde mit diesem Namen mehr produziert, oder der Sollzustand falsch ist.')) location.href = 'soll_no_longer_needed.php?soll_no_longer_needed={$aTest.name|urlencode}&project={$project|urlencode}';"      >
        {if $Sprache=='de'}
            Soll-Zustand verwerfen
        {else}
            Discard target state
        {/if}
    </button>
  </div>
  {/if}
</div>

{include file="footer.tpl"}
