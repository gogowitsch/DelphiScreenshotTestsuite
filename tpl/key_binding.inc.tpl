{* Schnellwahltasten für index.tpl und details.tpl *}
ignoreShortcuts ();

{*Beim eingeben der E-Mail Tastenkürzel ignorieren*}
$(":text, textarea").focus(function () {
    $(document).unbind('keydown');
});

$(":text, textarea").focusout(function() {
    ignoreShortcuts ();
});

function ignoreShortcuts () {
    $(document).bind('keydown', 'a', function (event) {
        if (event.keyCode == 65 && !event.ctrlKey) {
            $('#done-button').click();
        }
    });
    $(document).bind('keydown', 'b', function (event) {
        if (event.keyCode == 66 && !event.ctrlKey) {
            $('#doneAll-button').click();
        }
    });
    $(document).bind('keydown', 'c', function (event) {
        if (event.keyCode == 67 && !event.ctrlKey) {
            $('#discard-button').click();
        }
    });
    $(document).bind('keydown', 'd', function (event) {
        if (event.keyCode == 68 && !event.ctrlKey) {
            $('#soll_no_longer_needed-button').click();
        }
    });
}

{* Submit Form mit CTRL + Enter *}
$('#textarea').keydown(function (e) {
    if (e.ctrlKey && e.keyCode == 13) {
        $('#submit_button').click();
    }
});