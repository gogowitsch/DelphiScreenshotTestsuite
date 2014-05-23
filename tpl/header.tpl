{strip}
<HTML>
    <HEAD>
        <TITLE>{$title} - {$Name}</TITLE>
        <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
        <style>
            body, td { font-family: Arial; }
            .status1 a { text-decoration: none; color:black; }
            .status1 { background: #99ff99 {* gut - grün *} }
            .status0 { background: #ff9999 {* schlecht - rot *} }
            .label { width: 82px; display: inline-block; }
            img {
                vertical-align: top;
            }
            iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
            #breadcrumbs {
              margin: 0 0 1em 0;
            }

            .iframe_container { width: 600px; height: 350px; padding: 0.5em; }
            .ui-resizable-helper { border: 75px solid #EFEFEF; margin: -75px; }
        </style>
        <script src="js/jquery.min.js"></script>
    </HEAD>
    <BODY bgcolor="#ffffff">
{/strip}