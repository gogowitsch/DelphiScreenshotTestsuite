{if $Sprache=='de'}
    {$Proj="Projektübersicht"}
    {$IstAcu="Ist"}
    {$SollTar="Soll"}
    {$UntDif="Unterschiede"}
    {$SollD="Soll-Datei existiert noch nicht"}
    {$IstZ="Ist-Zustand als neuen Sollwert abspeichern"}
    {$GleiU="Ist-Zustand als neuen Sollwert für alle gleichen Unterschiede abspeichern"}
    {$ZuVer="Ist-Zustand verwerfen"}
    {$SoVer="Soll-Zustand verwerfen"}
{else}
    {$Proj="Project Overview"}
    {$IstAcu="Actual"}
    {$SollTar="Target"}
    {$UntDif="Differences"}
    {$SollD="Currently no Target state file"}
    {$IstZ="Save actual state as new target state"}
    {$GleiU="Save actual state as new target state for all equal differences"}
    {$ZuVer="Discard actual state"}
    {$SoVer="Discard target state"}
{/if}



{include file="header.tpl" title=Details}

<div id="breadcrumbs">

        <a href='/'>{$Proj}</a> &#187;

    <a href='/?project={$project|urlencode}'>{$project}</a> &#187;
    {$aTest.title}
</div>

{function showDifferences}
    <div style='background:{$color}'><span class='label'>   {$label}:</span>
        {if $aTest.ext=='png' || $aTest.ext=='bmp'}
            <img src="{$file}?{$sTime|urlencode}" title="{$label} {$time}">
        {else}
            {if $aTest.ext=='txt' || $aTest.ext=='rtf' || $aTest.ext=='csv'}
                {if file_exists($file)}
                    <div class='iframe_container'>
                        {$aTest.sRtfLink|default}
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
{showDifferences color=red file=$aTest.fileIst|utf8_encode label=$IstAcu time=$aTest.istTime}


{literal}
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script>
        $(function() {
            $( ".iframe_container" ).resizable({
              helper: "ui-resizable-helper"
            });

            $(document).bind('keydown', 'a', function(event) {
                if(event.keyCode == 65) {
                    $('#done-button').click();
                }
            });

            $(document).bind('keydown', 'b', function(event) {
                if(event.keyCode == 66) {
                    $('#doneAll-button').click();
                }
            });

            $(document).bind('keydown', 'c', function(event) {
                if(event.keyCode == 67) {
                    $('#discard-button').click();
                }
            });

            $(document).bind('keydown', 'd', function(event) {
                if(event.keyCode == 68) {
                    $('#soll_no_longer_needed-button').click();
                }
            });
        });
    </script>
{/literal}
{if file_exists($aTest.fileSoll)}

  {showDifferences color=green file=$aTest.fileSoll|utf8_encode label={$SollTar} time=$aTest.sollTime}

  {if $aTest.ext=='png' || $aTest.ext=='bmp'}

          <span class='label'>{$UntDif}: </span>

    <div style='position:relative;display:inline-block;background-image:url("{$aTest.fileIst|escape}?{$sTime|urlencode}")'>
      <img src='compare.php?sTestName={$aTest.name|urlencode}'  style='opacity:0.95' title=Unterschiede >
    </div>
  {/if}
  {if $aTest.ext=='txt' || $aTest.ext=='rtf' || $aTest.ext=='ini' || $aTest.ext=='lmo' || $aTest.ext=='csv'}
      {* von diesen Dateitypen soll ein FineDiff angezeigt werden *}
      <span class='label'>{$UntDif}: </span>

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
    {$SollD}
{/if}
<br>

<div class='buttons' style='z-index:22'>
    <button id="done-button" onclick="location.href = 'done.php?done={$aTest.name|urlencode}&project={$project|urlencode}';">
        A: {$IstZ}
    </button>
    {if $aTest.ext=='png' && file_exists($aTest.fileSoll)}
        <button id="done-button-alternative" title="speichert den Ist-Wert als Sollwert-Alternative" onclick="location.href = 'done.php?done={$aTest.name|urlencode}&project={$project|urlencode}&alternative=1';">
            *
        </button>
        <button id="doneAll-button" onclick="location.href = 'done.php?doneAll={$aTest.name|urlencode}&project={$project|urlencode}';">
            B: {$GleiU}
        </button>
    {/if}
    <button id="discard-button" onclick="if (confirm('M&ouml;chten Sie dieses Testergebnis (Ist-Zustand) wirklich l&ouml;schen?')) location.href = 'discard.php?discard={$aTest.name|urlencode}&project={$project|urlencode}';" style='opacity:0.9'>
        C: {$ZuVer}
    </button>
  {if file_exists($aTest.fileSoll)}
    <div id="soll_no_longer_needed-wrap">
        <button id="soll_no_longer_needed-button" onclick="if (confirm('M&ouml;chten Sie den Soll-Zustand wirklich l&ouml;schen? Das macht Sinn, wenn die neuste EXE keine Ist-Zust&auml;nde mit diesem Namen mehr produziert, oder der Sollzustand falsch ist.')) location.href = 'soll_no_longer_needed.php?soll_no_longer_needed={$aTest.name|urlencode}&project={$project|urlencode}';"      >
            D: {$SoVer}
        </button>
    </div>
  {/if}
</div>

{include file="footer.tpl"}
