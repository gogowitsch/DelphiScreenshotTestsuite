{* Schnellwahltasten für index.tpl und details.tpl *}

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
