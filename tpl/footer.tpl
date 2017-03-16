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
<script>
    {if !empty($smarty.get.l)}
        function addLanguageParameterToAllPages(sAttribute) {
            $('[' + sAttribute + ']').each(function() {
                href = $(this).attr(sAttribute);
                $(this).attr(sAttribute, href + (href.indexOf('?') >= 0 ? '&' : '?') + 'l={$smarty.get.l}');
            });
        }
        addLanguageParameterToAllPages('href');
        addLanguageParameterToAllPages('action');
    {/if}

    $('button[href]').click(function() {
      location.href = this.getAttribute('href');
    });
</script>
<i style='font-size:10px'>{$MehrIn} <a href="https://wiki.quodata.de/index.php?title=DelphiScreenshotTestsuite">{$QDde}</a>.</BODY>

</HTML>
