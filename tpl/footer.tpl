{* wird am Ende jeder Seite eingebunden *}

{if $Sprache=='de'}
    {$MehrIn="Mehr Infos zu diesem Tool gibt es im"}
    {$QDde="QD-Wiki unter dem Stichwort DelphiScreenshotTestsuite"}
{else}
    {$MehrIn="For more information about this tool click here"}
    {$QDde="QD-Wiki DelphiScreenshotTestsuite"}
{/if}

{if isset($iframeFurtherImageConversions)}
  <iframe src='convert_images.php' style='height:25px;width:90%;border:none'></iframe>
{/if}
<hr>
<i style='font-size:10px'>{$MehrIn} <a href="https://wiki.quodata.de/index.php?title=DelphiScreenshotTestsuite">{$QDde}</a>.</BODY>

</HTML>
