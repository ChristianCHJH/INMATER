
jQuery(window).load(function (event) {
    jQuery('.loader').hide();
});
function findText() {
    var input, filter, table, tr, td, i;
    input = document.getElementById("find-input");
    filter = input.value.toUpperCase();
    table = document.getElementById("table_main");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        var encontro = false;
        for (var j = 0; j < 10; j++) {
            td = tr[i].getElementsByTagName("td")[j];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    encontro = true; break;
                }
            }
        }
        if (encontro) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}