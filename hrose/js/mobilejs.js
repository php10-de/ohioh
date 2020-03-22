$(document).ready(function(){
    $('#switch_optimal').on('change', function(){
        var val = $(this).val();
        var disabled = $('input#slope').add($('input#direction'))
        if (val == "on") {
            disabled.removeAttr('disabled');
        } else if (val == "off") {
            disabled.attr("disabled", "disabled");
        }
    })
    
});