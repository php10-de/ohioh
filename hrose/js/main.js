function formatDEToEN(fieldId) {
    var fieldVal = $('#'+fieldId).val();
    fieldVal = fieldVal.replace('.','');
    fieldVal = fieldVal.replace(',','.');
    fieldVal = parseFloat(fieldVal);
    return fieldVal;
}

function formatFloat(val) {
    val = Math.round(val*100)/100;
    val = val.toString();
    val = val.replace('.', ',');
    return val;
}

function setLimit(l) {
    limit = l;
    $('.limit').css('font-weight','normal')
    $('#limit_'+l).css('font-weight','bold');
    updateList();
}