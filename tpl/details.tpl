{include file="header.tpl" title=Details}

<b style='color:red'>Es gibt Unterschiede in {$aTest.title}</b>
<div style='background:red'>   Ist: <img src='{$aTest.fileIst}?$sTime' title=Ist></div>
<div style='background:green'>   Soll: <img src='{$aTest.fileSoll}?$sTime' title=Soll></div>
Unterschiede: <img src='compare.php?sTestName={$aTest.name|urlencode}' title=Unterschiede> <br>
<button onclick="location.href = 'index.php?done={$aTest.name|urlencode}';">
    Ist-Zustand als neuen Sollwert abspeichern
</button>

{include file="footer.tpl"}
